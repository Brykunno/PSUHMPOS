<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser from caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Get current full URL
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$current_url .= "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// Redirect to login if not logged in and not on login page
if (!isset($_SESSION['userdata']) && !strpos($current_url, 'login.php')) {
    redirect('admin/login.php');
    exit;
}

// Redirect away from login page if already logged in
if (isset($_SESSION['userdata']) && strpos($current_url, 'login.php')) {
    redirect('admin/index.php');
    exit;
}

// Restrict access to admin pages
$module = array('', 'admin', 'tutor'); // login_type: 1 = admin, 2 = tutor
if (
    isset($_SESSION['userdata']) && 
    (strpos($current_url, 'index.php') || strpos($current_url, 'admin/')) && 
    $_SESSION['userdata']['login_type'] != 1
) {
    echo "<script>alert('Access Denied!'); location.replace('" . base_url . $module[$_SESSION['userdata']['login_type']] . "');</script>";
    exit;
}
