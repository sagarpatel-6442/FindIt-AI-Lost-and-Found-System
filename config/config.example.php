<?php
return [
    'app_name' => 'FindIt',
    'app_env' => 'local',
    'timezone' => 'Australia/Sydney',
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'findit',
    'db_user' => 'root',
    'db_pass' => '',
    'ai_api_url' => 'http://127.0.0.1:5000',
    'ai_api_key' => 'findit-local-key',
    'ai_timeout_seconds' => 45,
    'upload_max_bytes' => 5 * 1024 * 1024,
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp'],
];
