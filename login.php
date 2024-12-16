<?php // trang đăng nhập
  include 'models/pdo.php';
  include 'config/getbootstrap5.php';
  session_start();

  if(isset($_COOKIE['cookie_key'])){
    $stmt = $pdo->prepare("SELECT u_s.*,u.* FROM user_sessions AS u_s JOIN users AS u ON u.id=u_s.user_id WHERE cookie_id=?");
    $stmt->execute([$_COOKIE['cookie_key']]);
    $session = $stmt->fetch();
    $_SESSION['user_id'] = $session['user_id'];
    $_SESSION['username'] = $session['username'];
  }

  function generateCookieKey($length = 16) {
    return substr(bin2hex(random_bytes($length)), 0, $length);
  }
  $cookie_key = generateCookieKey();

  if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (empty($user)) {
      $_SESSION['login_message_user'] = "Couldn't find account!";
    } else {
      $verify = password_verify($password, $user['password']);

      if ($verify) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION["app_message"] = "Đăng nhập thành công";
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        setcookie("cookie_key", $cookie_key, time() + 86400 * 30, "/");// 30 ngày
        $expired_at = date('Y-m-d H:i:s',strtotime('+30 days'));
        $stmt = $pdo->prepare("INSERT INTO user_sessions(user_id, user_agents, cookie_id, expired_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user['id'], $user_agent, $cookie_key, $expired_at]);

        header('Location: index.php');
      } else {
        $_SESSION['login_message_pass'] = "Incorrect password!";
      }
    }
  }
?>

<div class="container-fluid py-3">

  <?php
    if (isset($_SESSION["err_message"])) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['err_message'] .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
      unset($_SESSION["err_message"]);
    }

    if (isset($_SESSION["app_message"])) {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['app_message'] .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
      unset($_SESSION["app_message"]);
    }
  ?>

  <div class="d-flex align-items-center justify-content-center">

    <div class="col-md-7 col-sm-12">
      <form method="POST" action="">
        <!-- Email input -->
        <div class="form-outline mb-4">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control form-control-lg" required />
          <?php
          if (isset($_SESSION["login_message_user"])) {
            echo '<span class="text-danger">' . $_SESSION['login_message_user'] . '</span>';
            unset($_SESSION["login_message_user"]);
          }
          ?>
        </div>
        <div class="form-outline mb-4">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control form-control-lg" required />
          <?php
          if (isset($_SESSION["login_message_pass"])) {
            echo '<span class="text-danger">' . $_SESSION['login_message_pass'] . '</span>';
            unset($_SESSION["login_message_pass"]);
          }
          ?>
        </div>

        <div class="d-flex justify-content-around align-items-center mb-4">
          <a href="forgot_password.php">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary">Sign in</button>

        <div class="d-flex mb-4 mt-3">
          <p>No account? <a href="register.php">Register</a> now!</p>
        </div>

      </form>
    </div>
  </div>
</div>


