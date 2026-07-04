<?php

// Refuse to run if the application is already installed, otherwise anyone
// could overwrite the configuration and take over the site
if (file_exists(__DIR__ . '/../.env')) {
    http_response_code(403);
    die('PHPapp is already installed. Delete the .env file to reinstall.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data for database
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? 'database';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPassword = $_POST['db_password'] ?? '';
    $dbPort = $_POST['db_port'] ?? '3306';
    $dbPrefix = $_POST['db_prefix'] ?? 'prefix_';

    // Get form data for web config
    $useSSL = isset($_POST['use_ssl']);
    $domain = $_POST['domain'] ?? $_SERVER['HTTP_HOST'];
    $defaultLanguage = $_POST['default_language'] ?? 'en';

    // Validate values that end up in generated files
    if (!preg_match('/^[a-zA-Z0-9._:-]+$/', $domain)) {
        $errors[] = 'Invalid domain.';
    }

    if (!preg_match('/^[a-z]{2}(-[a-z]{2})?$/i', $defaultLanguage)) {
        $errors[] = 'Invalid default language (expected e.g. "en").';
    }

    if (!preg_match('/^[0-9]{1,5}$/', $dbPort)) {
        $errors[] = 'Invalid database port.';
    }

    if (!preg_match('/^[a-zA-Z0-9_]*$/', $dbPrefix)) {
        $errors[] = 'Invalid table prefix (letters, digits and underscore only).';
    }

    if (!$errors) {
        // Generate .env file (escape backslashes/quotes, strip newlines)
        $env = function ($value) {
            return '"' . addcslashes(str_replace(["\r", "\n"], '', $value), '\\"') . '"';
        };

        $envContent = "DB_HOST=" . $env($dbHost) . "\n"
            . "DB_NAME=" . $env($dbName) . "\n"
            . "DB_USER=" . $env($dbUser) . "\n"
            . "DB_PASS=" . $env($dbPassword) . "\n"
            . "DB_PORT=" . $env($dbPort) . "\n"
            . "DB_PREFIX=" . $env($dbPrefix) . "\n";
        file_put_contents(__DIR__ . '/../.env', $envContent);

        // Generate web.php config (values exported as safe PHP literals)
        $webConfigContent = "<?php\n\n";
        $webConfigContent .= "define('SSL', " . ($useSSL ? 'true' : 'false') . ");\n";
        $webConfigContent .= "define('DOMAIN', " . var_export($domain, true) . ");\n";
        $webConfigContent .= "define('DEFAULT_LANGUAGE', " . var_export(strtolower($defaultLanguage), true) . ");\n";
        $webConfigContent .= "define('SESSION_NAME', 'PHPAPPSESSID');\n\n";
        $webConfigContent .= "define('URL_WEBSITE', (SSL ? 'https://' : 'http://') . DOMAIN . '/');\n";
        $webConfigContent .= "define('URL_STATIC', URL_WEBSITE . 'static/');\n";
        file_put_contents(__DIR__ . '/../config/web.php', $webConfigContent);

        $root = dirname(__DIR__);

        $execDisabled = !function_exists('exec') || in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        // Check if composer.phar exists
        if (!file_exists($root . '/composer.phar')) {
            if ($execDisabled) {
                echo '<div class="alert alert-warning">
                    <strong>Warning:</strong> The exec function is disabled on this server.
                    Please manually install Composer and dependencies by running these commands in your terminal:
                    <pre>
cd ' . htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . '
php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"
php composer-setup.php
php -r "unlink(\'composer-setup.php\');"
php composer.phar install
php migrate.php migrate
                    </pre>
                    Then reload this page after completing these steps.
                    </div>';
                exit;
            }

            // Download composer.phar
            file_put_contents($root . '/composer-setup.php', file_get_contents('https://getcomposer.org/installer'));
            exec('php ' . escapeshellarg($root . '/composer-setup.php') . ' --install-dir=' . escapeshellarg($root) . ' 2>&1', $output, $returnVar);
            unlink($root . '/composer-setup.php');
            if ($returnVar !== 0) {
                die('Error downloading Composer: ' . htmlspecialchars(implode("\n", $output), ENT_QUOTES, 'UTF-8'));
            }
        }

        if ($execDisabled) {
            echo '<div class="alert alert-warning">
                <strong>Warning:</strong> The exec function is disabled on this server.
                Please manually complete the installation by running these commands in your terminal:
                <pre>
cd ' . htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . '
php composer.phar install
php migrate.php migrate
                </pre>
                Then reload this page after completing these steps.
                </div>';
            exit;
        }

        // Run composer install
        exec('php ' . escapeshellarg($root . '/composer.phar') . ' install --no-interaction --working-dir=' . escapeshellarg($root) . ' 2>&1', $output, $returnVar);
        if ($returnVar !== 0) {
            die('Error installing dependencies: ' . htmlspecialchars(implode("\n", $output), ENT_QUOTES, 'UTF-8'));
        }

        // Run database migrations
        exec('php ' . escapeshellarg($root . '/migrate.php') . ' migrate 2>&1', $output, $returnVar);
        if ($returnVar !== 0) {
            die('Error running migrations: ' . htmlspecialchars(implode("\n", $output), ENT_QUOTES, 'UTF-8'));
        }

        echo 'Installation complete. For security, delete or block access to installer.php. Reload the page.';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPapp Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">PHPapp Installer</h1>
    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endforeach; ?>
    <form method="POST" class="mt-4">
        <div class="bg-light p-4 mb-4">
            <h3>Database Configuration</h3>
            <div class="mb-3">
                <label for="db_host" class="form-label">Database Host</label>
                <input type="text" class="form-control" id="db_host" name="db_host" required value="localhost">
            </div>
            <div class="mb-3">
                <label for="db_name" class="form-label">Database Name</label>
                <input type="text" class="form-control" id="db_name" name="db_name" required value="database">
            </div>
            <div class="mb-3">
                <label for="db_user" class="form-label">Database User</label>
                <input type="text" class="form-control" id="db_user" name="db_user" required value="root">
            </div>
            <div class="mb-3">
                <label for="db_password" class="form-label">Database Password</label>
                <input type="password" class="form-control" id="db_password" name="db_password" placeholder="<password>" value="">
            </div>
            <div class="mb-3">
                <label for="db_port" class="form-label">Database Port</label>
                <input type="text" class="form-control" id="db_port" name="db_port" required value="3306">
            </div>
            <div class="mb-3">
                <label for="db_prefix" class="form-label">Table Prefix</label>
                <input type="text" class="form-control" id="db_prefix" name="db_prefix" required value="prefix_">
            </div>
        </div>
        <div class="bg-light p-4 mb-4">
            <h3>Web Configuration</h3>
            <div class="mb-3">
                <label for="domain" class="form-label">Domain</label>
                <input type="text" class="form-control" id="domain" name="domain" value="<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="default_language" class="form-label">Default Language</label>
                <input type="text" class="form-control" id="default_language" name="default_language" value="en" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="use_ssl" name="use_ssl">
                <label class="form-check-label" for="use_ssl">Use SSL (HTTPS)</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Install</button>
    </form>
</div>
</body>
</html>
