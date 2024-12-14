<?php // đặt lại mật khẩu (yêu cầu verify_reset.php)
        include 'models/pdo.php';
        include 'config/getbootstrap5.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $user_id]);

        $stmt = $pdo->prepare("DELETE FROM user_forgot_pass WHERE user_id = ?");
        $stmt->execute([$user_id]);

        session_destroy();
        session_start();
        $_SESSION['app_message'] = "Thay đổi mật khẩu thành công. Vui lòng đăng nhập lại";
        header("Location:login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
</head>
<body>
    <h2>Đặt lại mật khẩu</h2>
    <form method="POST" action="">
        <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
        <button type="submit">Đổi mật khẩu</button>
    </form>
</body>
</html>
