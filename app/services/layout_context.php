<?php
declare(strict_types=1);

function load_layout_context(PDO $pdo): array
{
    $flashes = pull_flashes();
    $user = current_user();
    $unreadCount = 0;
    if ($user) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$user['id']]);
        $unreadCount = (int)$stmt->fetchColumn();
    }
    return [$flashes, $user, $unreadCount];
}
