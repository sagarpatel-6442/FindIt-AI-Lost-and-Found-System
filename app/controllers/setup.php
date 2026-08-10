<?php
declare(strict_types=1);

$configPath = dirname(__DIR__, 2) . '/config/config.php';
$lockPath = dirname(__DIR__, 2) . '/config/installed.lock';
$message = '';
$error = '';

if (is_file($lockPath)) {
    exit('<h1>FindIt is already installed.</h1><p>Delete config/installed.lock only if you intentionally need to reinstall.</p><p><a href="index.php">Open FindIt</a></p>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['db_host'] ?? '127.0.0.1');
    $port = trim($_POST['db_port'] ?? '3306');
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['db_name'] ?? 'findit');
    $dbUser = trim($_POST['db_user'] ?? 'root');
    $dbPass = (string)($_POST['db_pass'] ?? '');
    $aiUrl = rtrim(trim((string)($_POST['ai_api_url'] ?? 'http://127.0.0.1:5000')), '/');
    $aiKey = trim((string)($_POST['ai_api_key'] ?? '')) ?: bin2hex(random_bytes(20));
    $adminName = trim((string)($_POST['admin_name'] ?? ''));
    $adminEmail = filter_var(trim((string)($_POST['admin_email'] ?? '')), FILTER_VALIDATE_EMAIL);
    $adminPassword = (string)($_POST['admin_password'] ?? '');

    if (!$dbName || !$adminName || !$adminEmail || strlen($adminPassword) < 10) {
        $error = 'Enter valid database details, administrator name/email and a password of at least 10 characters.';
    } else {
        try {
            $serverDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $serverPdo = new PDO($serverDsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $sql = file_get_contents(dirname(__DIR__, 2) . '/database/findit.sql');
            $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS findit[^;]+;/i', '', $sql);
            $sql = preg_replace('/USE findit;/i', '', $sql);
            $sql = preg_replace('/INSERT INTO users[\s\S]+?ON DUPLICATE KEY UPDATE name = VALUES\(name\);/i', '', $sql);
            $statements = preg_split('/;\s*(?:\r?\n|$)/', trim($sql)) ?: [];
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if ($statement !== '') $pdo->exec($statement);
            }

            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, "admin") ON DUPLICATE KEY UPDATE name = VALUES(name), password_hash = VALUES(password_hash), role = "admin", status = "active"');
            $stmt->execute([$adminName, $adminEmail, password_hash($adminPassword, PASSWORD_DEFAULT)]);

            $config = [
                'app_name' => 'FindIt',
                'app_env' => 'local',
                'timezone' => 'Australia/Sydney',
                'db_host' => $host,
                'db_port' => $port,
                'db_name' => $dbName,
                'db_user' => $dbUser,
                'db_pass' => $dbPass,
                'ai_api_url' => $aiUrl,
                'ai_api_key' => $aiKey,
                'ai_timeout_seconds' => 45,
                'upload_max_bytes' => 5 * 1024 * 1024,
                'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp'],
            ];
            $written = file_put_contents($configPath, "<?php\nreturn " . var_export($config, true) . ";\n");
            if ($written === false) throw new RuntimeException('Could not write config/config.php.');
            $envPath = dirname(__DIR__, 2) . '/python_ai/.env';
            file_put_contents($envPath, 'MATCH_API_KEY=' . $aiKey . PHP_EOL . 'HOST=127.0.0.1' . PHP_EOL . 'PORT=5000' . PHP_EOL . 'PRODUCTION=0' . PHP_EOL);
            file_put_contents($lockPath, date('c'));
            $message = 'Installation completed. The local AI configuration was created automatically. Start the Python service using start_ai.bat.';
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
