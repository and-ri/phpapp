<?php

class Cache {
    public function get($key) {
        $file = $this->path($key);

        if (!is_file($file)) {
            return null;
        }

        $data = unserialize(file_get_contents($file));

        if (!is_array($data) || !array_key_exists('value', $data)) {
            return null;
        }

        if ($data['expires'] && $data['expires'] < time()) {
            unlink($file);

            return null;
        }

        return $data['value'];
    }

    public function set($key, $value, $ttl = 0) {
        file_put_contents($this->path($key), serialize(array(
            'expires' => $ttl ? time() + (int)$ttl : 0,
            'value' => $value
        )), LOCK_EX);
    }

    public function delete($key) {
        $file = $this->path($key);

        if (is_file($file)) {
            unlink($file);
        }
    }

    public function deleteAll($prefix = '') {
        $files = glob(DIR_CACHE . $this->sanitize($prefix) . '*.cache');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function clear() {
        $this->deleteAll();
    }

    protected function path($key) {
        return DIR_CACHE . $this->sanitize($key) . '.cache';
    }

    protected function sanitize($key) {
        // Keys become file names: strip anything that could escape DIR_CACHE
        return ltrim(preg_replace('/[^a-zA-Z0-9._-]/', '_', (string)$key), '.');
    }
}
