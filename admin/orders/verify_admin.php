<?php
require_once('../../config.php');
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if(empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
    exit;
}

try {
    // Query to check if user exists and is an admin (type = 1)
    $stmt = $conn->prepare("SELECT id, username, password, type FROM users WHERE username = ? AND type = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid admin credentials']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password (check MD5 hash first, then other methods)
    $inputPasswordMD5 = md5($password);
    
    if($inputPasswordMD5 === $user['password']) {
        // MD5 password match (most likely case for this system)
        echo json_encode([
            'status' => 'success', 
            'message' => 'Admin authentication successful',
            'admin_id' => $user['id'],
            'admin_username' => $user['username']
        ]);
    } else if(password_verify($password, $user['password'])) {
        // Password hash verification (for newer hashed passwords)
        echo json_encode([
            'status' => 'success', 
            'message' => 'Admin authentication successful',
            'admin_id' => $user['id'],
            'admin_username' => $user['username']
        ]);
    } else if($password === $user['password']) {
        // Plain text comparison (fallback)
        echo json_encode([
            'status' => 'success', 
            'message' => 'Admin authentication successful',
            'admin_id' => $user['id'],
            'admin_username' => $user['username']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid admin credentials']);
    }
    
} catch(Exception $e) {
    error_log("Admin verification error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'System error occurred']);
}
?>