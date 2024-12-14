<?php //đăng ký người dùng mới
  include 'models/pdo.php';
  include 'config/getbootstrap5.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

      $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
      $stmt->bindValue(':username', $username);
      $stmt->execute();
      $user = $stmt->fetch();
      
      if ($user) {
          $_SESSION["register_message_user"] = "Người dùng đã tồn tại.";
      }
      else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $email_new = $stmt->fetch();
        if ($email_new) {
          $_SESSION["register_message_email"] = "Email đã được đăng ký.";
        }else{
          $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
          $stmt->execute([$username, $email, $password]);
          $_SESSION['app_message'] = "Đăng ký tài khoản thành công!";
          header('Location: login.php');
        }

      }
  }
?>

<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-center">

      <div class="col-md-7">
        <form method="POST" action="">
          <!-- input -->
          <div class="form-outline mb-4">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control form-control-lg" required/>
            <?php
                if(isset($_SESSION["register_message_user"])) {
                echo '<span style="color:red">' . $_SESSION['register_message_user'] . '</span>';
                unset($_SESSION["register_message_user"]);
                }
            ?>
          </div>
          <div class="form-outline mb-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control form-control-lg" required/>
            <?php
                if(isset($_SESSION["register_message_email"])) {
                echo '<span style="color:red">' . $_SESSION['register_message_email'] . '</span>';
                unset($_SESSION["register_message_email"]);
                }
            ?>
          </div>
          <div class="form-outline mb-4">
            <label class="form-label" >Password</label>
            <input type="password" name="password" class="form-control form-control-lg" required/>
            <?php
                if(isset($_SESSION["register_message_pass"])) {
                echo '<span style="color:red">' . $_SESSION['register_message_pass'] . '</span>';
                unset($_SESSION["register_message_pass"]);
                }
            ?>
          </div>

          <button type="submit" class="btn btn-success">Register</button>

          <div class="d-flex mb-4">
            <p>Have account? <a href="login.php">Login</a> now!</p>
          </div>

        </form>
      </div>
    </div>
  </div>