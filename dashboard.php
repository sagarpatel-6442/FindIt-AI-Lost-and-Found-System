<?php
require __DIR__ . '/app/bootstrap.php';
require ROOT_PATH . '/app/controllers/dashboard.php';
[$flashes, $user, $unreadCount] = load_layout_context($pdo);
include ROOT_PATH . '/app/views/layout/header.php';
include ROOT_PATH . '/app/views/dashboard.php';
include ROOT_PATH . '/app/views/layout/footer.php';
