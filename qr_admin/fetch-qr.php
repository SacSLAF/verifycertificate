<?php

// Get the QR code URL
$qr_url = 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($_GET['data']) . '&size=150x150';

// Fetch the QR code image
$qr_image = file_get_contents($qr_url);

// Output the image with correct headers
header('Content-Type: image/png');
echo $qr_image;
?>
