<?php

include  'models/pdo.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

$messages = $pdo->query("SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC LIMIT 20")->fetchAll();

foreach ($messages as $message):
  $time = strtotime($message['created_at']);
  $time_chat = date('H:i d-m-Y',$time);
?>
<!-- HTML -->
  <div class="message mb-3">
    <span class="fw-bold text-primary"><?php echo htmlspecialchars($message['username']); ?>:</span>
    <?php echo htmlspecialchars($message['message'] ?? ''); ?>

    <?php if ($message['image_path']): ?>
      <div>
        <a href="<?php echo htmlspecialchars($message['image_path']); ?>" target="_blank">
          <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Hình ảnh" width="200" class="chat-image img-fluid mt-2">
        </a>
      </div>
    <?php endif; ?>

    <?php if ($message['user_id'] === $user_id): ?>
      <a href="recall_messages.php?recall_id=<?= $message['id']?>" class="btn btn-link">Thu hồi </a>
    <?php endif; ?>
    <p style="color: darkgrey;"><?=$time_chat?></p>
  </div>
<?php endforeach; ?>