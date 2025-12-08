<?php
require_once('../../config.php');

// Initialize activity log if available
$activityLog = null;
try {
    if(!defined('BASE_PATH')) {
        define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
    }
    require_once(BASE_PATH . '/classes/ActivityLog.php');
    $activityLog = new ActivityLog();
} catch (Exception $e) {
    error_log('Failed to initialize ActivityLog in delete_order_item.php: ' . $e->getMessage());
}

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$order_id = $_POST['order_id'] ?? '';
$item_id = $_POST['item_id'] ?? '';

// Validate input
if(empty($order_id) || empty($item_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID and Item ID are required']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // First, get the item details before deletion
    $item_query = $conn->prepare("SELECT oi.*, ml.name as menu_name, ml.price 
                                  FROM order_items oi 
                                  INNER JOIN menu_list ml ON oi.menu_id = ml.id 
                                  WHERE oi.id = ? AND oi.order_id = ?");
    $item_query->bind_param("ii", $item_id, $order_id);
    $item_query->execute();
    $item_result = $item_query->get_result();
    
    if($item_result->num_rows === 0) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Order item not found']);
        exit;
    }
    
    $item_data = $item_result->fetch_assoc();
    $item_total = $item_data['price'] * $item_data['quantity'];
    
    // Check if this is the last item in the order
    $remaining_items = $conn->prepare("SELECT COUNT(*) as count FROM order_items WHERE order_id = ?");
    $remaining_items->bind_param("i", $order_id);
    $remaining_items->execute();
    $count_result = $remaining_items->get_result();
    $item_count = $count_result->fetch_assoc()['count'];
    
    if($item_count <= 1) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Cannot void the last item in an order. Delete the entire order instead.']);
        exit;
    }
    
    // Get current user ID for void tracking
    $voided_by = $_settings->userdata('id');
    $reason = isset($_POST['reason']) ? $_POST['reason'] : 'Voided';
    
    // Instead of deleting, insert into voids table
    $insert_void = $conn->prepare("INSERT INTO voids (order_id, order_item_id, menu_id, quantity, amount, reason, voided_by, voided_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $insert_void->bind_param("iiidisi", $order_id, $item_id, $item_data['menu_id'], $item_data['quantity'], $item_total, $reason, $voided_by);
    
    if(!$insert_void->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to record void: ' . $conn->error]);
        exit;
    }
    
    // Mark the order item as archived instead of deleting
    $archive_item = $conn->prepare("UPDATE order_items SET archived = 1, archived_by = ?, archived_at = NOW() WHERE id = ? AND order_id = ?");
    $archive_item->bind_param("iii", $voided_by, $item_id, $order_id);
    
    if(!$archive_item->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to archive order item: ' . $conn->error]);
        exit;
    }
    
    // Update the order total
    $update_total = $conn->prepare("UPDATE order_list SET total_amount = total_amount - ? WHERE id = ?");
    $update_total->bind_param("di", $item_total, $order_id);
    
    if(!$update_total->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update order total']);
        exit;
    }
    
    // Get updated order total
    $get_new_total = $conn->prepare("SELECT total_amount FROM order_list WHERE id = ?");
    $get_new_total->bind_param("i", $order_id);
    $get_new_total->execute();
    $total_result = $get_new_total->get_result();
    $new_total = $total_result->fetch_assoc()['total_amount'];
    
    // Commit transaction
    $conn->commit();
    
    // Log the void activity
    if($activityLog) {
        try {
            $description = "Voided {$item_data['quantity']} x {$item_data['menu_name']} from Order #{$order_id}. Reason: {$reason}. Amount: ₱" . number_format($item_total, 2);
            $activityLog->log_activity('VOID_ITEM', $description, 'Orders', $order_id);
        } catch (Exception $e) {
            error_log('ActivityLog Error in delete_order_item: ' . $e->getMessage());
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Order item voided successfully',
        'deleted_item' => $item_data['menu_name'],
        'new_total' => number_format($new_total, 2),
        'deleted_amount' => number_format($item_total, 2)
    ]);
    
} catch(Exception $e) {
    $conn->rollback();
    error_log("Delete order item error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'System error occurred']);
}
?>