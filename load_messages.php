<?php
include 'models/pdo.php';

$messages = $pdo->query("SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC LIMIT 20")->fetchAll();

foreach ($messages as $message) {
    echo "<div class='message'>";
    echo "<span class='username'>" . htmlspecialchars($message['username']) . ":</span> ";
    echo htmlspecialchars($message['message']);
    echo "</div>";
}
?>
