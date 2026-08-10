<?php
declare(strict_types=1);

function php_text_similarity(string $a, string $b): float
{
    $normalise = static function (string $text): array {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\pL\pN\s]+/u', ' ', $text) ?? '';
        $words = preg_split('/\s+/', trim($text)) ?: [];
        return array_values(array_unique(array_filter($words, static fn($w) => mb_strlen($w) > 2)));
    };
    $x = $normalise($a); $y = $normalise($b);
    if (!$x || !$y) return 0.0;
    $intersection = count(array_intersect($x, $y));
    $union = count(array_unique(array_merge($x, $y)));
    return $union ? $intersection / $union : 0.0;
}

function php_fallback_matches(array $source, array $candidates): array
{
    $results = [];
    foreach ($candidates as $candidate) {
        $description = php_text_similarity($source['description'], $candidate['description']);
        $category = strcasecmp($source['category'], $candidate['category']) === 0 ? 1.0 : 0.0;
        $colour = strcasecmp($source['colour'], $candidate['colour']) === 0 ? 1.0 : php_text_similarity($source['colour'], $candidate['colour']);
        $location = strcasecmp($source['location'], $candidate['location']) === 0 ? 1.0 : php_text_similarity($source['location'], $candidate['location']);
        $days = abs((strtotime($source['incident_date']) ?: 0) - (strtotime($candidate['incident_date']) ?: 0)) / 86400;
        $date = max(0.0, 1.0 - min($days, 30) / 30);
        $image = 0.0;
        $score = ($image * .40) + ($description * .20) + ($category * .15) + ($colour * .10) + ($location * .10) + ($date * .05);
        $results[] = [
            'item_id' => (int)$candidate['id'], 'score' => round($score * 100, 2),
            'components' => ['image'=>round($image*100,2),'description'=>round($description*100,2),'category'=>round($category*100,2),'colour'=>round($colour*100,2),'location'=>round($location*100,2),'date'=>round($date*100,2)],
        ];
    }
    usort($results, static fn($a, $b) => $b['score'] <=> $a['score']);
    return array_slice($results, 0, 5);
}
