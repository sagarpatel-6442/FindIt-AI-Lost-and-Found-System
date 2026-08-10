<?php
require __DIR__ . '/app/bootstrap.php';
require ROOT_PATH . '/app/controllers/notifications.php';
[$flashes, $user, $unreadCount] = load_layout_context($pdo);
include ROOT_PATH . '/app/views/layout/header.php';
include ROOT_PATH . '/app/views/notifications.php';
include ROOT_PATH . '/app/views/layout/footer.php';
