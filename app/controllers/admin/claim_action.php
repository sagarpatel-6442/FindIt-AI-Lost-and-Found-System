<?php
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/claims.php');
verify_csrf();
$claimId = filter_input(INPUT_POST, 'claim_id', FILTER_VALIDATE_INT);
$decision = $_POST['decision'] ?? '';
$notes = trim((string)($_POST['admin_notes'] ?? ''));
if (!in_array($decision, ['approved','rejected'], true) || mb_strlen($notes) < 5) { flash('danger','Enter a valid decision and note.'); redirect('admin/claims.php'); }
$stmt=$pdo->prepare('SELECT c.*, f.user_id found_reporter FROM claims c JOIN items f ON f.id=c.found_item_id WHERE c.id=? AND c.status="pending" FOR UPDATE');
$pdo->beginTransaction();
try {
    $stmt->execute([$claimId]); $claim=$stmt->fetch(); if(!$claim) throw new RuntimeException('Claim is no longer pending.');
    $update=$pdo->prepare('UPDATE claims SET status=?, admin_notes=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?');
    $update->execute([$decision,$notes,current_user()['id'],$claimId]);
    if($decision==='approved') {
        $pdo->prepare('UPDATE items SET status="claimed" WHERE id=?')->execute([$claim['found_item_id']]);
        $pdo->prepare('UPDATE items SET status="returned" WHERE id=?')->execute([$claim['lost_item_id']]);
        $pdo->prepare('UPDATE claims SET status="rejected", admin_notes=CONCAT(COALESCE(admin_notes,""), " Automatically closed after another claim was approved."), reviewed_by=?, reviewed_at=NOW() WHERE found_item_id=? AND id<>? AND status="pending"')->execute([current_user()['id'],$claim['found_item_id'],$claimId]);
    }
    notify($pdo,(int)$claim['claimant_id'],'Your ownership claim was '.$decision.'.','claims.php');
    notify($pdo,(int)$claim['found_reporter'],'An administrator '.$decision.' a claim for your found item.','item.php?id='.$claim['found_item_id']);
    $pdo->commit();
    audit($pdo,'claim_'.$decision,'Claim #'.$claimId.' reviewed');
    flash('success','Claim '.$decision.'.');
} catch(Throwable $e) { $pdo->rollBack(); flash('danger',$e->getMessage()); }
redirect('admin/claims.php');
