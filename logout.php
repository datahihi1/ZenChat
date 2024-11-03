<?php
include 'models/pdo.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET status = 0 WHERE id = ?");
    $stmt->execute([$user_id]);
}

session_unset();
session_destroy();
header('Location: login.php');
exit;
?>
