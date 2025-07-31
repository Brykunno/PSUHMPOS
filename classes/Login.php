<?php
require_once '../config.php';

class Login extends DBConnection {
    private $settings;

    public function __construct() {
        global $_settings;
        $this->settings = $_settings;

        parent::__construct();
        ini_set('display_error', 1);
    }

    public function __destruct() {
        parent::__destruct();
    }

    public function index() {
        echo "<h1>Access Denied</h1> <a href='" . base_url . "'>Go Back.</a>";
    }

    public function login() {
        extract($_POST);
    
        // Prepare and execute query to validate login
        $stmt = $this->conn->prepare("SELECT * from users where username = ? and password = ?");
        $password = md5($password); // Encrypt password
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Fetch once and reuse
    
            // Store session data
            foreach ($user as $k => $v) {
                if ($k !== 'password') {
                    $this->settings->set_userdata($k, $v);
                }
            }
    
            $this->settings->set_userdata('login_type', 1); // If needed
    
            // Return success response including the user's role/type
            return json_encode([
                'status' => 'success',
                'redirect' => 'admin/dashboard.php',
                'role' => $user['type'] // or whatever field represents the role
            ]);
        } else {
            return json_encode([
                'status' => 'incorrect',
                'last_qry' => "SELECT * from users where username = '$username' and password = md5('$password') "
            ]);
        }
    }
    
   public function logout() {
    session_start();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Optionally destroy settings session data
    if (isset($this->settings)) {
        $this->settings->sess_des(); // If it does something meaningful
    }

    header("Location: ../admin/login.php");
    exit;
}


    public function login_customer() {
        extract($_POST);

        // Prepare and execute query to validate customer login
        $stmt = $this->conn->prepare("SELECT * from customer_list where email = ? and `password` = ?");
        $password = md5($password); // Encrypt password
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Store session data for customer user
            $res = $result->fetch_array();
            foreach ($res as $k => $v) {
                $this->settings->set_userdata($k, $v);
            }
            $this->settings->set_userdata('login_type', 2);

            // Return success response and redirect URL for customer dashboard
            return json_encode(array('status' => 'success', 'redirect' => 'customer/dashboard.php'));
        } else {
            return json_encode(array('status' => 'failed', 'msg' => 'Incorrect Email or Password'));
        }
    }

    public function logout_customer() {
        if ($this->settings->sess_des()) {
            redirect('?');
        }
    }
}

$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();

switch ($action) {
    case 'login':
        echo $auth->login();
        break;
    case 'logout':
        echo $auth->logout();
        break;
    case 'login_customer':
        echo $auth->login_customer();
        break;
    case 'logout_customer':
        echo $auth->logout_customer();
        break;
    default:
        echo $auth->index();
        break;
}
