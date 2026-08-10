<?php
require_guest();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (login_is_rate_limited()) {
        $error = 'Too many login attempts. Try again later.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && $user['status'] === 'active' && secure_password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
            clear_login_failures();
            audit($pdo, 'login', 'Successful login');
            redirect('dashboard.php');
        }
        record_login_failure();
        $error = 'Invalid login details or disabled account.';
        audit($pdo, 'login_failed', 'Failed login for ' . $email, $user ? (int)$user['id'] : null);
    }
}
$pageTitle = 'Login';
?>
