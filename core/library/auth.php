<?php

/**
 * Authentication service backed by the user / user_auth / user_meta tables.
 *
 * Local login:
 *   $this->auth->register(['email' => $email, 'password' => $password]);
 *   if ($this->auth->attempt($email, $password, $remember = true)) { ... }
 *
 * Reading the current user (lazy, cached per request):
 *   if ($this->auth->check()) { $id = $this->auth->id(); $u = $this->auth->user(); }
 *
 * OAuth (see google_auth):
 *   $user = $this->auth->findOrCreateFromProvider('google', $googleId, ['email' => $email]);
 *   $this->auth->login($user);
 *
 * All queries touching user input use prepared statements (Db::execute).
 */
class Auth {
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;

    protected $registry;
    protected $user;
    protected $loaded = false;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    // --- Registration & credentials ---------------------------------------

    public function register(array $data) {
        $now = date('Y-m-d H:i:s');

        $password = isset($data['password']) && $data['password'] !== null
            ? password_hash($data['password'], PASSWORD_DEFAULT)
            : null;

        $this->db->execute(
            "INSERT INTO `" . $this->table() . "`
                (uuid, email, username, password, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            array(
                $this->uuid(),
                isset($data['email']) ? $data['email'] : null,
                isset($data['username']) ? $data['username'] : null,
                $password,
                isset($data['status']) ? (int)$data['status'] : self::STATUS_PENDING,
                $now,
                $now
            )
        );

        return $this->find($this->db->last());
    }

    /**
     * Verify credentials (by email or username) and log the user in.
     * Returns false on any mismatch. Status is NOT checked here — the
     * application decides what a pending/banned user may do.
     */
    public function attempt($identifier, $password, $remember = false) {
        $user = $this->findByIdentifier($identifier);

        if (!$user || $user['password'] === null || !password_verify($password, $user['password'])) {
            return false;
        }

        // Transparently upgrade the hash if the algorithm/cost changed
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $this->setPassword($user['user_id'], $password);
        }

        $this->login($user, $remember);

        return true;
    }

    public function setPassword($userId, $plainPassword) {
        $this->db->execute(
            "UPDATE `" . $this->table() . "` SET password = ?, updated_at = ? WHERE user_id = ?",
            array(password_hash($plainPassword, PASSWORD_DEFAULT), date('Y-m-d H:i:s'), (int)$userId)
        );
    }

    // --- Session -----------------------------------------------------------

    public function login($user, $remember = false) {
        $this->session->regenerate();
        $this->session->set('user_id', (int)$user['user_id']);

        $this->user = $user;
        $this->loaded = true;

        $this->db->execute(
            "UPDATE `" . $this->table() . "` SET last_login_at = ? WHERE user_id = ?",
            array(date('Y-m-d H:i:s'), (int)$user['user_id'])
        );

        if ($remember) {
            $this->setRememberToken((int)$user['user_id']);
        }
    }

    public function loginById($userId) {
        $user = $this->find($userId);

        if (!$user) {
            return false;
        }

        $this->login($user);

        return true;
    }

    public function logout() {
        $userId = $this->id();

        if ($userId) {
            $this->db->execute(
                "UPDATE `" . $this->table() . "` SET remember_token = NULL WHERE user_id = ?",
                array($userId)
            );
        }

        $this->session->remove('user_id');
        $this->clearRememberCookie();

        $this->user = null;
        $this->loaded = true;

        $this->session->regenerate();
    }

    public function check() {
        return $this->user() !== null;
    }

    public function guest() {
        return !$this->check();
    }

    public function id() {
        $user = $this->user();

        return $user ? (int)$user['user_id'] : null;
    }

    /**
     * Current user row (or null). Resolved once per request from the session,
     * falling back to a valid "remember me" cookie.
     */
    public function user() {
        if ($this->loaded) {
            return $this->user;
        }

        $this->loaded = true;

        $userId = $this->session->get('user_id');

        if ($userId) {
            $this->user = $this->find((int)$userId);
        } elseif (!empty($this->request->cookie['remember'])) {
            $this->user = $this->userFromRemember($this->request->cookie['remember']);

            if ($this->user) {
                $this->session->regenerate();
                $this->session->set('user_id', (int)$this->user['user_id']);
            }
        }

        return $this->user;
    }

    // --- Finders -----------------------------------------------------------

    public function find($userId) {
        return $this->first(
            "SELECT * FROM `" . $this->table() . "` WHERE user_id = ? LIMIT 1",
            array((int)$userId)
        );
    }

    public function findByEmail($email) {
        return $this->first(
            "SELECT * FROM `" . $this->table() . "` WHERE email = ? LIMIT 1",
            array($email)
        );
    }

    public function findByUsername($username) {
        return $this->first(
            "SELECT * FROM `" . $this->table() . "` WHERE username = ? LIMIT 1",
            array($username)
        );
    }

