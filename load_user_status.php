<?php
include 'models/pdo.php';

$users = $pdo->query("SELECT username, status FROM users")->fetchAll();

?>
<!-- HTML -->
<h6>Online</h6>
<?php foreach ($users as $user): ?>
    <?php if ($user['status'] == 1): ?>
        <div class="user">
            <span class="text-success">
                <?php echo htmlspecialchars($user['username']); ?> - Online
            </span>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<hr>

<h6>Offline</h6>
<?php foreach ($users as $user): ?>
    <?php if ($user['status'] == 0): ?>
        <div class="user">
            <span class="text-secondary">
                <?php echo htmlspecialchars($user['username']); ?> - Offline
            </span>
        </div>
    <?php endif; ?>
<?php endforeach; ?>