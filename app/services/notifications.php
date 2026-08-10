<?php
declare(strict_types=1);
function notify(PDO $pdo, int $userId, string $message, ?string $link = null): void
{
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)');
    $stmt->execute([$userId, mb_substr($message, 0, 255), $link]);
}
