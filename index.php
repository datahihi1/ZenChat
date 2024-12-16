<?php

include 'models/pdo.php';
include 'config/getbootstrap5.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $pdo->prepare("UPDATE users SET last_activity = NOW(), status = 1 WHERE id = ?");
$stmt->execute([$user_id]);

// Xử lý gửi tin nhắn và tải lên ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $message = $_POST['message'];
  $image_path = null;

  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    if ($_FILES['image']['size'] > 10 * 1024 * 1024) {
      echo "Dung lượng ảnh vượt quá 10MB.";
    } else {
      $timestamp = date("Y_m_d-H_i_s");
      $random_str = substr(bin2hex(random_bytes(10)), 0, rand(5, 20));
      $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
      $file_name = "{$timestamp}-{$username}-{$user_id}-{$random_str}.{$file_extension}";
      $target_file = UPLOAD_PUBLIC . $file_name;

      // Kiểm tra định dạng ảnh hợp lệ
      $allowed_types = ["jpg", "jpeg", "png", "gif"];
      if (in_array($file_extension, $allowed_types)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
          $image_path = $target_file;
        } else {
          echo "Đã xảy ra lỗi khi tải lên hình ảnh.";
        }
      } else {
        echo "Chỉ chấp nhận các định dạng JPG, JPEG, PNG và GIF.";
      }
    }
  }

  $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, image_path) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $message, $image_path]);
}

// Lấy tin nhắn từ cơ sở dữ liệu
$messages = $pdo->query("SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC LIMIT 20")->fetchAll();
function getUserStatus($pdo)
{
  $users = $pdo->query("SELECT username, status FROM users")->fetchAll();
  return $users;
}
$user_statuses = getUserStatus($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat Room</title>
  <link rel="icon" type="image/x-icon" href="resources/favicon.png">
</head>

<body class="container-fluid">

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
  } ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Chat Room</h2>
    <div>
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
      <a href="private_chat.php" class="btn btn-primary">Private</a>
      <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#UserStatusModal">
        User List
      </button>
    </div>
  </div>

  <!-- Modal user status -->
  <div class="modal fade" id="UserStatusModal" tabindex="-1" aria-labelledby="UserStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="UserStatusModalLabel">User Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="user-list">
          <p>Vui lòng chờ ...</p>
          <!--  Đây là vị trí load_user_status.php tự động truyền vào và tải -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Khu vực hiển thị tin nhắn chat của người dùng -->
  <div id="chat-box" class="border rounded p-3 mb-4" style="height: 64vh; overflow-y: scroll;">
  <p>Vui lòng chờ ...</p>
    <!--  Đây là vị trí load_message.php tự động truyền vào và tải -->
  </div>

  <!-- Khu vực nhắn tin -->
  <div class="bg-body-tertiary p-4 rounded position-fixed bottom-0 start-0 end-0">
    <form id="chat-form" method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-8">
        <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn">
      </div>
      <div class="col-md-3">
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-success w-100">Gửi</button>
      </div>
    </form>
  </div>

</body>
<script src="resources/main.js"></script>

</html>

<script>
  function toggleFullScreen() {
    var elem = document.documentElement;
    if (!document.fullscreenElement) {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
      } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }
    }
  }
</script>