<?php
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ? AND report_type = "lost"');
$stmt->execute([$id]); $source = $stmt->fetch();
if (!$source || (!is_admin() && (int)$source['user_id'] !== (int)current_user()['id'])) { http_response_code(404); exit('Lost report not found.'); }
$stmt = $pdo->prepare('SELECT * FROM items WHERE report_type = "found" AND status = "open" AND id <> ? ORDER BY incident_date DESC LIMIT 100');
$stmt->execute([$id]); $candidates = $stmt->fetchAll();
if (!$candidates) { flash('warning', 'No open found-item reports are available for matching.'); redirect('item.php?id=' . $id); }

$engine = 'python';
try {
    $matches = request_python_matches($source, $candidates, $config);
    if (!$matches) throw new RuntimeException('No results returned.');
} catch (Throwable $e) {
    $engine = 'php-fallback';
    $matches = php_fallback_matches($source, $candidates);
    flash('warning', 'The Python AI service was unavailable, so FindIt used the metadata fallback matcher. Start python_ai/app.py for image analysis.');
}

$pdo->beginTransaction();
try {
    $delete = $pdo->prepare('DELETE FROM ai_matches WHERE source_item_id = ?');
    $delete->execute([$id]);
    $insert = $pdo->prepare('INSERT INTO ai_matches (source_item_id, candidate_item_id, score, components_json, engine) VALUES (?, ?, ?, ?, ?)');
    foreach (array_slice($matches, 0, 5) as $match) {
        $insert->execute([$id, (int)$match['item_id'], (float)$match['score'], json_encode($match['components'] ?? []), $engine]);
    }
    $pdo->commit();
    audit($pdo, 'ai_matching_completed', 'Lost item #' . $id . ' using ' . $engine);
    flash('success', 'Five match recommendations were generated using ' . $engine . '.');
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
redirect('matches.php?id=' . $id);
