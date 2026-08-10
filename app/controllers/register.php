<?php
require_guest();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['password_confirmation'] ?? '');

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) $errors[] = 'Enter a valid name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 10) $errors[] = 'Use a password with at least 10 characters.';
    if ($password !== $confirm) $errors[] = 'Password confirmation does not match.';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, secure_password_hash($password)]);
            audit($pdo, 'user_registered', 'Account created for ' . $email, (int)$pdo->lastInsertId());
            flash('success', 'Account created. You can now log in.');
            redirect('login.php');
        } catch (PDOException $e) {
            $errors[] = $e->getCode() === '23000' ? 'An account already exists with this email.' : 'Registration failed.';
        }
    }
}
$pageTitle = 'Create account';
?>
