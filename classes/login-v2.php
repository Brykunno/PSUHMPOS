<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['login_type'] = $user['type'];

            echo json_encode([
                'status' => 'success',
                'type' => (int) $user['type']
            ]);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }
}
?>
