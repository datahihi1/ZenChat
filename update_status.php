<?php
    include 'models/pdo.php';

    $timeout = 30;
    $stmt = $pdo->prepare("UPDATE users SET status = 0 WHERE status = 1 AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_activity) > ?");
    $stmt->execute([$timeout]);
