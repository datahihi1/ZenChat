<?php
include 'models/pdo.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$message = $_POST['message'];

$stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
$stmt->execute([$user_id, $message]);
?>
