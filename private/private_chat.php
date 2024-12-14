<?php
    include 'models/pdo.php';
    include 'config/getbootstrap5.php';
    session_start();

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = $_POST['message'];
        $image_path = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "uploads/private/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                } else {
                    echo "Đã xảy ra lỗi khi tải lên hình ảnh.";
                }
            } else {
                echo "Chỉ chấp nhận các định dạng JPG, JPEG, PNG và GIF.";
            }
        }

        $stmt = $pdo->prepare("INSERT INTO private_messages (room_id, sender_id, message, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$room_id, $user_id, $message, $image_path]);
    }

    $stmt = $pdo->prepare("SELECT private_messages.*, users.username FROM private_messages JOIN users ON private_messages.sender_id = users.id WHERE room_id = ? ORDER BY created_at DESC");
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
        .chat-image { max-width: 200px; margin-top: 5px; }
    </style>
</head>
<body>
    <h2>Phòng Chat Riêng Tư</h2>

    <!-- Nút Đăng xuất -->
    <div style="text-align: right; margin-bottom: 10px;">
        <a href="logout.php">Logout</a>
    </div>

    <!-- Nút Vào khu vực chat công cộng -->
    <div style="text-align: right; margin-bottom: 10px;">
        <a href="index.php">Enter public chat</a>
    </div>

    <!-- Nút đổi đoạn chat riêng tư -->
    <div style="text-align: right; margin-bottom: 10px;">
        <a href="join_private_room.php">Change private chat</a>
    </div>

    <div id="chat-box">
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <span class="username"><?php echo htmlspecialchars($message['username']); ?>:</span>
                <?php echo htmlspecialchars($message['message']); ?>
                
                <?php if ($message['image_path']): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Hình ảnh" class="chat-image">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="message" placeholder="Nhập tin nhắn" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Gửi</button>
    </form>
</body>
</html>


