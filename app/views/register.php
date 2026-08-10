<div class="card form-card"><h1>Create account</h1><p class="muted">Register as a student or staff user.</p>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endforeach; ?>
<form method="post"><?= csrf_field() ?>
<div class="form-group"><label for="name">Full name</label><input id="name" name="name" value="<?= old('name') ?>" maxlength="100" required autocomplete="name"></div>
<div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" value="<?= old('email') ?>" required autocomplete="email"></div>
<div class="form-grid"><div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" minlength="10" required autocomplete="new-password"></div><div class="form-group"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" minlength="10" required autocomplete="new-password"></div></div>
<button class="btn" type="submit">Create account</button></form></div>
