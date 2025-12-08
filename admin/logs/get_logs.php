<?php
// Fix path resolution for AJAX calls
$base_path = dirname(dirname(__DIR__));
require_once($base_path . '/config.php');

header('Content-Type: application/json');

try {
    // Simple test first
    global $conn;
    
    // Check if activity_logs table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'activity_logs'");
    
    if($table_check->num_rows == 0) {
        throw new Exception('activity_logs table does not exist. Please run the migration.');
    }
    
    require_once($base_path . '/classes/ActivityLog.php');
    $activityLog = new ActivityLog();

    // Get filter parameters
    $filters = array(
        'user_id' => isset($_POST['user_id']) ? $_POST['user_id'] : '',
        'action' => isset($_POST['action']) ? $_POST['action'] : '',
        'module' => isset($_POST['module']) ? $_POST['module'] : '',
        'date_from' => isset($_POST['date_from']) ? $_POST['date_from'] : '',
        'date_to' => isset($_POST['date_to']) ? $_POST['date_to'] : '',
        'search' => isset($_POST['search']) ? $_POST['search'] : ''
    );

    // Get logs with error checking
    $logs = $activityLog->get_logs($filters);
    
    if($logs === false) {
        throw new Exception('Failed to retrieve logs from database');
    }

    // Return as JSON for DataTables
    $response = array(
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        'recordsTotal' => count($logs),
        'recordsFiltered' => count($logs),
        'data' => $logs
    );

    echo json_encode($response);
    exit;
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    error_log('ActivityLog Error: ' . $e->getMessage());
    echo json_encode(array(
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => array(),
        'error' => $e->getMessage()
    ));
    exit;
}
