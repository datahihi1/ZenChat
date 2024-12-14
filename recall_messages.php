<?php
    include 'models/pdo.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        exit('Unauthorized');
    }

    $user_id = $_SESSION['user_id'];
    $message_id = isset($_GET['recall_id']) ? intval($_GET['recall_id']) : 0;

    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ? AND user_id = ?");
    $stmt->execute([$message_id, $user_id]);
    $message = $stmt->fetch();
    $mess_img = $message["image_path"];

    if ($message) {
        if($mess_img){
            unlink($mess_img);
        }
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $_SESSION['app_message'] = "Thu hồi tin nhắn thành công!";
        header("Location: index.php");
    } else {
        $_SESSION['err_message'] = "Đã có lỗi xảy ra!";
        header("Location: index.php");
    }
