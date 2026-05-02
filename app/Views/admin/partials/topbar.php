<?php

use App\Core\View;

$user = $_SESSION['user'] ?? [];
$initials = strtoupper(substr($user['full_name'] ?? 'A', 0, 1));
?>
<header class="admin-topbar">
    <div>
        <h2>Admin Portal</h2>
        <p><?= View::e($user['email'] ?? '') ?></p>
    </div>

    <div class="admin-avatar" aria-label="Admin profile">
        <?= View::e($initials) ?>
    </div>
</header>
