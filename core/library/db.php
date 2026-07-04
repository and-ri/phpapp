<?php

class Db {
    private $registry;
    private $adaptor;
    public $prefix;

    public function __construct($registry) {
        $this->registry = $registry;

        require_once DIR_CONFIG . 'database.php';

        $this->prefix = DB_PREFIX ? DB_PREFIX : $this->env->get('DB_PREFIX');

        $DB_HOST = DB_HOST ? DB_HOST : $this->env->get('DB_HOST');
        $DB_USER = DB_USER ? DB_USER : $this->env->get('DB_USER');
        $DB_PASS = DB_PASS ? DB_PASS : $this->env->get('DB_PASS');
        $DB_NAME = DB_NAME ? DB_NAME : $this->env->get('DB_NAME');
        $DB_PORT = DB_PORT ? DB_PORT : $this->env->get('DB_PORT');

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->adaptor = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);
        } catch (mysqli_sql_exception $e) {
            throw new RuntimeException('Could not connect to the database: ' . $e->getMessage(), 0, $e);
        }

        // Without an explicit connection charset real_escape_string() can be
        // bypassed with certain multi-byte encodings
        $this->adaptor->set_charset('utf8mb4');
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function query($sql, $rows = false) {
        $result = $this->adaptor->query($sql);

        return $this->fetch($result, $rows);
    }

    /**
     * Prepared statement query. Placeholders: ?
     * Example: $db->execute("SELECT * FROM users WHERE email = ?", [$email], true)
     */
    public function execute($sql, array $params = array(), $rows = false) {
        $statement = $this->adaptor->prepare($sql);

        if ($params) {
            $statement->bind_param(str_repeat('s', count($params)), ...$params);
        }

        $statement->execute();

        $result = $statement->get_result();

        $output = $this->fetch($result === false ? true : $result, $rows);

        $statement->close();

        return $output;
    }

    private function fetch($result, $rows) {
        if ($result instanceof mysqli_result) {
            if ($rows) {
                $output = $result->num_rows ? $result->fetch_all(MYSQLI_ASSOC) : array();
            } else {
                $output = $result->num_rows ? $result->fetch_assoc() : false;
            }
        } else {
            $output = false;
        }

        return $output;
    }

    public function escape($value) {
        return $this->adaptor->real_escape_string($value);
    }

    public function last() {
        return $this->adaptor->insert_id;
    }

    public function count() {
        return $this->adaptor->affected_rows;
    }

    public function __destruct() {
        if ($this->adaptor instanceof mysqli) {
            $this->adaptor->close();
        }
    }
}
