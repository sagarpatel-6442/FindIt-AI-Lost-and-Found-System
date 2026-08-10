<?php
require_login();
$stmt = $pdo->prepare('SELECT c.*, l.title lost_title, f.title found_title FROM claims c JOIN items l ON l.id = c.lost_item_id JOIN items f ON f.id = c.found_item_id WHERE c.claimant_id = ? ORDER BY c.created_at DESC');
$stmt->execute([current_user()['id']]); $claims = $stmt->fetchAll();
$pageTitle = 'My claims';
?>
