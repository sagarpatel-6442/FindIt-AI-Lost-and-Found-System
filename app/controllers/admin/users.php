<?php
require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$id=filter_input(INPUT_POST,'user_id',FILTER_VALIDATE_INT);$status=$_POST['status']??'';
 if($id===(int)current_user()['id']){flash('warning','You cannot disable your own account.');redirect('admin/users.php');}
 if($id&&in_array($status,['active','disabled'],true)){$pdo->prepare('UPDATE users SET status=? WHERE id=?')->execute([$status,$id]);audit($pdo,'user_status_changed','User #'.$id.' set to '.$status);flash('success','User status updated.');}
 redirect('admin/users.php');
}
$users=$pdo->query('SELECT u.*, (SELECT COUNT(*) FROM items i WHERE i.user_id=u.id) report_count FROM users u ORDER BY u.created_at DESC')->fetchAll();
$pageTitle='Manage users';
?>
