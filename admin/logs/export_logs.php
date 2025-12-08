<?php
// Fix path resolution for AJAX calls
$base_path = dirname(dirname(__DIR__));
require_once($base_path . '/config.php');
require_once($base_path . '/classes/ActivityLog.php');

// Only admin can access
if($_settings->userdata('type') != 1){
    exit;
}

$activityLog = new ActivityLog();

// Get filter parameters
$filters = array(
    'user_id' => isset($_GET['user_id']) ? $_GET['user_id'] : '',
    'action' => isset($_GET['action']) ? $_GET['action'] : '',
    'module' => isset($_GET['module']) ? $_GET['module'] : '',
    'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : '',
    'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : '',
    'search' => isset($_GET['search']) ? $_GET['search'] : ''
);

// Get logs
$logs = $activityLog->get_logs($filters);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=activity_logs_' . date('Y-m-d_His') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM to fix UTF-8 in Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, array('ID', 'Date/Time', 'User', 'User Type', 'Action', 'Module', 'Description', 'IP Address', 'Record ID'));

// Add data rows
foreach($logs as $log){
    $user_type = '';
    switch($log['user_type']){
        case 1: $user_type = 'Admin'; break;
        case 2: $user_type = 'Cashier'; break;
        case 3: $user_type = 'Kitchen'; break;
    }
    
    fputcsv($output, array(
        $log['id'],
        date('Y-m-d H:i:s', strtotime($log['date_created'])),
        $log['user_name'],
        $user_type,
        strtoupper($log['action']),
        $log['module'] ?: '-',
        $log['description'],
        $log['ip_address'],
        $log['record_id'] ?: '-'
    ));
}

fclose($output);
exit;
