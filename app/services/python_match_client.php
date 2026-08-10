<?php
declare(strict_types=1);

function request_python_matches(array $source, array $candidates, array $config): array
{
    if (!function_exists('curl_init')) throw new RuntimeException('PHP cURL extension is not enabled.');
    $payload = [
        'source' => [
            'id' => (int)$source['id'],
            'description' => $source['description'],
            'colour' => $source['colour'],
            'category' => $source['category'],
            'location' => $source['location'],
            'incident_date' => $source['incident_date'],
            'image' => image_to_data_url($source['image_path']),
        ],
        'candidates' => array_map(static fn($item) => [
            'id' => (int)$item['id'],
            'description' => $item['description'],
            'colour' => $item['colour'],
            'category' => $item['category'],
            'location' => $item['location'],
            'incident_date' => $item['incident_date'],
            'image' => image_to_data_url($item['image_path']),
        ], $candidates),
    ];

    $ch = curl_init(rtrim($config['ai_api_url'], '/') . '/match');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-FindIt-Key: ' . $config['ai_api_key']],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => (int)$config['ai_timeout_seconds'],
    ]);
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($body === false || $status !== 200) throw new RuntimeException('Python AI service failed: ' . ($error ?: 'HTTP ' . $status));
    $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    return $decoded['matches'] ?? [];
}
