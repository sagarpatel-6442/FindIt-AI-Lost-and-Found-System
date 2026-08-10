<?php
$pageTitle = $pageTitle ?? 'FindIt';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FindIt AI-assisted campus lost and found system">
    <title><?= h($pageTitle) ?> | FindIt</title>
    <link rel="stylesheet" href="<?= h(url('assets/css/style.css')) ?>">
    <script defer src="<?= h(url('assets/js/app.js')) ?>"></script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= h(url()) ?>"><span class="brand-mark">F</span>FindIt</a>
        <button class="nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">☰</button>
        <nav class="main-nav" aria-label="Primary navigation">
            <a href="<?= h(url()) ?>">Home</a>
            <?php if ($user): ?>
                <a href="<?= h(url('dashboard.php')) ?>">Dashboard</a>
                <a href="<?= h(url('report_item.php')) ?>">Report Item</a>
                <a href="<?= h(url('my_items.php')) ?>">My Reports</a>
                <a href="<?= h(url('claims.php')) ?>">Claims</a>
                <a href="<?= h(url('notifications.php')) ?>">Notifications<?= $unreadCount ? ' (' . $unreadCount . ')' : '' ?></a>
                <?php if (is_admin()): ?><a href="<?= h(url('admin/index.php')) ?>">Admin</a><?php endif; ?>
                <a href="<?= h(url('logout.php')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= h(url('login.php')) ?>">Login</a>
                <a class="nav-cta" href="<?= h(url('register.php')) ?>">Create account</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main id="main-content" class="container main-content">
<?php foreach ($flashes as $flash): ?>
    <div class="alert alert-<?= h($flash['type']) ?>" role="alert"><?= h($flash['message']) ?></div>
<?php endforeach; ?>
