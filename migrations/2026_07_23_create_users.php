<?php

return new class {
    public function up($db) {
        $prefix = $db->prefix;

        // Core identity/auth table. Everything here is provider-agnostic:
        // email/username/password are all nullable so local, username-only
        // and OAuth-only accounts fit the same table.
        $db->query("
            CREATE TABLE IF NOT EXISTS `{$prefix}user` (
                `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` CHAR(36) NOT NULL,
                `email` VARCHAR(255) DEFAULT NULL,
                `username` VARCHAR(191) DEFAULT NULL,
                `password` VARCHAR(255) DEFAULT NULL,
                `status` TINYINT NOT NULL DEFAULT 0,
                `email_verified_at` DATETIME DEFAULT NULL,
                `remember_token` VARCHAR(64) DEFAULT NULL,
                `last_login_at` DATETIME DEFAULT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`user_id`),
                UNIQUE KEY `uuid` (`uuid`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Linked OAuth identities (google, github, ...). One user can have
        // several providers, so this is a separate table.
        $db->query("
            CREATE TABLE IF NOT EXISTS `{$prefix}user_auth` (
                `user_auth_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `provider` VARCHAR(32) NOT NULL,
                `provider_user_id` VARCHAR(191) NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`user_auth_id`),
                UNIQUE KEY `provider_identity` (`provider`, `provider_user_id`),
                KEY `user_id` (`user_id`),
                CONSTRAINT `{$prefix}user_auth_user` FOREIGN KEY (`user_id`)
                    REFERENCES `{$prefix}user` (`user_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Arbitrary application-specific fields as key/value, so app code
        // never has to alter the core user table.
        $db->query("
            CREATE TABLE IF NOT EXISTS `{$prefix}user_meta` (
                `user_meta_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `meta_key` VARCHAR(191) NOT NULL,
                `meta_value` TEXT,
                PRIMARY KEY (`user_meta_id`),
                UNIQUE KEY `user_key` (`user_id`, `meta_key`),
                CONSTRAINT `{$prefix}user_meta_user` FOREIGN KEY (`user_id`)
                    REFERENCES `{$prefix}user` (`user_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down($db) {
        $prefix = $db->prefix;

        $db->query("DROP TABLE IF EXISTS `{$prefix}user_meta`");
        $db->query("DROP TABLE IF EXISTS `{$prefix}user_auth`");
        $db->query("DROP TABLE IF EXISTS `{$prefix}user`");
    }
};
