<?php
require __DIR__ . '/app/bootstrap.php';
require ROOT_PATH . '/app/controllers/item.php';
[$flashes, $user, $unreadCount] = load_layout_context($pdo);
include ROOT_PATH . '/app/views/layout/header.php';
include ROOT_PATH . '/app/views/item.php';
include ROOT_PATH . '/app/views/layout/footer.php';
