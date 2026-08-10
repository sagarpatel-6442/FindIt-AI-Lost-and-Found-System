<div class="card form-card"><h1>Log in</h1><p class="muted">Access your reports, matches and claims.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" required autocomplete="email"></div>
<div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
<button class="btn" type="submit">Log in</button></form></div>
