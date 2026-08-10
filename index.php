<?php
require __DIR__ . '/app/bootstrap.php';
require ROOT_PATH . '/app/controllers/index.php';
[$flashes, $user, $unreadCount] = load_layout_context($pdo);
include ROOT_PATH . '/app/views/layout/header.php';
include ROOT_PATH . '/app/views/index.php';
include ROOT_PATH . '/app/views/layout/footer.php';
