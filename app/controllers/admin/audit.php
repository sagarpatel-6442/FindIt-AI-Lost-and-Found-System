<?php
require_admin();
$stmt=$pdo->query('SELECT a.*,u.name user_name,u.email FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 300');$logs=$stmt->fetchAll();
$pageTitle='Audit log';
?>
