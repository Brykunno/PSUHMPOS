<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../config.php');
Class Master extends DBConnection {
    private $settings;
    
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    
    public function __destruct(){
        parent::__destruct();
    }

    function capture_err(){
        if(!$this->conn->error)
            return false;
        else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
            exit;
        }
    }

    function delete_img(){
        extract($_POST);
        if(is_file($path)){
            if(unlink($path)){
                $resp['status'] = 'success';
            }else{
                $resp['status'] = 'failed';
                $resp['error'] = 'failed to delete '.$path;
            }
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = 'Unknown '.$path.' path';
        }
        return json_encode($resp);
    }

    function save_category(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .=","; 
                $v = htmlspecialchars($this->conn->real_escape_string($v)); 
                $data .= " `{$k}`='{$v}' "; 
            }
        }

        $check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Category already exists.";
            return json_encode($resp);
            exit;
        }

        if(empty($id)){
            $sql = "INSERT INTO `category_list` set {$data} ";
        }else{
            $sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
        }
        
        $save = $this->conn->query($sql);
        if($save){
            $cid = !empty($id) ? $id : $this->conn->insert_id;
            $resp['cid'] = $cid;
            $resp['status'] = 'success';
            $resp['msg'] = empty($id) ? "New Category successfully saved." : " Category successfully updated.";
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }

    function delete_category(){
        extract($_POST);
        $del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success'," Category successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function save_menu(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id','existing_image'))){
                if(!empty($data)) $data .=","; 
                $v = htmlspecialchars($this->conn->real_escape_string($v)); 
                $data .= " `{$k}`='{$v}' "; 
            }
        }
    
        // Image handling
        if(isset($_FILES['image']) && $_FILES['image']['tmp_name'] != ''){
            $img_dir = '../uploads/image/';
            $filename = time().'_'.preg_replace("/[^A-Za-z0-9_\-\.]/", '_', $_FILES['image']['name']);
            $full_path = $img_dir . $filename;
    
            // Ensure folder exists
            if (!is_dir($img_dir)) {
                mkdir($img_dir, 0777, true);
            }
    
            // Check if file upload is successful
            if(move_uploaded_file($_FILES['image']['tmp_name'], $full_path)){
                $data .= ", `image_path`='{$filename}'"; // Use `image_path` instead of `image`
                
                // Delete old image if it exists
                if(!empty($_POST['existing_image']) && is_file($img_dir.$_POST['existing_image'])){
                    unlink($img_dir.$_POST['existing_image']);
                }
            } else {
                $resp['status'] = 'failed';
                $resp['error'] = 'Failed to upload image.';
                return json_encode($resp);
            }
        }
    
        // Check for duplicate code
        $check = $this->conn->query("SELECT * FROM `menu_list` where `code` = '{$code}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err()) return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Menu Code already exists.";
            return json_encode($resp);
            exit;
        }
    
        // Save or Update menu item
        if(empty($id)){
            $sql = "INSERT INTO `menu_list` set {$data} ";
        }else{
            $sql = "UPDATE `menu_list` set {$data} where id = '{$id}' ";
        }
        $save = $this->conn->query($sql);
        if($save){
            $iid = !empty($id) ? $id : $this->conn->insert_id;
            $resp['iid'] = $iid;
            $resp['status'] = 'success';
            $resp['msg'] = empty($id) ? "New Menu successfully saved." : "Menu successfully updated.";
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
 
    function save_discount(){
        extract($_POST);
        $resp = [];
    
        // Validate required fields
        if(empty($name) || empty($percentage)){
            return json_encode(['status' => 'failed', 'msg' => 'Required fields are missing.']);
        }
    
        // Ensure percentage is within valid range
        $percentage = floatval($percentage);
        if($percentage < 0 || $percentage > 100){
            return json_encode(['status' => 'failed', 'msg' => 'Percentage must be between 0 and 100.']);
        }
    
        $status = isset($status) ? intval($status) : 0;
    
        // Check for duplicate discount name
        $stmt = $this->conn->prepare("SELECT id FROM `discount_list` WHERE `name` = ? AND `delete_flag` = 0" . (!empty($id) ? " AND id != ?" : ""));
        if (!empty($id)) {
            $stmt->bind_param("si", $name, $id);
        } else {
            $stmt->bind_param("s", $name);
        }
        $stmt->execute();
        $check = $stmt->get_result()->num_rows;
        $stmt->close();
    
        if ($check > 0) {
            return json_encode(['status' => 'failed', 'msg' => "Discount name already exists."]);
        }
    
        // Save or update discount
        if (empty($id)) {
            $stmt = $this->conn->prepare("INSERT INTO `discount_list` (`name`, `percentage`, `status`) VALUES (?, ?, ?)");
            $stmt->bind_param("sdi", $name, $percentage, $status);
        } else {
            $stmt = $this->conn->prepare("UPDATE `discount_list` SET `name` = ?, `percentage` = ?, `status` = ? WHERE id = ?");
            $stmt->bind_param("sdii", $name, $percentage, $status, $id);
        }
    
        $save = $stmt->execute();
        $stmt->close();
    
        return json_encode([
            'status' => $save ? 'success' : 'failed',
            'cid' => $save ? (empty($id) ? $this->conn->insert_id : $id) : null,
            'msg' => $save ? "Discount successfully saved." : "SQL Error: ".$this->conn->error
        ]);
    }
    
    function delete_discount(){
        extract($_POST);
        
        // Validate ID
        if(empty($id) || !is_numeric($id)){
            return json_encode(['status' => 'failed', 'msg' => 'Invalid discount ID.']);
        }
    
        // Use prepared statement for security
        $stmt = $this->conn->prepare("UPDATE `discount_list` SET `delete_flag` = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $delete = $stmt->execute();
        $stmt->close();
    
        return json_encode([
            'status' => $delete ? 'success' : 'failed',
            'msg' => $delete ? "Discount successfully deleted." : "An error occurred: ".$this->conn->error
        ]);
    }
    


    function delete_menu(){
        extract($_POST);
        $del = $this->conn->query("UPDATE `menu_list` set `delete_flag` = 1 where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success'," Menu successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    public function place_order() {
        $prefix = date("Ymd");
        $num = 1;
        while (true) {
            $code = sprintf("%'.05d", $num);
            $full_code = $prefix . $code;
            $check = $this->conn->query("SELECT * FROM `order_list` WHERE code = '{$full_code}'")->num_rows;
            if ($check > 0) {
                $num++;
            } else {
                $_POST['code'] = $full_code;
                $_POST['queue'] = $code;
                break;
            }
        }
    
        $_POST['user_id'] = $this->settings->userdata('id');
        $_POST['status'] = 0;
        $_POST['date_created'] = date("Y-m-d H:i:s");
        $_POST['date_updated'] = date("Y-m-d H:i:s");
    
        $required_fields = [
            'code', 'queue', 'total_amount', 'tendered_amount','table_id',
            'user_id', 'order_type', 'discount_type', 
            'status', 'date_created', 'date_updated'
        ];
        
        $data = "";
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field])) {
                return json_encode([
                    'status' => 'failed', 
                    'msg' => "Missing required field: {$field}"
                ]);
            }
            $val = $this->conn->real_escape_string($_POST[$field]);
            $data .= ($data ? ", " : "") . " `{$field}` = '{$val}' ";
        }
    
        $sql = "INSERT INTO `order_list` SET {$data}";
        error_log("SQL Order List: $sql");
    
        $save = $this->conn->query($sql);
        $resp = [];
    
        if ($save) {
            $oid = $this->conn->insert_id;
            $resp['oid'] = $oid;
    
            if (isset($_POST['menu_id'], $_POST['price'], $_POST['quantity']) &&
                is_array($_POST['menu_id']) && is_array($_POST['price']) && is_array($_POST['quantity'])) {
                
                $items_data = "";
                foreach ($_POST['menu_id'] as $k => $menu_id) {
                    $mid = $this->conn->real_escape_string($menu_id);
                    $p = $this->conn->real_escape_string($_POST['price'][$k]);
                    $q = $this->conn->real_escape_string($_POST['quantity'][$k]);
                    $items_data .= ($items_data ? ", " : "") . "('{$oid}', '{$mid}', '{$p}', '{$q}')";
                }
    
                $sql2 = "INSERT INTO `order_items` (`order_id`, `menu_id`, `price`, `quantity`) VALUES {$items_data}";
                error_log("SQL Order Items: $sql2");
    
                $save2 = $this->conn->query($sql2);
                if ($save2) {
                    $resp['status'] = 'success';
                    $resp['msg'] = 'Order placed successfully.';
                } else {
                    // Rollback: delete the order header if items fail
                    $this->conn->query("DELETE FROM `order_list` WHERE id = '{$oid}'");
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Order items failed to insert.';
                    $resp['error'] = $this->conn->error;
                }
            } else {
                // Rollback: missing items data
                $this->conn->query("DELETE FROM `order_list` WHERE id = '{$oid}'");
                $resp['status'] = 'failed';
                $resp['msg'] = 'Order item details missing or invalid.';
            }
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Order insert failed.';
            $resp['error'] = $this->conn->error;
        }
    
        return json_encode($resp);
    }


        public function bill_out() {
        $prefix = date("Ymd");
        $num = 1;
        while (true) {
            $code = sprintf("%'.05d", $num);
            $full_code = $prefix . $code;
            $check = $this->conn->query("SELECT * FROM `order_list` WHERE code = '{$full_code}'")->num_rows;
            if ($check > 0) {
                $num++;
            } else {
                $_POST['code'] = $full_code;
                $_POST['queue'] = $code;
                break;
            }
        }
    
        $_POST['user_id'] = $this->settings->userdata('id');
        $_POST['status'] = 0;
        $_POST['date_created'] = date("Y-m-d H:i:s");
        $_POST['date_updated'] = date("Y-m-d H:i:s");
    
        $required_fields = [
            'code', 'queue', 'total_amount', 'tendered_amount', 
            'user_id', 'order_type', 'discount_type', 
            'status', 'date_created', 'date_updated'
        ];
        
        $data = "";
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field])) {
                return json_encode([
                    'status' => 'failed', 
                    'msg' => "Missing required field: {$field}"
                ]);
            }
            $val = $this->conn->real_escape_string($_POST[$field]);
            $data .= ($data ? ", " : "") . " `{$field}` = '{$val}' ";
        }
    
        $sql = "INSERT INTO `order_list` SET {$data}";
        error_log("SQL Order List: $sql");
    
        $save = $this->conn->query($sql);
        $resp = [];
    
        if ($save) {
            $oid = $this->conn->insert_id;
            $resp['oid'] = $oid;
    
            if (isset($_POST['menu_id'], $_POST['price'], $_POST['quantity']) &&
                is_array($_POST['menu_id']) && is_array($_POST['price']) && is_array($_POST['quantity'])) {
                
                $items_data = "";
                foreach ($_POST['menu_id'] as $k => $menu_id) {
                    $mid = $this->conn->real_escape_string($menu_id);
                    $p = $this->conn->real_escape_string($_POST['price'][$k]);
                    $q = $this->conn->real_escape_string($_POST['quantity'][$k]);
                    $items_data .= ($items_data ? ", " : "") . "('{$oid}', '{$mid}', '{$p}', '{$q}')";
                }
    
                $sql2 = "INSERT INTO `order_items` (`order_id`, `menu_id`, `price`, `quantity`) VALUES {$items_data}";
                error_log("SQL Order Items: $sql2");
    
                $save2 = $this->conn->query($sql2);
                if ($save2) {
                    $resp['status'] = 'success';
                    $resp['msg'] = 'Order placed successfully.';
                } else {
                    // Rollback: delete the order header if items fail
                    $this->conn->query("DELETE FROM `order_list` WHERE id = '{$oid}'");
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Order items failed to insert.';
                    $resp['error'] = $this->conn->error;
                }
            } else {
                // Rollback: missing items data
                $this->conn->query("DELETE FROM `order_list` WHERE id = '{$oid}'");
                $resp['status'] = 'failed';
                $resp['msg'] = 'Order item details missing or invalid.';
            }
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Order insert failed.';
            $resp['error'] = $this->conn->error;
        }
    
        return json_encode($resp);
    }
    

    function delete_order(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `order_list` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success'," Order has been deleted successfully.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function get_order(){
        extract($_POST);
        $swhere = "";
        if(isset($listed) && count($listed) > 0){
            $swhere = " and order_list.id not in (".implode(",", $listed).")";
        }
        $orders = $this->conn->query("SELECT order_list.id, order_list.queue, order_list.order_type,table_list.table_number FROM `order_list` INNER JOIN table_list ON order_list.table_id = table_list.id where order_list.status = 0 {$swhere} order by abs(unix_timestamp(order_list.date_created)) asc limit 10");
        $data = [];
        while($row = $orders->fetch_assoc()){
            $items = $this->conn->query("SELECT oi.*, concat(m.code, m.name) as `item` FROM `order_items` oi inner join menu_list m on oi.menu_id = m.id where order_id = '{$row['id']}'");
            $item_arr = [];
            while($irow = $items->fetch_assoc()){
                $item_arr[] = $irow;
            }
            $row['item_arr'] = $item_arr;
            $data[] = $row;
        }
        $resp['status'] = 'success';
        $resp['data'] = $data;
        return json_encode($resp);
    }
        
    function serve_order(){
        extract($_POST);
        $update = $this->conn->query("UPDATE `order_list` set `status` = 1 where id = '{$id}'");
        $this->conn->query("UPDATE order_items SET served = 1 WHERE order_id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }


    function serve_one_order(){
        extract($_POST);
        $update = $this->conn->query("UPDATE order_items SET served = 1 WHERE id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    
function save_table(){
    extract($_POST);
    $data = "";
    foreach($_POST as $k =>$v){
        if(!in_array($k,array('id'))){
            if(!empty($data)) $data .=",";
            $data .= " `{$k}`='{$v}' ";
        }
    }
    $check = $this->conn->query("SELECT * FROM `table_list` where `table_number` = '{$table_number}' ".(!empty($id) ? " and id != {$id} " : "")." and delete_flag = 0 ")->num_rows;
    if($this->conn->error){
        $resp['status'] = 'failed';
        $resp['msg'] = "An error occurred.";
        $resp['error'] = $this->conn->error;
    }elseif($check > 0){
        $resp['status'] = 'failed';
        $resp['msg'] = "Table Number already exists.";
    }else{
        if(empty($id)){
            $sql = "INSERT INTO `table_list` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `table_list` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Table successfully saved.");
            else
                $this->settings->set_flashdata('success',"Table successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "An error occurred.";
            $resp['error'] = $this->conn->error;
        }
    }
    return json_encode($resp);
}

function delete_table(){
    extract($_POST);
    $del = $this->conn->query("UPDATE `table_list` set `delete_flag` = 1 where id = '{$id}'");
    if($del){
        $resp['status'] = 'success';
        $this->settings->set_flashdata('success',"Table successfully deleted.");
    }else{
        $resp['status'] = 'failed';
        $resp['msg'] = "An error occurred.";
        $resp['error'] = $this->conn->error;
    }
    return json_encode($resp);
}

function get_table(){
    extract($_POST);
    $qry = $this->conn->query("SELECT * FROM `table_list` where id = '{$id}' and delete_flag = 0");
    if($qry->num_rows > 0){
        $resp['status'] = 'success';
        $resp['data'] = $qry->fetch_assoc();
    }else{
        $resp['status'] = 'failed';
        $resp['msg'] = "Table not found.";
    }
    return json_encode($resp);
}

function update_table_status(){
    extract($_POST);
    $update = $this->conn->query("UPDATE `table_list` set `status` = '{$status}' where id = '{$id}'");
    if($update){
        $resp['status'] = 'success';
        $resp['msg'] = "Table status updated successfully.";
    }else{
        $resp['status'] = 'failed';
        $resp['msg'] = "An error occurred.";
        $resp['error'] = $this->conn->error;
    }
    return json_encode($resp);
}
}




$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();

switch ($action) {
    case 'delete_img':
        echo $Master->delete_img();
        break;
    case 'save_category':
        echo $Master->save_category();
        break;
    case 'delete_category':
        echo $Master->delete_category();
        break;
    case 'save_menu':
        echo $Master->save_menu();
        break;
    case 'delete_menu':
        echo $Master->delete_menu();
        break;
    case 'place_order':
        echo $Master->place_order();
        break;
    case 'delete_order':
        echo $Master->delete_order();
        break;
    case 'get_order':
        echo $Master->get_order();
        break;
    case 'serve_order':
        echo $Master->serve_order();
        break;
    case 'serve_one_order':
        echo $Master->serve_one_order();
        break;
    case 'save_table':
        echo $Master->save_table();
        break;
    case 'delete_table':
        echo $Master->delete_table();
        break;
    case 'get_table':
        echo $Master->get_table();
        break;
    case 'update_table_status':
        echo $Master->update_table_status();
        break;

    default:
        // echo $sysset->index();
        break;
}

