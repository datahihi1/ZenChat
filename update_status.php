<?php
include 'models/pdo.php';

// Chuyển tất cả người dùng không hoạt động trong 5 phút về trạng thái offline
$timeout = 5 * 60; // 5 phút
$stmt = $pdo->prepare("UPDATE users SET status = 0 WHERE status = 1 AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_activity) > ?");
$stmt->execute([$timeout]);
?>
