<?php
include 'models/pdo.php';
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['room_id'])) {
    header("Location: join_private_room.php");
    exit;
}

$room_id = $_SESSION['room_id'];
$user_id = $_SESSION['user_id'];

// Xử lý gửi tin nhắn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO private_messages (room_id, sender_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$room_id, $user_id, $message]);
}

// Lấy tin nhắn từ phòng chat riêng tư
$stmt = $pdo->prepare("SELECT private_messages.*, users.username FROM private_messages JOIN users ON private_messages.sender_id = users.id WHERE room_id = ? ORDER BY created_at ASC");
$stmt->execute([$room_id]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Phòng Chat Riêng Tư</title>
    <style>
        #chat-box { height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; }
        .message { margin-bottom: 10px; }
        .username { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Phòng Chat Riêng Tư</h2>

    <div id="chat-box">
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <span class="username"><?php echo htmlspecialchars($message['username']); ?>:</span>
                <?php echo htmlspecialchars($message['message']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post">
        <input type="text" name="message" required>
        <button type="submit">Gửi</button>
    </form>
</body>
</html>

