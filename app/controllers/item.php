<?php
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT i.*, u.name reporter_name FROM items i JOIN users u ON u.id = i.user_id WHERE i.id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) { http_response_code(404); exit('Item not found.'); }
$canView = is_admin() || (int)$item['user_id'] === (int)current_user()['id'];
if (!$canView && $item['report_type'] === 'lost') { http_response_code(403); exit('You cannot view another user\'s lost report.'); }
$pageTitle = $item['title'];
?>
