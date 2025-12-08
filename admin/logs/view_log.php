<?php
// Fix path resolution for AJAX calls
$base_path = dirname(dirname(__DIR__));
require_once($base_path . '/config.php');
require_once($base_path . '/classes/ActivityLog.php');

if(!isset($_GET['id'])){
    echo '<div class="alert alert-danger">Log ID is required</div>';
    exit;
}

$activityLog = new ActivityLog();
$log = $activityLog->get_log_details($_GET['id']);

if(!$log){
    echo '<div class="alert alert-danger">Log not found</div>';
    exit;
}

// Get user type label
$user_type_label = '';
switch($log['user_type']){
    case 1:
        $user_type_label = '<span class="badge badge-danger">Admin</span>';
        break;
    case 2:
        $user_type_label = '<span class="badge badge-primary">Cashier</span>';
        break;
    case 3:
        $user_type_label = '<span class="badge badge-info">Kitchen</span>';
        break;
}

// Get action badge
$action_badge = '';
switch($log['action']){
    case 'login':
        $action_badge = '<span class="badge badge-success">LOGIN</span>';
        break;
    case 'logout':
        $action_badge = '<span class="badge badge-warning">LOGOUT</span>';
        break;
    case 'create':
        $action_badge = '<span class="badge badge-primary">CREATE</span>';
        break;
    case 'update':
        $action_badge = '<span class="badge badge-info">UPDATE</span>';
        break;
    case 'delete':
        $action_badge = '<span class="badge badge-danger">DELETE</span>';
        break;
    default:
        $action_badge = '<span class="badge badge-secondary">' . strtoupper($log['action']) . '</span>';
}
?>

<div class="container-fluid">
    <dl class="row">
        <dt class="col-sm-3">Log ID:</dt>
        <dd class="col-sm-9"><?php echo $log['id'] ?></dd>

        <dt class="col-sm-3">Date/Time:</dt>
        <dd class="col-sm-9"><?php echo date('F d, Y h:i A', strtotime($log['date_created'])) ?></dd>

        <dt class="col-sm-3">User:</dt>
        <dd class="col-sm-9">
            <?php echo $log['user_name'] ?> (<?php echo $log['username'] ?>) <?php echo $user_type_label ?>
        </dd>

        <dt class="col-sm-3">Action:</dt>
        <dd class="col-sm-9"><?php echo $action_badge ?></dd>

        <dt class="col-sm-3">Module:</dt>
        <dd class="col-sm-9">
            <?php echo !empty($log['module']) ? '<span class="badge badge-light">' . $log['module'] . '</span>' : '-' ?>
        </dd>

        <dt class="col-sm-3">Record ID:</dt>
        <dd class="col-sm-9"><?php echo !empty($log['record_id']) ? $log['record_id'] : '-' ?></dd>

        <dt class="col-sm-3">Description:</dt>
        <dd class="col-sm-9">
            <div class="alert alert-light">
                <?php echo $log['description'] ?>
            </div>
        </dd>

        <dt class="col-sm-3">Date Created:</dt>
        <dd class="col-sm-9"><?php echo date('F d, Y h:i:s A', strtotime($log['date_created'])) ?></dd>

        <dt class="col-sm-3">User Agent:</dt>
        <dd class="col-sm-9">
            <small class="text-muted"><?php echo $log['user_agent'] ?></small>
        </dd>
    </dl>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
