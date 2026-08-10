<?php
require_admin();
$stats = [];
foreach (['users' => 'SELECT COUNT(*) FROM users', 'items' => 'SELECT COUNT(*) FROM items', 'pending_claims' => 'SELECT COUNT(*) FROM claims WHERE status="pending"', 'open_found' => 'SELECT COUNT(*) FROM items WHERE report_type="found" AND status="open"'] as $key => $sql) $stats[$key] = (int)$pdo->query($sql)->fetchColumn();
$pageTitle = 'Administration';
?>
