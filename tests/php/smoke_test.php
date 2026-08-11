<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
$required = [
    'app/bootstrap.php','app/core/helpers.php','app/security/auth.php','app/security/csrf.php',
    'app/services/python_match_client.php','database/findit.sql','python_ai/app.py','python_ai/matcher.py',
    'assets/css/style.css','assets/js/app.js','index.php','login.php','report_item.php','admin/index.php'
];
$missing = [];
foreach ($required as $file) if (!is_file($root . '/' . $file)) $missing[] = $file;
if ($missing) { fwrite(STDERR, "Missing files:\n" . implode("\n", $missing) . "\n"); exit(1); }
echo "FindIt structure smoke test passed\n";
