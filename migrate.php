<?php

if (PHP_SAPI !== 'cli') {
    exit('migrate.php can only be run from the command line.');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 0);

require_once 'config/directories.php';
require_once DIR_CORE . 'registry.php';
require_once DIR_CORE . 'autoload.php';

$registry = new Registry();

$registry->set('env', new Env());

$db = new Db($registry);

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
        $db->query("SELECT migration FROM migrations", true) ?: array(),
        'migration'
    );

    foreach (migrationFiles() as $file) {
        $migrationName = basename($file, '.php');
        if (!in_array($migrationName, $appliedMigrations)) {
            $migration = loadMigration($file);
            $migration->up($db);
            $db->query("INSERT INTO migrations (migration, applied_at) VALUES ('" . $db->escape($migrationName) . "', NOW())");
            echo "Applied: $migrationName\n";
        }
    }
}

function migrationFiles() {
    return array_filter(glob(__DIR__ . '/migrations/*.php'), function ($file) {
        return basename($file) !== 'template.php';
    });
}

function loadMigration($file) {
    $migration = require $file;

    // New style: the file returns an anonymous class instance.
    // Old style: the file defines a Migration class.
    if (is_object($migration)) {
        return $migration;
    }

    if (class_exists('Migration')) {
        $class = 'Migration';

        return new $class();
    }

    throw new RuntimeException("Migration file $file must return an object or define a Migration class.");
}

function rollback($db) {
    echo "Rolling back last migration...\n";

    $lastMigration = $db->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT 1", true)[0]['migration'] ?? null;

    if ($lastMigration) {
        $lastMigration = basename($lastMigration);

        $migration = loadMigration(__DIR__ . "/migrations/$lastMigration.php");
        $migration->down($db);
        $db->query("DELETE FROM migrations WHERE migration = '" . $db->escape($lastMigration) . "'");
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