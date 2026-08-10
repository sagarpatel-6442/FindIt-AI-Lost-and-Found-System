<?php
require_login();
$userId = (int)current_user()['id'];
$stmt = $pdo->prepare('SELECT report_type, COUNT(*) total FROM items WHERE user_id = ? GROUP BY report_type');
$stmt->execute([$userId]);
$counts = ['lost' => 0, 'found' => 0];
foreach ($stmt as $row) $counts[$row['report_type']] = (int)$row['total'];
$stmt = $pdo->prepare('SELECT COUNT(*) FROM claims WHERE claimant_id = ? AND status = "pending"');
$stmt->execute([$userId]);
$pendingClaims = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC LIMIT 6');
$stmt->execute([$userId]);
$items = $stmt->fetchAll();
$pageTitle = 'Dashboard';
?>
