<?php
require_login();
$errors = [];
$categories = ['Bag', 'Electronics', 'Clothing', 'Keys', 'Books', 'Documents', 'Jewellery', 'Bottle', 'Other'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $type = $_POST['report_type'] ?? '';
    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $colour = trim((string)($_POST['colour'] ?? ''));
    $category = trim((string)($_POST['category'] ?? ''));
    $location = trim((string)($_POST['location'] ?? ''));
    $date = trim((string)($_POST['incident_date'] ?? ''));

    if (!in_array($type, ['lost', 'found'], true)) $errors[] = 'Select lost or found.';
    if (mb_strlen($title) < 3 || mb_strlen($title) > 150) $errors[] = 'Enter a title between 3 and 150 characters.';
    if (mb_strlen($description) < 10) $errors[] = 'Enter a useful description of at least 10 characters.';
    if (!$colour || !$category || !$location) $errors[] = 'Colour, category and location are required.';
    if (!valid_date($date) || $date > date('Y-m-d')) $errors[] = 'Enter a valid date that is not in the future.';

    $imagePath = null;
    if (!$errors) {
        try { $imagePath = upload_item_image($_FILES['image'] ?? [], $config); }
        catch (RuntimeException $e) { $errors[] = $e->getMessage(); }
    }
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO items (user_id, report_type, title, description, colour, category, location, incident_date, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([current_user()['id'], $type, $title, $description, $colour, $category, $location, $date, $imagePath]);
        $itemId = (int)$pdo->lastInsertId();
        audit($pdo, 'item_reported', ucfirst($type) . ' item #' . $itemId);
        flash('success', 'Your ' . $type . ' item report was created.');
        redirect('item.php?id=' . $itemId);
    }
}
$pageTitle = 'Report an item';
?>
