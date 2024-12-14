<?php
    include 'models/pdo.php';
    include 'config/getbootstrap5.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $code = $_POST['code'];
        $stmt = $pdo->prepare("SELECT * FROM private_rooms WHERE code = ? AND (user1_id = ? OR user2_id = ?)");
        $stmt->execute([$code, $user_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            $room = $stmt->fetch();
            $_SESSION['room_id'] = $room['id'];
            header("Location: private_chat.php");
            exit;
        } else {
            echo "Mã code không hợp lệ hoặc bạn không có quyền truy cập.";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tham Gia Phòng Chat Riêng Tư</title>
</head>
<body>
    <form method="post">
        <label for="code">Mã Code:</label>
        <input type="text" name="code" required>
        <button type="submit">Tham Gia</button>
    </form>

    <!-- Nút tạo đoạn chat riêng tư -->
    <div style="text-align: right; margin-bottom: 10px;">
        <a href="create_private_room.php">Create private chat</a>
    </div>

    <div style="text-align: right; margin-bottom: 10px;">
        <a href="index.php">Public chat</a>
    </div>

</body>
</html>
