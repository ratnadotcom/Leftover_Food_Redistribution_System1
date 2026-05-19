<?php
// includes/navbar.php — Top navigation bar
$role       = $_SESSION['role'] ?? '';
$name       = $_SESSION['name'] ?? '';
$initials   = strtoupper(substr($name, 0, 1));
$base       = '/food_system/';
$dashLink   = $base . $role . '/dashboard.php';
$roleColors = ['admin'=>'danger','donor'=>'success','receiver'=>'primary'];
$roleColor  = $roleColors[$role] ?? 'secondary';
?>
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="<?= $dashLink ?>">
      <i class="fas fa-seedling me-2"></i>Food<span>Share</span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="badge bg-<?= $roleColor ?> text-capitalize"><?= $role ?></span>
      <!-- <div class="avatar"><?= $initials ?></div> -->
      <span class="text-white fw-600 d-none d-md-inline" style="font-size:0.88rem;"><?= htmlspecialchars($name) ?></span>
      <a href="/food_system/logout.php" class="btn btn-sm btn-danger">
        <i class="fas fa-sign-out-alt me-1"></i> Logout
      </a>
    </div>
  </div>
</nav>
