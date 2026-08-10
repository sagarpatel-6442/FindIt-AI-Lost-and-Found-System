<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax','use_strict_mode'=>true]);
}

define('ROOT_PATH', dirname(__DIR__));
$config = require ROOT_PATH . '/config/config.php';
date_default_timezone_set($config['timezone'] ?? 'UTC');

$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$rootReal = realpath(ROOT_PATH) ?: ROOT_PATH;
$baseUrl = '';
if ($documentRoot !== '' && str_starts_with(strtolower(str_replace('\\','/',$rootReal)), strtolower(str_replace('\\','/',$documentRoot)))) {
    $baseUrl = substr(str_replace('\\','/',$rootReal), strlen(str_replace('\\','/',$documentRoot)));
}
define('BASE_URL', rtrim('/' . trim($baseUrl, '/'), '/'));

require_once ROOT_PATH . '/app/core/helpers.php';
require_once ROOT_PATH . '/app/security/headers.php';
require_once ROOT_PATH . '/app/security/auth.php';
require_once ROOT_PATH . '/app/security/csrf.php';
require_once ROOT_PATH . '/app/security/uploads.php';
require_once ROOT_PATH . '/app/services/audit.php';
require_once ROOT_PATH . '/app/services/notifications.php';
require_once ROOT_PATH . '/app/services/fallback_matcher.php';
require_once ROOT_PATH . '/app/services/python_match_client.php';
require_once ROOT_PATH . '/app/services/layout_context.php';
apply_security_headers();

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['db_host'], $config['db_port'], $config['db_name']);
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
    ]);
} catch (PDOException $e) {
    if (basename($_SERVER['PHP_SELF'] ?? '') !== 'setup.php') {
        http_response_code(500);
        echo '<h1>Database connection failed</h1>';
        echo '<p>Run <a href="' . h((BASE_URL !== '' ? BASE_URL : '') . '/setup.php') . '">setup.php</a> or check config/config.php.</p>';
        if (($config['app_env'] ?? 'production') === 'local') echo '<pre>' . h($e->getMessage()) . '</pre>';
        exit;
    }
}
