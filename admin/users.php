<?php
require __DIR__ . '/../app/bootstrap.php';
require ROOT_PATH . '/app/controllers/admin/users.php';
[$flashes, $user, $unreadCount] = load_layout_context($pdo);
include ROOT_PATH . '/app/views/layout/header.php';
include ROOT_PATH . '/app/views/admin/users.php';
include ROOT_PATH . '/app/views/layout/footer.php';
