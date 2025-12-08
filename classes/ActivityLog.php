<?php
// Fix path resolution - handle both relative and AJAX calls
if(!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__FILE__)));
}
require_once(BASE_PATH . '/config.php');

// Handle AJAX requests
if(isset($_GET['action'])){
    $activityLog = new ActivityLog();
    
    if($_GET['action'] == 'get_statistics'){
        $stats = $activityLog->get_statistics();
        echo json_encode($stats);
        exit;
    }
}

class ActivityLog extends DBConnection {
    private $settings;
    
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    
    public function __destruct(){
        parent::__destruct();
    }
    
    /**
     * Log user activity
     * @param string $action - The action performed (e.g., 'login', 'create', 'update', 'delete')
     * @param string $description - Detailed description of the action
     * @param string $module - Module/section where action occurred (e.g., 'users', 'orders', 'menus')
     * @param int $record_id - ID of the affected record (optional)
     * @return bool
     */
    public function log_activity($action, $description, $module = null, $record_id = null){
        // Get user ID from session
        $user_id = $this->settings->userdata('id');
        
        // If no user is logged in, skip logging
        if(empty($user_id)){
            return false;
        }
        
        // Get IP address
        $ip_address = $this->get_client_ip();
        
        // Get user agent
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        // Escape all values
        $action = $this->conn->real_escape_string($action);
        $description = $this->conn->real_escape_string($description);
        $module = !empty($module) ? "'".$this->conn->real_escape_string($module)."'" : "NULL";
        $record_id = !empty($record_id) ? intval($record_id) : "NULL";
        $ip_address = $this->conn->real_escape_string($ip_address);
        $user_agent = $this->conn->real_escape_string($user_agent);
        
        // Insert log
        $sql = "INSERT INTO `activity_logs` 
                (`user_id`, `action`, `description`, `ip_address`, `user_agent`, `module`, `record_id`) 
                VALUES 
                ('{$user_id}', '{$action}', '{$description}', '{$ip_address}', '{$user_agent}', {$module}, {$record_id})";
        
        $result = $this->conn->query($sql);
        
        return $result ? true : false;
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip(){
        $ip_address = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        return $ip_address;
    }
    
    /**
     * Get all activity logs with filters
     */
    public function get_logs($filters = array()){
        $where = array();
        
        // Filter by user
        if(isset($filters['user_id']) && !empty($filters['user_id'])){
            $user_id = intval($filters['user_id']);
            $where[] = "al.user_id = {$user_id}";
        }
        
        // Filter by action
        if(isset($filters['action']) && !empty($filters['action'])){
            $action = $this->conn->real_escape_string($filters['action']);
            $where[] = "al.action = '{$action}'";
        }
        
        // Filter by module
        if(isset($filters['module']) && !empty($filters['module'])){
            $module = $this->conn->real_escape_string($filters['module']);
            $where[] = "al.module = '{$module}'";
        }
        
        // Filter by date range
        if(isset($filters['date_from']) && !empty($filters['date_from'])){
            $date_from = $this->conn->real_escape_string($filters['date_from']);
            $where[] = "DATE(al.date_created) >= '{$date_from}'";
        }
        
        if(isset($filters['date_to']) && !empty($filters['date_to'])){
            $date_to = $this->conn->real_escape_string($filters['date_to']);
            $where[] = "DATE(al.date_created) <= '{$date_to}'";
        }
        
        // Filter by search term
        if(isset($filters['search']) && !empty($filters['search'])){
            $search = $this->conn->real_escape_string($filters['search']);
            $where[] = "(al.description LIKE '%{$search}%' OR al.action LIKE '%{$search}%' OR CONCAT(u.firstname, ' ', u.lastname) LIKE '%{$search}%')";
        }
        
        $where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
        
        // Build query
        $sql = "SELECT 
                    al.*,
                    CONCAT(u.firstname, ' ', u.lastname) as user_name,
                    u.type as user_type
                FROM `activity_logs` al
                LEFT JOIN `users` u ON al.user_id = u.id
                {$where_clause}
                ORDER BY al.date_created DESC";
        
        $qry = $this->conn->query($sql);
        
        if(!$qry){
            // Log query error
            error_log('ActivityLog Query Error: ' . $this->conn->error . ' SQL: ' . $sql);
            return array();
        }
        
        $logs = array();
        
        if($qry->num_rows > 0){
            while($row = $qry->fetch_assoc()){
                $logs[] = $row;
            }
        }
        
        return $logs;
    }
    
    /**
     * Get activity log statistics
     */
    public function get_statistics(){
        $stats = array();
        
        // Total logs
        $total_qry = $this->conn->query("SELECT COUNT(*) as total FROM `activity_logs`");
        $stats['total_logs'] = $total_qry->fetch_assoc()['total'];
        
        // Logs today
        $today_qry = $this->conn->query("SELECT COUNT(*) as total FROM `activity_logs` WHERE DATE(date_created) = CURDATE()");
        $stats['today_logs'] = $today_qry->fetch_assoc()['total'];
        
        // Logs this week
        $week_qry = $this->conn->query("SELECT COUNT(*) as total FROM `activity_logs` WHERE YEARWEEK(date_created) = YEARWEEK(NOW())");
        $stats['week_logs'] = $week_qry->fetch_assoc()['total'];
        
        // Logs this month
        $month_qry = $this->conn->query("SELECT COUNT(*) as total FROM `activity_logs` WHERE YEAR(date_created) = YEAR(NOW()) AND MONTH(date_created) = MONTH(NOW())");
        $stats['month_logs'] = $month_qry->fetch_assoc()['total'];
        
        // Most active users
        $active_users_qry = $this->conn->query("
            SELECT 
                u.id,
                CONCAT(u.firstname, ' ', u.lastname) as name,
                COUNT(al.id) as activity_count
            FROM `activity_logs` al
            LEFT JOIN `users` u ON al.user_id = u.id
            WHERE DATE(al.date_created) >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY u.id
            ORDER BY activity_count DESC
            LIMIT 5
        ");
        
        $stats['active_users'] = array();
        while($row = $active_users_qry->fetch_assoc()){
            $stats['active_users'][] = $row;
        }
        
        // Activity by module
        $module_qry = $this->conn->query("
            SELECT 
                module,
                COUNT(*) as count
            FROM `activity_logs`
            WHERE module IS NOT NULL
            AND DATE(date_created) >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY module
            ORDER BY count DESC
        ");
        
        $stats['activity_by_module'] = array();
        while($row = $module_qry->fetch_assoc()){
            $stats['activity_by_module'][] = $row;
        }
        
        return $stats;
    }
    
    /**
     * Delete old logs (cleanup function)
     */
    public function delete_old_logs($days = 90){
        $days = intval($days);
        $sql = "DELETE FROM `activity_logs` WHERE date_created < DATE_SUB(NOW(), INTERVAL {$days} DAY)";
        return $this->conn->query($sql);
    }
    
    /**
     * Get single log details
     */
    public function get_log_details($id){
        $id = intval($id);
        $sql = "SELECT 
                    al.*,
                    CONCAT(u.firstname, ' ', u.lastname) as user_name,
                    u.username,
                    u.type as user_type
                FROM `activity_logs` al
                LEFT JOIN `users` u ON al.user_id = u.id
                WHERE al.id = {$id}";
        
        $qry = $this->conn->query($sql);
        if($qry && $qry->num_rows > 0){
            return $qry->fetch_assoc();
        }
        return null;
    }
}
