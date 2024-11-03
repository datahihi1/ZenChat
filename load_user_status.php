<?php
include 'models/pdo.php';

$users = $pdo->query("SELECT username, status FROM users")->fetchAll();

foreach ($users as $user) {
    $status = $user['status'] ? 'Online' : 'Offline';
    $status_class = $user['status'] ? 'online' : 'offline';
    echo "<div class='user'><span class='{$status_class}'>" . htmlspecialchars($user['username']) . " - {$status}</span></div>";
}
?>