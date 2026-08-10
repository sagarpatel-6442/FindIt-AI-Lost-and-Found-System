<?php
require_login();
$type = $_GET['type'] ?? '';
$sql = 'SELECT * FROM items WHERE user_id = ?';
$params = [current_user()['id']];
if (in_array($type, ['lost', 'found'], true)) { $sql .= ' AND report_type = ?'; $params[] = $type; }
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $items = $stmt->fetchAll();
$pageTitle = 'My reports';
?>
