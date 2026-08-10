<?php
require_login();
$lostId = filter_input(INPUT_GET, 'lost', FILTER_VALIDATE_INT);
$foundId = filter_input(INPUT_GET, 'found', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ? AND user_id = ? AND report_type = "lost"'); $stmt->execute([$lostId, current_user()['id']]); $lost = $stmt->fetch();
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ? AND report_type = "found" AND status = "open"'); $stmt->execute([$foundId]); $found = $stmt->fetch();
if (!$lost || !$found) { http_response_code(404); exit('The selected reports are not available.'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $evidence = trim((string)($_POST['evidence'] ?? ''));
    if (mb_strlen($evidence) < 30) $error = 'Provide at least 30 characters of ownership evidence.';
    if (!$error) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM claims WHERE claimant_id = ? AND lost_item_id = ? AND found_item_id = ? AND status IN ("pending","approved")');
        $check->execute([current_user()['id'], $lostId, $foundId]);
        if ($check->fetchColumn()) $error = 'You already have an active claim for this item.';
    }
    if (!$error) {
        $stmt = $pdo->prepare('INSERT INTO claims (claimant_id, lost_item_id, found_item_id, evidence) VALUES (?, ?, ?, ?)');
        $stmt->execute([current_user()['id'], $lostId, $foundId, $evidence]);
        $claimId = (int)$pdo->lastInsertId();
        notify($pdo, (int)$found['user_id'], 'A claim was submitted for your found-item report.', 'item.php?id=' . $foundId);
        audit($pdo, 'claim_submitted', 'Claim #' . $claimId . ' for found item #' . $foundId);
        flash('success', 'Your claim was submitted for administrator review.');
        redirect('claims.php');
    }
}
$pageTitle = 'Submit claim';
?>
