<?php // xác thực với mã 
    include 'models/pdo.php';
    include 'config/getbootstrap5.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $resetKey = $_POST['reset_key'];

        $stmt = $pdo->prepare("SELECT * FROM user_forgot_pass WHERE user_id = ? AND key_id = ?");
        $stmt->execute([$user_id, $resetKey]);
        $record = $stmt->fetch();

        if ($record) {
            header("Location: reset_password.php");
            exit();
        } else {
            echo "Mã xác thực không đúng.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Xác thực mã</title>
</head>
<body>
    <h2>Xác thực mã quên mật khẩu</h2>
    <?php
        if(isset(($_SESSION['user_name']))){
            echo 'Tên người dùng của bạn là '.$_SESSION['user_name'];
        }
        unset($_SESSION['user_name']);
    ?>
    <form method="POST" action="">
        <input type="text" name="reset_key" placeholder="Nhập mã xác thực" required>
        <button type="submit">Xác nhận</button>
    </form>
</body>
</html>
