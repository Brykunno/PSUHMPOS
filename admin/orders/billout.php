<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];

    // Update the order in the database
    $stmt = $conn->prepare("UPDATE order_list SET 
        status = 3
        WHERE id = ?");
    $stmt->bind_param("i",$order_id);

    if($stmt->execute()){
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => $conn->error]);
    }
    exit;
}
?>