    protected function findByIdentifier($identifier) {
        return $this->first(
            "SELECT * FROM `" . $this->table() . "` WHERE email = ? OR username = ? LIMIT 1",
            array($identifier, $identifier)
        );
    }

    // --- OAuth providers ---------------------------------------------------

    /**
     * Resolve a provider identity to a user: reuse the linked account,
     * else link to an existing account with the same email, else create one.
     */
    public function findOrCreateFromProvider($provider, $providerUserId, array $attributes = array()) {
        $link = $this->first(
            "SELECT user_id FROM `" . $this->table('user_auth') . "`
             WHERE provider = ? AND provider_user_id = ? LIMIT 1",
            array($provider, $providerUserId)
        );

        if ($link) {
            return $this->find((int)$link['user_id']);
        }

        $user = null;

        if (!empty($attributes['email'])) {
            $user = $this->findByEmail($attributes['email']);
        }

        if (!$user) {
            $user = $this->register(array(
                'email' => isset($attributes['email']) ? $attributes['email'] : null,
                'username' => isset($attributes['username']) ? $attributes['username'] : null,
                'password' => null,
                'status' => self::STATUS_ACTIVE
            ));

            if (!empty($attributes['email'])) {
                $this->markEmailVerified((int)$user['user_id']);
                $user = $this->find((int)$user['user_id']);
            }
        }

        $this->db->execute(
            "INSERT INTO `" . $this->table('user_auth') . "`
                (user_id, provider, provider_user_id, created_at)
             VALUES (?, ?, ?, ?)",
            array((int)$user['user_id'], $provider, $providerUserId, date('Y-m-d H:i:s'))
        );

        return $user;
    }

    public function markEmailVerified($userId) {
        $this->db->execute(
            "UPDATE `" . $this->table() . "`
             SET email_verified_at = ?, status = IF(status = 0, 1, status)
             WHERE user_id = ?",
            array(date('Y-m-d H:i:s'), (int)$userId)
        );
    }

    // --- Meta (application-specific fields) --------------------------------

    public function getMeta($userId, $key, $default = null) {
        $row = $this->first(
            "SELECT meta_value FROM `" . $this->table('user_meta') . "`
             WHERE user_id = ? AND meta_key = ? LIMIT 1",
            array((int)$userId, $key)
        );

        return $row ? $row['meta_value'] : $default;
    }

    public function setMeta($userId, $key, $value) {
        // pass $value twice to avoid the deprecated VALUES() form
        $this->db->execute(
            "INSERT INTO `" . $this->table('user_meta') . "` (user_id, meta_key, meta_value)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE meta_value = ?",
            array((int)$userId, $key, $value, $value)
        );
    }

    public function allMeta($userId) {
        $rows = $this->db->execute(
            "SELECT meta_key, meta_value FROM `" . $this->table('user_meta') . "` WHERE user_id = ?",
            array((int)$userId),
            true
        );

        $meta = array();

        foreach ($rows as $row) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }

        return $meta;
    }

    public function deleteMeta($userId, $key) {
        $this->db->execute(
            "DELETE FROM `" . $this->table('user_meta') . "` WHERE user_id = ? AND meta_key = ?",
            array((int)$userId, $key)
        );
    }

    // --- Internals ---------------------------------------------------------

    protected function table($name = 'user') {
        return $this->db->prefix . $name;
    }

    protected function first($sql, array $params) {
        $rows = $this->db->execute($sql, $params, true);

        return $rows ? $rows[0] : null;
    }

    protected function setRememberToken($userId) {
        $token = bin2hex(random_bytes(32));

        // Only the hash is stored; the raw token lives in the cookie
        $this->db->execute(
            "UPDATE `" . $this->table() . "` SET remember_token = ? WHERE user_id = ?",
            array(hash('sha256', $token), (int)$userId)
        );

        setcookie('remember', $userId . ':' . $token, array(
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'httponly' => true,
            'secure' => defined('SSL') && SSL,
            'samesite' => 'Strict'
        ));
    }

    protected function userFromRemember($cookie) {
        if (strpos($cookie, ':') === false) {
            return null;
        }

        list($userId, $token) = explode(':', $cookie, 2);

        $user = $this->find((int)$userId);

        if (!$user || !$user['remember_token']) {
            return null;
        }

        if (!hash_equals($user['remember_token'], hash('sha256', $token))) {
            return null;
        }

        return $user;
    }

    protected function clearRememberCookie() {
        setcookie('remember', '', array(
            'expires' => time() - 42000,
            'path' => '/',
            'httponly' => true,
            'secure' => defined('SSL') && SSL,
            'samesite' => 'Strict'
        ));
    }

    protected function uuid() {
        $data = random_bytes(16);

        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
