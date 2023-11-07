<?php

// sudo apt install php-cli php-curl

$token = 'xxxxxxxxxxxxx';
$file_path = '/path/to/file.pdf';
$mime_type = 'application/pdf';
$document_type = 'to_be_implemented';

$mindeeClient = new Mindee\Client($token);

// file from memory
$file_bytes = file_get_contents($file_path); // using this to simulate behavior
$file_curl = $mindeeClient->sourceFromBytes($file_bytes, 'file-name.pdf');
$response = $mindeeClient->parse($file_curl, $file_curl);

echo "\n";
echo $response['code'];
echo "\n\n";
echo $response['data'];
