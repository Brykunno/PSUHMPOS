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
                    $n = $this->conn->real_escape_string($_POST['notes'][$k]);
                    $items_data .= ($items_data ? ", " : "") . "('{$oid}', '{$mid}', '{$p}', '{$q}', '{$n}')";
                }
    
                $sql2 = "INSERT INTO `order_items` (`order_id`, `menu_id`, `price`, `quantity`, `notes`) VALUES {$items_data}";
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
        $user_id = $this->settings->userdata('id');
        $reason_s = isset($reason) ? $this->conn->real_escape_string($reason) : 'Order Voided';

        // Get the order details first
        $order = $this->conn->query("SELECT * FROM `order_list` WHERE id = '". $this->conn->real_escape_string($id) ."'")->fetch_assoc();
        if(!$order){
            $resp['status'] = 'failed';
            $resp['error'] = 'Order not found';
            return json_encode($resp);
        }

        // Get all order items that are not archived
        $items = $this->conn->query("SELECT * FROM `order_items` WHERE order_id = '". $this->conn->real_escape_string($id) ."' AND archived = 0");
        if(!$items){
            $resp['status'] = 'failed';
            $resp['error'] = 'Failed to fetch order items';
            return json_encode($resp);
        }

        $this->conn->begin_transaction();

        $total_voided = 0;

        // Record each item in voids table
        while($item = $items->fetch_assoc()){
            $item_total = $item['price'] * $item['quantity'];
            $total_voided += $item_total;
            
            $ins = $this->conn->query("INSERT INTO `voids` (`order_id`,`order_item_id`,`menu_id`,`quantity`,`amount`,`reason`,`voided_by`,`voided_at`) 
                                      VALUES ('". $this->conn->real_escape_string($id) ."','". $this->conn->real_escape_string($item['id']) ."','". $this->conn->real_escape_string($item['menu_id']) ."',
                                              '". $this->conn->real_escape_string($item['quantity']) ."','". $this->conn->real_escape_string(number_format($item_total,2,'.','')) ."',
                                              '{$reason_s}','". $this->conn->real_escape_string($user_id) ."', CURRENT_TIMESTAMP())");
            
            if(!$ins){
                $this->conn->rollback();
                $resp['status'] = 'failed';
                $resp['error'] = 'Failed to record void: ' . $this->conn->error;
                return json_encode($resp);
            }

            // Mark item as archived
            $upd = $this->conn->query("UPDATE `order_items` SET `archived` = 1, `archived_by` = '". $this->conn->real_escape_string($user_id) ."', `archived_at` = CURRENT_TIMESTAMP() 
                                       WHERE id = '". $this->conn->real_escape_string($item['id']) ."'");
            if(!$upd){
                $this->conn->rollback();
                $resp['status'] = 'failed';
                $resp['error'] = 'Failed to archive item: ' . $this->conn->error;
                return json_encode($resp);
            }
        }

        // Recalculate order total (sum of non-archived items)
        $total_result = $this->conn->query("SELECT SUM(price * quantity) as new_total FROM `order_items` WHERE order_id = '". $this->conn->real_escape_string($id) ."' AND archived = 0");
        $new_total = 0;
        if($total_result){
            $total_row = $total_result->fetch_assoc();
            $new_total = floatval($total_row['new_total'] ?? 0);
        }

        // Update order total to reflect only non-archived items (keep status the same)
        // Don't change status - order remains visible in the list
        $upd_order = $this->conn->query("UPDATE `order_list` SET `total_amount` = '". $this->conn->real_escape_string(number_format($new_total, 2, '.', '')) ."', `voided_by` = '". $this->conn->real_escape_string($user_id) ."' WHERE id = '". $this->conn->real_escape_string($id) ."'");
        if(!$upd_order){
            $this->conn->rollback();
            $resp['status'] = 'failed';
            $resp['error'] = 'Failed to update order: ' . $this->conn->error;
            return json_encode($resp);
        }

        $this->conn->commit();

        $resp['status'] = 'success';
        $this->settings->set_flashdata('success'," Order has been voided successfully and recorded in void history.");
        return json_encode($resp);
    }

    function get_order(){
        extract($_POST);
        $swhere = "";
        if(isset($listed) && count($listed) > 0){
            $swhere = " and order_list.id not in (".implode(",", $listed).")";
        }
        $orders = $this->conn->query("SELECT order_list.id, order_list.queue, order_list.order_type, COALESCE(table_list.table_number, 'N/A') as table_number FROM `order_list` LEFT JOIN table_list ON order_list.table_id = table_list.id where order_list.status = 0 {$swhere} order by abs(unix_timestamp(order_list.date_created)) asc limit 10");
        $data = [];
        while($row = $orders->fetch_assoc()){
            $items = $this->conn->query("SELECT oi.*, concat(m.code, m.name) as `item`, oi.notes FROM `order_items` oi inner join menu_list m on oi.menu_id = m.id where order_id = '{$row['id']}' and oi.archived = 0");
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
        $user_id = $this->settings->userdata('id');
        $update = $this->conn->query("UPDATE `order_list` set `status` = 1 where id = '{$id}'");
        $this->conn->query("UPDATE order_items SET served = 1, served_by = '{$user_id}', served_at = CURRENT_TIMESTAMP() WHERE order_id = '{$id}'");
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
        $user_id = $this->settings->userdata('id');
        $update = $this->conn->query("UPDATE order_items SET served = 1, served_by = '{$user_id}', served_at = CURRENT_TIMESTAMP() WHERE id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function get_kitchen_serve_history(){
        extract($_POST);
        $swhere = "";
        if(isset($start_date) && isset($end_date)){
            $swhere .= " AND DATE(oi.served_at) BETWEEN '". $this->conn->real_escape_string($start_date) ."' AND '". $this->conn->real_escape_string($end_date) ."'";
        }
        if(isset($staff_id) && is_numeric($staff_id)){
            $swhere .= " AND oi.served_by = '".intval($staff_id)."'";
        }

        $qry = $this->conn->query("SELECT oi.id as oi_id, oi.order_id, ol.code as order_code, oi.menu_id, m.name as item_name, oi.quantity, oi.served_by, u.username as served_by_name, oi.served_at
            FROM order_items oi
            INNER JOIN order_list ol ON oi.order_id = ol.id
            INNER JOIN menu_list m ON oi.menu_id = m.id
            LEFT JOIN users u ON oi.served_by = u.id
            WHERE oi.served = 1 " . $swhere . " ORDER BY oi.served_at DESC LIMIT 1000");

        $data = [];
        while($row = $qry->fetch_assoc()){
            $data[] = $row;
        }
        return json_encode(['status'=>'success','data'=>$data]);
    }

    function get_void_history(){
        extract($_POST);
        $swhere = "";
        if(isset($start_date) && isset($end_date)){
            $swhere .= " AND DATE(v.voided_at) BETWEEN '". $this->conn->real_escape_string($start_date) ."' AND '". $this->conn->real_escape_string($end_date) ."'";
        }
        if(isset($staff_id) && is_numeric($staff_id)){
            $swhere .= " AND v.voided_by = '".intval($staff_id)."'";
        }

        $qry = $this->conn->query("SELECT v.id as vid, v.order_id, ol.code as order_code, v.order_item_id, v.menu_id, m.name as item_name, v.quantity, v.amount, v.reason, v.voided_by, u.username as voided_by_name, v.voided_at
            FROM voids v
            INNER JOIN order_list ol ON v.order_id = ol.id
            LEFT JOIN menu_list m ON v.menu_id = m.id
            LEFT JOIN users u ON v.voided_by = u.id
            WHERE 1=1 " . $swhere . " ORDER BY v.voided_at DESC LIMIT 1000");

        $data = [];
        while($row = $qry->fetch_assoc()){
            $data[] = $row;
        }
        return json_encode(['status'=>'success','data'=>$data]);
    }

    function void_item(){
        extract($_POST);
        $resp = ['status' => 'failed'];
        if(empty($order_item_id) || !is_numeric($order_item_id)){
            $resp['msg'] = 'Invalid order item.';
            return json_encode($resp);
        }

        $user_id = $this->settings->userdata('id');

        $item = $this->conn->query("SELECT * FROM `order_items` WHERE id = '". $this->conn->real_escape_string($order_item_id) ."'")->fetch_assoc();
        if(!$item){
            $resp['msg'] = 'Order item not found.';
            return json_encode($resp);
        }

        if(isset($item['archived']) && $item['archived'] == 1){
            $resp['msg'] = 'Item already archived.';
            return json_encode($resp);
        }

        $order_id = $item['order_id'];
        $menu_id = $item['menu_id'];
        $available_qty = isset($item['quantity']) ? intval($item['quantity']) : 1;
        $vqty = isset($quantity) && is_numeric($quantity) ? intval($quantity) : $available_qty;
        if($vqty <= 0 || $vqty > $available_qty){
            $resp['msg'] = 'Invalid quantity.';
            return json_encode($resp);
        }

        $price = isset($item['price']) ? floatval($item['price']) : 0;
        $amount = $price * $vqty;
        $reason_s = isset($reason) ? $this->conn->real_escape_string($reason) : 'Voided';

        $this->conn->begin_transaction();

        // Insert into voids
        $ins = $this->conn->query("INSERT INTO `voids` (`order_id`,`order_item_id`,`menu_id`,`quantity`,`amount`,`reason`,`voided_by`,`voided_at`) VALUES ('". $this->conn->real_escape_string($order_id) ."','". $this->conn->real_escape_string($order_item_id) ."','". $this->conn->real_escape_string($menu_id) ."','". $this->conn->real_escape_string($vqty) ."','". $this->conn->real_escape_string(number_format($amount,2,'.','')) ."','{$reason_s}','". $this->conn->real_escape_string($user_id) ."', CURRENT_TIMESTAMP())");
        if(!$ins){
            $this->conn->rollback();
            $resp['msg'] = 'Failed to record void.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        $void_id = $this->conn->insert_id;

        // Update order_items to mark archived
        $u = $this->conn->query("UPDATE `order_items` SET `archived` = 1, `archived_by` = '". $this->conn->real_escape_string($user_id) ."', `archived_at` = CURRENT_TIMESTAMP() WHERE id = '". $this->conn->real_escape_string($order_item_id) ."'");
        if(!$u){
            $this->conn->rollback();
            $resp['msg'] = 'Failed to archive item.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        // Subtract amount from order total
        $upd = $this->conn->query("UPDATE `order_list` SET `total_amount` = total_amount - '". $this->conn->real_escape_string(number_format($amount,2,'.','')) ."' WHERE id = '". $this->conn->real_escape_string($order_id) ."'");
        if(!$upd){
            $this->conn->rollback();
            $resp['msg'] = 'Failed to update order total.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        $this->conn->commit();

        $resp['status'] = 'success';
        $resp['vid'] = $void_id;
        $resp['msg'] = 'Item archived (voided) successfully.';
        return json_encode($resp);
    }

    function restore_void_item(){
        extract($_POST);
        $resp = ['status' => 'failed'];
        
        if(empty($void_id) || !is_numeric($void_id)){
            $resp['msg'] = 'Invalid void record.';
            return json_encode($resp);
        }

        $user_id = $this->settings->userdata('id');

        // Get void record details
        $void_record = $this->conn->query("SELECT * FROM `voids` WHERE id = '". $this->conn->real_escape_string($void_id) ."'")->fetch_assoc();
        if(!$void_record){
            $resp['msg'] = 'Void record not found.';
            return json_encode($resp);
        }

        $order_id = $void_record['order_id'];
        $order_item_id = $void_record['order_item_id'];
        $amount = floatval($void_record['amount']);

        // Check if order is still pending (not yet served/completed)
        $order = $this->conn->query("SELECT * FROM `order_list` WHERE id = '". $this->conn->real_escape_string($order_id) ."'")->fetch_assoc();
        if(!$order){
            $resp['msg'] = 'Order not found.';
            return json_encode($resp);
        }

        // Only allow restore if order is not completed/paid
        if($order['status'] == 'Completed' || $order['status'] == 'Paid'){
            $resp['msg'] = 'Cannot restore items from completed/paid orders.';
            return json_encode($resp);
        }

        $this->conn->begin_transaction();

        // Un-archive the order item
        $restore = $this->conn->query("UPDATE `order_items` SET `archived` = 0, `archived_by` = NULL, `archived_at` = NULL WHERE id = '". $this->conn->real_escape_string($order_item_id) ."'");
        if(!$restore){
            $this->conn->rollback();
            $resp['msg'] = 'Failed to restore item.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        // Delete from voids table
        $del = $this->conn->query("DELETE FROM `voids` WHERE id = '". $this->conn->real_escape_string($void_id) ."'");
        if(!$del){
            $this->conn->rollback();
            $resp['msg'] = 'Failed to remove void record.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        // Add amount back to order total
        $upd = $this->conn->query("UPDATE `order_list` SET `total_amount` = total_amount + '". $this->conn->real_escape_string(number_format($amount,2,'.','')) ."' WHERE id = '". $this->conn->real_escape_string($order_id) ."'");
        if(!$upd){
            $this->conn->rollback();
            $resp['msg'] = 'Failed to update order total.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        $this->conn->commit();

        $resp['status'] = 'success';
        $resp['msg'] = 'Item restored to order successfully.';
        return json_encode($resp);
    }

    function get_voided_orders(){
        $resp = ['status' => 'failed', 'data' => []];
        
        // Get all orders with voided items (voided_by is NOT NULL means entire order was voided)
        $sql = "SELECT 
                    ol.id,
                    ol.code,
                    ol.date_created,
                    ol.voided_by
                FROM `order_list` ol
                WHERE ol.voided_by IS NOT NULL
                ORDER BY ol.date_created DESC";
        
        $query = $this->conn->query($sql);
        
        if(!$query){
            $resp['msg'] = 'Failed to fetch voided orders.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }
        
        $data = [];
        while($row = $query->fetch_assoc()){
            $order_id = $row['id'];
            
            // Count voided items and get total voided amount from voids table
            $void_count = 0;
            $void_total = 0;
            $void_stats = $this->conn->query("SELECT COUNT(id) as cnt, SUM(amount) as total FROM `voids` WHERE order_id = '{$order_id}'");
            if($void_stats){
                $void_row = $void_stats->fetch_assoc();
                $void_count = $void_row['cnt'];
                $void_total = floatval($void_row['total'] ?? 0);
            }
            
            // Get user name (from order_list.voided_by first, fallback to voids table)
            $voided_by_name = 'System';
            $voided_by_id = $row['voided_by'];
            
            // If voided_by is null in order_list, try to get from voids table
            if(!$voided_by_id){
                $void_result = $this->conn->query("SELECT voided_by FROM `voids` WHERE order_id = '{$order_id}' LIMIT 1");
                if($void_result){
                    $void_row = $void_result->fetch_assoc();
                    if($void_row && isset($void_row['voided_by'])){
                        $voided_by_id = $void_row['voided_by'];
                    }
                }
            }
            
            // Get user details
            if($voided_by_id){
                $user_result = $this->conn->query("SELECT firstname, lastname FROM `users` WHERE id = '{$voided_by_id}'");
                if($user_result){
                    $user_row = $user_result->fetch_assoc();
                    if($user_row){
                        $voided_by_name = $user_row['firstname'] . ' ' . $user_row['lastname'];
                    }
                }
            }
            
            $data[] = [
                'id' => $row['id'],
                'code' => $row['code'],
                'date_created' => date('M d, Y h:i A', strtotime($row['date_created'])),
                'item_count' => $void_count,
                'total_amount' => $void_total,  // Return as float, not formatted string
                'voided_by_name' => $voided_by_name
            ];
        }
        
        $resp['status'] = 'success';
        $resp['data'] = $data;
        return json_encode($resp);
    }

    function restore_order(){
        extract($_POST);
        $resp = ['status' => 'failed'];
        
        if(empty($order_id) || !is_numeric($order_id)){
            $resp['msg'] = 'Invalid order.';
            return json_encode($resp);
        }

        $user_id = $this->settings->userdata('id');

        // Get order details
        $order = $this->conn->query("SELECT * FROM `order_list` WHERE id = '". $this->conn->real_escape_string($order_id) ."'")->fetch_assoc();
        if(!$order){
            $resp['msg'] = 'Order not found.';
            return json_encode($resp);
        }

        // Check if order has voided items to restore
        $void_check = $this->conn->query("SELECT COUNT(id) as cnt FROM `voids` WHERE order_id = '". $this->conn->real_escape_string($order_id) ."'");
        $void_count = 0;
        if($void_check){
            $void_check_row = $void_check->fetch_assoc();
            $void_count = intval($void_check_row['cnt']);
        }
        
        if($void_count == 0){
            $resp['msg'] = 'This order has no voided items to restore.';
            return json_encode($resp);
        }

        // Get all void records for this order
        $voids = $this->conn->query("SELECT * FROM `voids` WHERE order_id = '". $this->conn->real_escape_string($order_id) ."'");
        if(!$voids){
            $resp['msg'] = 'Failed to fetch void records.';
            return json_encode($resp);
        }

        $this->conn->begin_transaction();

        $total_restored = 0;
        $void_ids = [];

        // Process each void record
        while($void = $voids->fetch_assoc()){
            $void_ids[] = $void['id'];
            $total_restored += floatval($void['amount']);

            // Un-archive the order item
            $upd = $this->conn->query("UPDATE `order_items` SET `archived` = 0, `archived_by` = NULL, `archived_at` = NULL WHERE id = '". $this->conn->real_escape_string($void['order_item_id']) ."'");
            if(!$upd){
                $this->conn->rollback();
                $resp['msg'] = 'Failed to restore item.';
                $resp['error'] = $this->conn->error;
                return json_encode($resp);
            }
        }

        // Delete all void records for this order
        if(!empty($void_ids)){
            $void_ids_str = implode(',', $void_ids);
            $del = $this->conn->query("DELETE FROM `voids` WHERE id IN ({$void_ids_str})");
            if(!$del){
                $this->conn->rollback();
                $resp['msg'] = 'Failed to remove void records.';
                $resp['error'] = $this->conn->error;
                return json_encode($resp);
            }
        }

        // Calculate new total from all non-archived items
        $total_calc = $this->conn->query("SELECT SUM(price * quantity) as calculated_total FROM `order_items` WHERE order_id = '". $this->conn->real_escape_string($order_id) ."' AND archived = 0");
        $calculated_total = 0;
        if($total_calc){
            $calc_row = $total_calc->fetch_assoc();
            $calculated_total = floatval($calc_row['calculated_total'] ?? 0);
        }

        // Update order total with calculated amount (keep existing status)
        $upd_order = $this->conn->query("UPDATE `order_list` SET `total_amount` = '". $this->conn->real_escape_string(number_format($calculated_total,2,'.','')) ."', `voided_by` = NULL WHERE id = '". $this->conn->real_escape_string($order_id) ."'");
        if(!$upd_order){
            $this->conn->rollback();
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to restore order.';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }

        $this->conn->commit();

        $resp['status'] = 'success';
        $resp['msg'] = 'Order restored successfully.';
        return json_encode($resp);
    }

    function refund_order(){
        extract($_POST);
        $resp = ['status' => 'failed'];
        
        // Validate required fields
        if(empty($order_id)){
            $resp['msg'] = 'Order ID is required.';
            return json_encode($resp);
        }
        
        // Fetch order details
        $order = $this->conn->query("SELECT * FROM `order_list` WHERE id = '{$order_id}'")->fetch_assoc();
        
        if(!$order){
            $resp['msg'] = 'Order not found.';
            return json_encode($resp);
        }
        
        // Only paid orders can be refunded (status = 2) or Paid with Refunded Items (status = 5)
        if(!in_array((int)$order['status'], [2,5])){
            $resp['msg'] = 'Only paid orders can be refunded.';
            return json_encode($resp);
        }
        
        // Check if this is item-level or full order refund
        $refund_items = isset($refund_items) ? json_decode($refund_items, true) : [];
        $is_item_refund = !empty($refund_items);
        
        // Get current user ID
        $user_id = $this->settings->userdata('id');
        
        // Escape reason
        $reason = !empty($refund_reason) ? $this->conn->real_escape_string($refund_reason) : '';
        
        // Begin transaction
        $this->conn->begin_transaction();
        
        try {
            $total_refund_amount = 0;
            $refunded_item_count = 0;
            
            if($is_item_refund){
                // Item-level refund
                foreach($refund_items as $item_id){
                    // Get item details
                    $item = $this->conn->query("SELECT * FROM `order_items` WHERE id = '{$item_id}' AND order_id = '{$order_id}'")->fetch_assoc();
                    
                    if(!$item){
                        throw new Exception("Item ID {$item_id} not found in this order.");
                    }
                    
                    if($item['refunded'] == 1){
                        throw new Exception("Item has already been refunded.");
                    }
                    
                    $item_total = floatval($item['price']) * intval($item['quantity']);
                    $total_refund_amount += $item_total;
                    $refunded_item_count++;
                    
                    // Insert refund record for this item
                    $insert_refund = $this->conn->query("INSERT INTO `refunds` 
                        (`order_id`, `item_id`, `amount`, `reason`, `user_id`, `date_created`) 
                        VALUES ('{$order_id}', '{$item_id}', '{$item_total}', '{$reason}', '{$user_id}', CURRENT_TIMESTAMP())");
                    
                    if(!$insert_refund){
                        throw new Exception($this->conn->error);
                    }
                    
                    // Mark item as refunded
                    $update_item = $this->conn->query("UPDATE `order_items` SET `refunded` = 1 WHERE id = '{$item_id}'");
                    
                    if(!$update_item){
                        throw new Exception($this->conn->error);
                    }
                }
                
                // Check if all items are now refunded
                $remaining_items = $this->conn->query("SELECT COUNT(*) as count FROM `order_items` WHERE order_id = '{$order_id}' AND refunded = 0")->fetch_assoc();
                
                if($remaining_items['count'] == 0){
                    // All items refunded, mark entire order as refunded
                    $update_order = $this->conn->query("UPDATE `order_list` 
                        SET `status` = 4, `date_updated` = CURRENT_TIMESTAMP() 
                        WHERE id = '{$order_id}'");
                } else {
                    // Partial refund, mark order as 'Paid with Refunded Items' (status = 5)
                    $update_order = $this->conn->query("UPDATE `order_list` 
                        SET `status` = 5, `date_updated` = CURRENT_TIMESTAMP() 
                        WHERE id = '{$order_id}'");
                }
                
                if(!$update_order){
                    throw new Exception($this->conn->error);
                }
                
            } else {
                // Full order refund (legacy support)
                $refund_amount = !empty($amount) ? floatval($amount) : floatval($order['total_amount']);
                
                // Validate refund amount
                if($refund_amount <= 0){
                    throw new Exception('Invalid refund amount.');
                }
                
                if($refund_amount > floatval($order['total_amount'])){
                    throw new Exception('Refund amount cannot exceed order total.');
                }
                
                $total_refund_amount = $refund_amount;
                
                // Insert refund record (item_id is NULL for full order refund)
                $insert_refund = $this->conn->query("INSERT INTO `refunds` 
                    (`order_id`, `item_id`, `amount`, `reason`, `user_id`, `date_created`) 
                    VALUES ('{$order_id}', NULL, '{$refund_amount}', '{$reason}', '{$user_id}', CURRENT_TIMESTAMP())");
                
                if(!$insert_refund){
                    throw new Exception($this->conn->error);
                }
                
                // Update order status to Refunded (4)
                $update_order = $this->conn->query("UPDATE `order_list` 
                    SET `status` = 4, `date_updated` = CURRENT_TIMESTAMP() 
                    WHERE id = '{$order_id}'");
                
                if(!$update_order){
                    throw new Exception($this->conn->error);
                }
                
                // Mark all items as refunded
                $update_items = $this->conn->query("UPDATE `order_items` SET `refunded` = 1 WHERE order_id = '{$order_id}'");
                
                if(!$update_items){
                    throw new Exception($this->conn->error);
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            
            $resp['status'] = 'success';
            $resp['msg'] = $is_item_refund ? 
                "Successfully refunded {$refunded_item_count} item(s)." : 
                'Order refunded successfully.';
            $resp['refund_amount'] = number_format($total_refund_amount, 2);
            $resp['is_item_refund'] = $is_item_refund;
            $resp['refunded_count'] = $refunded_item_count;
            
        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollback();
            $resp['status'] = 'failed';
            $resp['msg'] = 'Refund failed: ' . $e->getMessage();
        }
        
        return json_encode($resp);
    }

    function get_order_items_for_refund(){
        extract($_POST);
        $resp = ['status' => 'failed'];
        
        if(empty($order_id)){
            $resp['msg'] = 'Order ID is required.';
            return json_encode($resp);
        }
        
        $items = $this->conn->query("SELECT oi.*, CONCAT(m.code, ' ', m.name) as item_name 
            FROM `order_items` oi 
            INNER JOIN `menu_list` m ON oi.menu_id = m.id 
            WHERE oi.order_id = '{$order_id}' 
            ORDER BY oi.id ASC");
        
        if($items && $items->num_rows > 0){
            $item_list = [];
            while($row = $items->fetch_assoc()){
                $item_list[] = $row;
            }
            $resp['status'] = 'success';
            $resp['items'] = $item_list;
        } else {
            $resp['msg'] = 'No items found for this order.';
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
    case 'get_kitchen_serve_history':
        echo $Master->get_kitchen_serve_history();
        break;
    case 'refund_order':
        echo $Master->refund_order();
        break;
    case 'get_order_items_for_refund':
        echo $Master->get_order_items_for_refund();
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
    case 'get_void_history':
        echo $Master->get_void_history();
        break;
    case 'void_item':
        echo $Master->void_item();
        break;
    case 'restore_void_item':
        echo $Master->restore_void_item();
        break;
    case 'get_voided_orders':
        echo $Master->get_voided_orders();
        break;
    case 'restore_order':
        echo $Master->restore_order();
        break;

    default:
        // Return a JSON error for unknown dispatcher actions to avoid empty responses
        header('Content-Type: application/json');
        echo json_encode(['status' => 'failed', 'msg' => 'Invalid action specified.']);
        break;
}

