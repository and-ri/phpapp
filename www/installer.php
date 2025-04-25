<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data for database
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? 'database';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPassword = $_POST['db_password'] ?? '';

    // Get form data for web config
    $useSSL = isset($_POST['use_ssl']) ? true : false;
    $domain = $_POST['domain'] ?? $_SERVER['HTTP_HOST'];
    $defaultLanguage = $_POST['default_language'] ?? 'en';

    // Generate .env file
    $envContent = "DB_HOST=\"$dbHost\"\nDB_NAME=\"$dbName\"\nDB_USER=\"$dbUser\"\nDB_PASS=\"$dbPassword\"\n";
    file_put_contents(__DIR__ . '/../.env', $envContent);

    // Generate web.php config
    $webConfigContent = "<?php\n\n";
    $webConfigContent .= "define('SSL', " . ($useSSL ? 'true' : 'false') . ");\n";
    $webConfigContent .= "define('DOMAIN', '$domain');\n";
    $webConfigContent .= "define('DEFAULT_LANGUAGE', '$defaultLanguage');\n";
    $webConfigContent .= "define('SESSION_NAME', 'PHPAPPSESSID');\n\n";
    $webConfigContent .= "define('URL_WEBSITE', (SSL ? 'https://' : 'http://') . DOMAIN . '/');\n";
    $webConfigContent .= "define('URL_STATIC', URL_WEBSITE . 'static/');\n";
    file_put_contents(__DIR__ . '/../config/web.php', $webConfigContent);

    // Check if composer.phar exists
    if (!file_exists(__DIR__ . '/../composer.phar')) {
        // Download composer.phar
        file_put_contents('composer-setup.php', file_get_contents('https://getcomposer.org/installer'));
        exec('php composer-setup.php', $output, $returnVar);
        unlink('composer-setup.php');
        if ($returnVar !== 0) {
            die('Error downloading Composer: ' . implode("\n", $output));
        }
    }

    // Run composer install
    exec('php ' . __DIR__ . '/../composer.phar install', $output, $returnVar);
    if ($returnVar !== 0) {
        die('Error installing dependencies: ' . implode("\n", $output));
    }

    // Run database migrations
    exec('php ' . __DIR__ . '/../migrate.php migrate', $output, $returnVar);
    if ($returnVar !== 0) {
        die('Error running migrations: ' . implode("\n", $output));
    }

    echo 'Installation complete. Reload the page.';
    exit;
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
        </div>
        <div class="bg-light p-4 mb-4">
            <h3>Web Configuration</h3>
            <div class="mb-3">
                <label for="domain" class="form-label">Domain</label>
                <input type="text" class="form-control" id="domain" name="domain" value="<?php echo $_SERVER['HTTP_HOST']; ?>" required>
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