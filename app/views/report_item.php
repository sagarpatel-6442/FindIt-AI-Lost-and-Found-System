<div class="card form-card"><h1>Report a lost or found item</h1><p class="muted">Accurate details improve match quality.</p>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><div class="form-grid">
<div class="form-group"><label for="report_type">Report type</label><select id="report_type" name="report_type" required><option value="">Select</option><option value="lost" <?= ($_POST['report_type'] ?? '') === 'lost' ? 'selected' : '' ?>>Lost item</option><option value="found" <?= ($_POST['report_type'] ?? '') === 'found' ? 'selected' : '' ?>>Found item</option></select></div>
<div class="form-group"><label for="category">Category</label><select id="category" name="category" required><option value="">Select category</option><?php foreach ($categories as $cat): ?><option <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= h($cat) ?></option><?php endforeach; ?></select></div>
<div class="form-group full"><label for="title">Short title</label><input id="title" name="title" maxlength="150" value="<?= old('title') ?>" placeholder="Black backpack with white logo" required></div>
<div class="form-group full"><label for="description">Detailed description</label><textarea id="description" name="description" required><?= old('description') ?></textarea><span class="help">Include brand, size, unique marks and contents that can be safely disclosed.</span></div>
<div class="form-group"><label for="colour">Colour</label><input id="colour" name="colour" value="<?= old('colour') ?>" required></div>
<div class="form-group"><label for="location">Location</label><input id="location" name="location" value="<?= old('location') ?>" placeholder="Library, Level 2" required></div>
<div class="form-group"><label for="incident_date">Date lost/found</label><input id="incident_date" type="date" name="incident_date" max="<?= date('Y-m-d') ?>" value="<?= old('incident_date', date('Y-m-d')) ?>" required></div>
<div class="form-group"><label for="image">Photograph</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" data-preview><span class="help">JPG, PNG or WEBP; maximum 5 MB.</span></div>
<div class="form-group full"><img id="image-preview" hidden alt="Selected image preview" style="max-height:260px;border-radius:12px"></div>
</div><button class="btn" type="submit">Submit report</button></form></div>
