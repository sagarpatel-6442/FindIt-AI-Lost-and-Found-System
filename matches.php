<?php
require __DIR__ . '/app/bootstrap.php';
require ROOT_PATH . '/app/controllers/matches.php';
[$flashes, $user, $unreadCount] = load_layout_context($pdo);
include ROOT_PATH . '/app/views/layout/header.php';
include ROOT_PATH . '/app/views/matches.php';
include ROOT_PATH . '/app/views/layout/footer.php';
