<?php
require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$id=filter_input(INPUT_POST,'item_id',FILTER_VALIDATE_INT);$status=$_POST['status']??'';
 if($id&&in_array($status,['open','claimed','returned','closed'],true)){$stmt=$pdo->prepare('UPDATE items SET status=? WHERE id=?');$stmt->execute([$status,$id]);audit($pdo,'item_status_changed','Item #'.$id.' set to '.$status);flash('success','Item status updated.');}
 redirect('admin/items.php');
}
$type=$_GET['type']??'';$sql='SELECT i.*,u.name reporter_name FROM items i JOIN users u ON u.id=i.user_id';$params=[];if(in_array($type,['lost','found'],true)){$sql.=' WHERE i.report_type=?';$params[]=$type;}$sql.=' ORDER BY i.created_at DESC LIMIT 200';$stmt=$pdo->prepare($sql);$stmt->execute($params);$items=$stmt->fetchAll();
$pageTitle='Manage reports';
?>
