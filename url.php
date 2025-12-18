<?php
$id = 1; // Example ID
$encrypted_id = base64_encode($id);
$url = "https://www.airforce.lk/certificate.php?id=" . urlencode($encrypted_id);
echo $url; // This URL will be used in your QR code
?>
