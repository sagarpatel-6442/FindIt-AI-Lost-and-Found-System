<?php
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ? AND report_type = "lost"'); $stmt->execute([$id]); $source = $stmt->fetch();
if (!$source || (!is_admin() && (int)$source['user_id'] !== (int)current_user()['id'])) { http_response_code(404); exit('Lost report not found.'); }
$stmt = $pdo->prepare('SELECT m.*, i.title, i.description, i.colour, i.category, i.location, i.incident_date, i.image_path, i.status FROM ai_matches m JOIN items i ON i.id = m.candidate_item_id WHERE m.source_item_id = ? ORDER BY m.score DESC LIMIT 5');
$stmt->execute([$id]); $matches = $stmt->fetchAll();
$pageTitle = 'Match recommendations';
?>
