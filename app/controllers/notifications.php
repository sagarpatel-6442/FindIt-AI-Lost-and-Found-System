<?php
require_login();
$stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?'); $stmt->execute([current_user()['id']]);
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 100'); $stmt->execute([current_user()['id']]); $notifications = $stmt->fetchAll();
$pageTitle = 'Notifications';
?>
