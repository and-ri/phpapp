<?php

require_once 'core/library/db.php';

$db = new Db(null); // Initialize the database connection

$command = $argv[1] ?? null;

switch ($command) {
    case 'migrate':
        migrate($db);
        break;

    case 'rollback':
        rollback($db);
        break;

    case 'status':
        migrationStatus($db);
        break;

    default:
        echo "Usage: php migrate.php [migrate|rollback|status]\n";
        break;
}

function migrate($db) {
    echo "Running migrations...\n";

    // Ensure migrations table exists
    $db->query("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            applied_at DATETIME
        )
    ");

    $appliedMigrations = array_column(
        $db->query("SELECT migration FROM migrations", true),
        'migration'
    );

    $files = glob(__DIR__ . '/migrations/*.php');
    foreach ($files as $file) {
        $migrationName = basename($file, '.php');
        if (!in_array($migrationName, $appliedMigrations)) {
            require_once $file;
            $migration = new Migration();
            $migration->up($db);
            $db->query("INSERT INTO migrations (migration, applied_at) VALUES ('$migrationName', NOW())");
            echo "Applied: $migrationName\n";
        }
    }
}

function rollback($db) {
    echo "Rolling back last migration...\n";

    $lastMigration = $db->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT 1")[0]['migration'] ?? null;

    if ($lastMigration) {
        require_once __DIR__ . "/migrations/$lastMigration.php";
        $migration = new Migration();
        $migration->down($db);
        $db->query("DELETE FROM migrations WHERE migration = '$lastMigration'");
        echo "Rolled back: $lastMigration\n";
    } else {
        echo "No migrations to roll back.\n";
    }
}

function migrationStatus($db) {
    echo "Migration status:\n";

    $migrations = $db->query("SELECT migration, applied_at FROM migrations ORDER BY applied_at", true);
    foreach ($migrations as $migration) {
        echo "Applied: {$migration['migration']} at {$migration['applied_at']}\n";
    }

    $files = glob(__DIR__ . '/migrations/*.php');
    foreach ($files as $file) {
        $migrationName = basename($file, '.php');
        if (!in_array($migrationName, array_column($migrations, 'migration'))) {
            echo "Pending: $migrationName\n";
        }
    }
}