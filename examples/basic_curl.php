<?php

// sudo apt install php-cli php-curl

$token = 'xxxxxxxxxxxxx';
$filePath = '/path/to/file.pdf';
$mimeType = 'application/pdf';
$document_type = 'to_be_implemented';

$mindeeClient = new Mindee\Client($token);

// file from memory
$fileBytes = file_get_contents($filePath); // using this to simulate behavior
$fileCurl = $mindeeClient->sourceFromBytes($fileBytes, 'file-name.pdf');
$response = $mindeeClient->parse($fileCurl, $fileCurl);

echo "\n";
echo $response['code'];
echo "\n\n";
echo $response['data'];
