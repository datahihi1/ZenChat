<?php
    include 'models/pdo.php';
    include 'config/getbootstrap5.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user1_id = $_SESSION['user_id'];
        $user2_id = $_POST['user2_id'];
        $code = $_POST['code'];

        $stmt = $pdo->prepare("SELECT * FROM private_rooms WHERE code = ?");
        $stmt->execute([$code]);
        if ($stmt->rowCount() > 0) {
            echo "Mã code này đã được sử dụng. Vui lòng chọn mã khác.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO private_rooms (code, user1_id, user2_id) VALUES (?, ?, ?)");
            $stmt->execute([$code, $user1_id, $user2_id]);
            echo "Phòng chat riêng tư đã được tạo với mã code: " . htmlspecialchars($code);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tạo Phòng Chat Riêng Tư</title>
</head>
<body>
    <form method="post">
        <label for="user2_id">ID Người dùng thứ hai:</label>
        <input type="number" name="user2_id" required>
        
        <label for="code">Mã Code:</label>
        <input type="text" name="code" required>
        
        <button type="submit">Tạo Phòng Chat</button>
    </form>

    <div style="text-align: right; margin-bottom: 10px;">
        <a href="index.php">Public chat</a>
    </div>

    <div style="text-align: right; margin-bottom: 10px;">
        <a href="join_private_room.php">Find chat</a>
    </div>
</body>
</html>
