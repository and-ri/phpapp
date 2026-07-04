<?php

// Copy this file to e.g. 2026_07_04_create_users.php and fill in up()/down().
// Each migration returns an anonymous class, so multiple migration files
// can be loaded in one process without class name collisions.

return new class {
    public function up($db) {
        // Add schema changes (e.g., CREATE TABLE) here
    }

    public function down($db) {
        // Revert schema changes (e.g., DROP TABLE) here
    }
};
