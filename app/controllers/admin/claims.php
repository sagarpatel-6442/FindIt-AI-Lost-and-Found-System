<?php
require_admin();
$status = $_GET['status'] ?? 'pending'; if (!in_array($status, ['pending','approved','rejected','all'], true)) $status = 'pending';
$sql = 'SELECT c.*, u.name claimant_name, u.email claimant_email, l.title lost_title, f.title found_title FROM claims c JOIN users u ON u.id=c.claimant_id JOIN items l ON l.id=c.lost_item_id JOIN items f ON f.id=c.found_item_id'; $params = [];
if ($status !== 'all') { $sql .= ' WHERE c.status=?'; $params[] = $status; } $sql .= ' ORDER BY c.created_at DESC';
$stmt=$pdo->prepare($sql);$stmt->execute($params);$claims=$stmt->fetchAll();
$pageTitle='Review claims';
?>
