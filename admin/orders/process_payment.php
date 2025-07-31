<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $cash_amount = floatval($_POST['cash_amount']);
    $discount_type = $_POST['discount_type'];
    $discount_percent = floatval($_POST['discount_percent']);
    $subtotal = floatval($_POST['subtotal']);
    $grand_total = floatval($_POST['grand_total']);
    $change = floatval($_POST['change']);
    $emoney_reference = $_POST['emoney_reference'];
    $card_number = $_POST['card_number'];
    $vat_amount = $_POST['vat_amount'];


    // Update the order in the database
    $stmt = $conn->prepare("UPDATE order_list SET 
        payment_method = ?, 
        tendered_amount = ?, 
        discount_type = ?, 
        discount_percent = ?, 
        total_amount = ?, 
        discounted_amount = ?, 
        change_amount = ?, 
        reference_number = ?, 
        card_number = ?, 
        vat_amount = ?,
        status = 2
        WHERE id = ?");
    $stmt->bind_param("sdsdddsssdi", $payment_method, $cash_amount, $discount_type, $discount_percent,  $subtotal,$grand_total, $change, $emoney_reference, $card_number,$vat_amount, $order_id);

    if($stmt->execute()){
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => $conn->error]);
    }
    exit;
}
?>