<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>

<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: url("../assets/img/background.png") no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: flex-start;
      justify-content: flex-start;
      height: 100vh;
      color: #fff;
    }

    .login-container {
  background: rgba(0, 0, 0, 0.1); /* semi-transparent gray */
  border: none;
  padding: 40px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  margin: 100px 0 0 60px; /* top & left margin to position left-top */
  border-radius: 20px; /* optional: rounded corners */
}

    .login-container img {
      width: 100px;
      margin-bottom: 20px;
    }

    .input-wrapper {
      position: relative;
      margin: 10px 0;
    }

    .input-wrapper i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #004F98;
      font-size: 16px;
    }

    .input-wrapper input {
      width: 100%;
      padding: 10px 18px 10px 40px; /* Padding left for icon */
      border-radius: 25px;
      border: none;
      font-size: 15px;
      background: rgba(255, 255, 255, 0.9);
      color: #333;
    }

    .input-wrapper input::placeholder {
      color: #666;
    }

    .login-container button {
      background: linear-gradient(90deg, #0073e6, #004F98);
      color: white;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 600;
      margin-top: 15px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .login-container button:hover {
      background: #003f7f;
    }

    .links {
      margin-top: 15px;
      display: flex;
      justify-content: space-between;
      font-size: 14px;
    }

    .links a {
      color: #fff;
      text-decoration: none;
    }

    .links a:hover {
      text-decoration: underline;
    }

    .page-header {
      position: absolute;
      top: 20px;
      left: 30px;
      font-family: 'Poppins', sans-serif;
      font-size: 18px;
      font-weight: 600;
      color: white;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      z-index: 5;
    }

    @media (max-width: 420px) {
      .login-container {
        margin: 30px;
        padding: 30px;
      }
    }
  </style>
</head>

<body>
  <div class="page-header">PSUHMPOS</div>

  <div class="login-container">
    <img src="<?= base_url ?>assets/img/lioncasa.png" alt="Logo">
    <form id="login-frm" action="" method="post">
      <div class="input-wrapper">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-wrapper">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit">LOGIN</button>
    </form>
  </div>

  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      end_loader();
    });
  </script>
</body>

</html>
