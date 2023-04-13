<?php
// sudo apt install php-cli php-curl

$token = 'xxxxxxxxxxxxx';
$file_path = '/path/to/file.pdf';
$mime_type = 'application/pdf';

$mindeeClient = new Mindee\Api\Client();

// file from memory
$file_bytes = file_get_contents($file_path); // using this to simulate behavior
$file_curl = $mindeeClient->docFromBytes('application/pdf', $file_bytes);
$response = $mindeeClient->predict($token, $file_curl);

echo "\n";
echo $response['code'];
echo "\n\n";
echo $response['data'];
