<?php
// sudo apt install php7.4-cli php7.4-curl
// https://php.watch/versions/8.1/CURLStringFile

$token = 'xxxxxxxxxxxxx';
$file_path = '/path/to/file.pdf';
$mime_type = 'application/pdf';

// file from disk
//$file_curl = new \CURLFile($file_path, 'application/pdf', 'file.pdf');

// file from memory
$file_bytes = file_get_contents($file_path); // using this to simulate behavior

$file_b64 = "data://$mime_type;base64," . base64_encode($file_bytes);
$file_curl = new \CURLFile($file_b64, $mime_type, 'file.pdf');

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token $token",
]);

curl_setopt($ch, CURLOPT_URL, 'https://api.mindee.net/v1/products/mindee/invoices/v3/predict');
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['document' => $file_curl]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch,  CURLINFO_HTTP_CODE);

echo "\n$http_code\n\n$response\n";

curl_close($ch);
