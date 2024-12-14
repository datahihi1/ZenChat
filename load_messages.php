<?php

    include  'models/pdo.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        exit('Unauthorized');
    }

    $user_id = $_SESSION['user_id'];

    $messages = $pdo->query("SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC LIMIT 20")->fetchAll();

    foreach ($messages as $message): ?>
        <div class="message mb-3">
          <span class="fw-bold text-primary"><?php echo htmlspecialchars($message['username']); ?>:</span>
          <?php echo htmlspecialchars($message['message']); ?>
  
          <?php if ($message['image_path']): ?>
            <div>
              <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Hình ảnh" width="200" class="chat-image img-fluid mt-2">
            </div>
          <?php endif; ?>
  
          <?php if ($message['user_id'] === $user_id): ?>
            <a href="recall_messages.php?recall_id=<?= $message['id'] ?>" class="btn btn-link">Thu hồi</a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
