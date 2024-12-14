<?php
    include 'models/pdo.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    $message = $_POST['message'];
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if ($_FILES['image']['size'] <= 10 * 1024 * 1024) {
            $target_dir = "uploads/public/";// vị trí lưu tệp
            $timestamp = date("Y_m_d-H_i_s"); // xác định thời gian lưu
            $random_str = substr(bin2hex(random_bytes(10)), 0, rand(5, 20));// đoạn mã ngẫu nhiên từ 5-20 ký tự
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $file_name = "{$timestamp}-{$username}-{$user_id}-{$random_str}.{$file_extension}";
            $target_file = $target_dir . $file_name;

            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (in_array($file_extension, $allowed_types)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                } else {
                    echo "Error uploading image.";
                    exit;
                }
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, image_path) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $message, $image_path]);