<?php
    include 'models/pdo.php';
    session_start();

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("UPDATE users SET status = 0 WHERE id = ?");
        $stmt->execute([$user_id]);
    }

    if (isset($_COOKIE['cookie_key'])) {
        $cookie_key = $_COOKIE['cookie_key'];
        setcookie("cookie_key", "", time() - 3600, "/");
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE cookie_id = ?");
        $stmt->execute([$cookie_key]);
    }

    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
