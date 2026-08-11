<?php

echo '<pre>';

echo "cURL enabled: ";
var_dump(function_exists('curl_init'));

if (!function_exists('curl_init')) {
    exit("PHP cURL is not enabled.");
}

$ch = curl_init('http://127.0.0.1:5000/health');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "\nHTTP status: ";
var_dump($status);

echo "\nResponse: ";
var_dump($response);

echo "\nError: ";
var_dump($error);