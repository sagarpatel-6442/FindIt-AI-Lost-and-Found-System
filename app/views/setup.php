<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>FindIt Setup</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<main class="container main-content"><div class="card form-card">
<h1>FindIt setup</h1><p class="muted">Create the MySQL database, administrator account and local configuration.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><p><strong>AI API key:</strong> <code><?= htmlspecialchars($aiKey) ?></code></p><a class="btn" href="index.php">Open FindIt</a><?php else: ?>
<form method="post">
<div class="form-grid">
<div class="form-group"><label for="db_host">Database host</label><input id="db_host" name="db_host" value="127.0.0.1" required></div>
<div class="form-group"><label for="db_port">Database port</label><input id="db_port" name="db_port" value="3306" required></div>
<div class="form-group"><label for="db_name">Database name</label><input id="db_name" name="db_name" value="findit" required></div>
<div class="form-group"><label for="db_user">Database username</label><input id="db_user" name="db_user" value="root" required></div>
<div class="form-group full"><label for="db_pass">Database password</label><input id="db_pass" name="db_pass" type="password"><span class="help">Leave blank for a default XAMPP installation.</span></div>

<div class="form-group"><label for="ai_api_url">Python AI URL</label><input id="ai_api_url" name="ai_api_url" value="http://127.0.0.1:5000" required></div>
<div class="form-group full"><label for="ai_api_key">AI API key</label><input id="ai_api_key" name="ai_api_key" value="change-this-local-key" required></div>
<div class="form-group"><label for="admin_name">Administrator name</label><input id="admin_name" name="admin_name" required></div>
<div class="form-group"><label for="admin_email">Administrator email</label><input id="admin_email" type="email" name="admin_email" required></div>
<div class="form-group full"><label for="admin_password">Administrator password</label><input id="admin_password" type="password" name="admin_password" minlength="10" required><span class="help">At least 10 characters.</span></div>
</div><button class="btn" type="submit">Install FindIt</button>
</form><?php endif; ?></div></main></body></html>
