<?php

class ModelDemoDemo extends Model {
    protected function createDemoData() {
        // If table already exists, return
        if ($this->db->query("SHOW TABLES LIKE '" . $this->db->prefix . "posts'")) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . $this->db->prefix . "posts` (
                `post_id` INT(11) NOT NULL AUTO_INCREMENT,
                `hash` VARCHAR(255) NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`post_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        for ($i = 0; $i < 100; $i++) {
            $this->db->query("
                INSERT INTO `" . $this->db->prefix . "posts` (`hash`, `created_at`)
                VALUES ('" . $this->db->escape(md5(uniqid())) . "', NOW());
            ");
        }
    }

    public function getPosts($request_data = array()) {
        $this->createDemoData(); // Create demo data if not exists

        $sql = "SELECT * FROM " . $this->db->prefix . "posts";

        if (isset($request_data['start']) && isset($request_data['limit'])) {
            $sql .= " LIMIT " . (int)$request_data['start'] . ", " . (int)$request_data['limit'];
        }

        return $this->db->query($sql, true);
    }

    public function getTotalPosts() {
        $this->createDemoData(); // Create demo data if not exists

        $result = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->prefix . "posts");

        return $result['total'];
    }
}
