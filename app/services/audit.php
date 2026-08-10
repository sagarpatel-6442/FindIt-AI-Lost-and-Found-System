<?php
declare(strict_types=1);
function audit(PDO $pdo, string $action, ?string $details = null, ?int $userId = null): void
{
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId ?? (current_user()['id'] ?? null), $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
}
