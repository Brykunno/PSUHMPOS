<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../config.php';
require_once '../../classes/DBConnection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'];

    // Get menu price
    $menu = $conn->query("SELECT price FROM menu_list WHERE id = '$menu_id'")->fetch_assoc();
    $price = $menu['price'];

    // Insert into order_items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $menu_id, $quantity, $price);
    $stmt->execute();

    // Optionally update total_amount in order_list
    $conn->query("UPDATE order_list SET total_amount = (SELECT SUM(quantity * price) FROM order_items WHERE order_id = '$order_id'),status = 0 WHERE id = '$order_id'");
    // Redirect back or return JSON for AJAX
    if(isset($_POST['ajax'])) {
        echo json_encode(['status' => 'success']);
    } else {
        header("Location: index.php");
        exit;
    }
}
?>