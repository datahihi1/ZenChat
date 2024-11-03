<?php
include 'models/pdo.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("UPDATE users SET last_activity = NOW(), status = 1 WHERE id = ?");
$stmt->execute([$user_id]);

// Lấy tin nhắn từ cơ sở dữ liệu
$messages = $pdo->query("SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC LIMIT 20")->fetchAll();
?>

<?php
function getUserStatus($pdo) {
    $users = $pdo->query("SELECT username, status FROM users")->fetchAll();
    return $users;
}

$user_statuses = getUserStatus($pdo);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Room</title>
    <style>
        #chat-box { height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; }
        .message { margin-bottom: 10px; }
        .username { font-weight: bold; }
        #user-list { margin-top: 10px; border: 1px solid #ccc; padding: 10px; }
        .user { margin-bottom: 5px; }
        .online { color: green; }
        .offline { color: gray; }
    </style>
</head>
<body>
    <h2>Chat Room</h2>

    <!-- Nút Đăng xuất -->
    <div style="text-align: right; margin-bottom: 10px;">
        <a href="logout.php">Logout</a>
    </div>

    <!-- Danh sách người dùng và trạng thái -->
    <div id="user-list">
        <h3>Users Online/Offline</h3>
        <?php foreach ($user_statuses as $user): ?>
            <div class="user">
                <span class="<?php echo $user['status'] ? 'online' : 'offline'; ?>">
                    <?php echo htmlspecialchars($user['username']); ?> - <?php echo $user['status'] ? 'Online' : 'Offline'; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Khu vực chat -->
    <div id="chat-box">
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <span class="username"><?php echo htmlspecialchars($message['username']); ?>:</span>
                <?php echo htmlspecialchars($message['message']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form id="chat-form">
        <input type="text" name="message" id="message" required>
        <button type="submit">Send</button>
    </form>

    <script>
        document.getElementById('chat-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const message = document.getElementById('message').value;

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message)
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('message').value = '';
                loadMessages();
                loadUserStatus();
            });
        });

        function loadMessages() {
            fetch('load_messages.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('chat-box').innerHTML = data;
                });
        }

        function loadUserStatus() {
            fetch('load_user_status.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('user-list').innerHTML = data;
                });
        }

        setInterval(loadMessages, 1000);
        setInterval(loadUserStatus, 10000); 
    </script>
</body>
</html>

