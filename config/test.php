<?php
    include 'models/pdo.php';
    include 'config/getbootstrap5.php';
    session_start();

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];

        $stmt = $pdo->prepare("SELECT id,username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $reset_id = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO user_forgot_pass (user_id, key_id) VALUES (?, ?)");
            $stmt->execute([$user['id'], $reset_id]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'datahihi1100@gmail.com';         // Địa chỉ Gmail của người gửi
            $mail->Password   = 'rgykczvjrrfmqukb';              // Mật khẩu ứng dụng của Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('datahihi1100@gmail.com', 'Sender');
            $mail->addAddress($email, 'Receiver'); // Địa chỉ email người nhận

            $mail->isHTML(true);
            $mail->Subject = 'Email Subject';
            $mail->Body    = "Mã xác nhận của bạn là: $reset_id";    // Nội dung HTML của email
            $mail->AltBody = 'Nội dung email dạng văn bản thuần túy.'; // Nội dung email dạng văn bản

            $mail->send();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            header("Location: verify_reset.php");
        } catch (Exception $e) {
            echo "Email không thể gửi được. Lỗi: {$mail->ErrorInfo}";
        }
        } else {
            echo "Tên người dùng hoặc email không đúng.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
</head>
<body>
    <h2>Quên mật khẩu</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Lấy lại mật khẩu</button>
    </form>
</body>
</html>