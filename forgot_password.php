<?php
    include 'models/pdo.php';
    include 'config/getbootstrap5.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];

        $stmt = $pdo->prepare("SELECT id,username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $reset_id = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO user_forgot_pass (user_id, key_id) VALUES (?, ?)");
            $stmt->execute([$user['id'], $reset_id]);

            $to = $email;
            $subject = "Mã xác nhận quên mật khẩu";
            $message = "Mã xác nhận của bạn là: $reset_id";
            $headers = "From: no-reply@example.com";

            if (mail($to, $subject, $message, $headers)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                header("Location: verify_reset.php");
                exit();
            } else {
                echo "Không thể gửi email. Vui lòng thử lại sau.";
            }
        } else {
            echo "Email không đúng định dạng hoặc không tồn tại trên hệ thống.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
</head>
<body>
    <h2>Quên mật khẩu</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Lấy lại mật khẩu</button>
    </form>
</body>
</html>