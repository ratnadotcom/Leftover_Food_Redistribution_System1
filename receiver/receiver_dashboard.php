<?php
// receiver/dashboard.php
require_once '../includes/config.php';
requireRole('receiver');
$uid = $_SESSION['user_id'];

$totalReqs   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM requests WHERE receiver_id=$uid"))[0];
$pending     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM requests WHERE receiver_id=$uid AND status='pending'"))[0];
$approved    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM requests WHERE receiver_id=$uid AND status='approved'"))[0];
$delivered   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM requests WHERE receiver_id=$uid AND status='delivered'"))[0];
$availableFood = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM food WHERE status='available' AND expiry > NOW()"))[0];

// My recent requests
$myReqs = mysqli_query($conn, "
    SELECT r.*, f.food_name, f.location, f.expiry,
           d.delivery_status, d.delivery_person
    FROM requests r
    JOIN food f ON r.food_id = f.id
    LEFT JOIN delivery d ON d.request_id = r.id
    WHERE r.receiver_id=$uid
    ORDER BY r.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Receiver Dashboard — FoodShare BD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <div class="sidebar-label">Receiver Menu</div>
  <a href="dashboard.php"    class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="available_food.php" class="nav-link"><i class="fas fa-search"></i> Browse Food</a>
  <a href="my_requests.php"  class="nav-link"><i class="fas fa-clipboard-list"></i> My Requests</a>
</div>
<div class="main-content">
  <div class="page-header">
    <h2>Hello, <?= htmlspecialchars($_SESSION['name']) ?>! 🙏</h2>
    <p>Browse available food donations and request what you need.</p>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat-card stat-orange">
      <div class="stat-num"><?= $availableFood ?></div><div class="stat-label">Food Available</div>
      <i class="fas fa-utensils stat-icon"></i>
    </div></div>
    <div class="col-6 col-lg-3"><div class="stat-card stat-purple">
      <div class="stat-num"><?= $pending ?></div><div class="stat-label">Pending Requests</div>
      <i class="fas fa-clock stat-icon"></i>
    </div></div>
    <div class="col-6 col-lg-3"><div class="stat-card stat-green">
      <div class="stat-num"><?= $approved ?></div><div class="stat-label">Approved</div>
      <i class="fas fa-check-circle stat-icon"></i>
    </div></div>
    <div class="col-6 col-lg-3"><div class="stat-card stat-blue">
      <div class="stat-num"><?= $delivered ?></div><div class="stat-label">Delivered</div>
      <i class="fas fa-truck stat-icon"></i>
    </div></div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-700 mb-0">My Recent Requests</h5>
    <a href="available_food.php" class="btn btn-primary"><i class="fas fa-search me-2"></i>Browse Food</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th>Food</th><th>Location</th><th>Request Status</th><th>Delivery</th><th>Date</th>
        </tr></thead>
        <tbody>
        <?php if (mysqli_num_rows($myReqs) === 0): ?>
          <tr><td colspan="5" class="text-center py-5 text-muted">
            No requests yet. <a href="available_food.php">Browse available food →</a>
          </td></tr>
        <?php endif; ?>
        <?php while ($r = mysqli_fetch_assoc($myReqs)): ?>
        <tr>
          <td><strong><?= htmlspecialchars($r['food_name']) ?></strong></td>
          <td><?= htmlspecialchars($r['location']) ?></td>
          <td><span class="status-badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
          <td>
            <?php if ($r['delivery_status']): ?>
              <span class="badge bg-info text-white"><?= str_replace('_',' ',$r['delivery_status']) ?></span>
              <?php if ($r['delivery_person']): ?>
                <div style="font-size:0.75rem;"><?= htmlspecialchars($r['delivery_person']) ?></div>
              <?php endif; ?>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td style="font-size:0.8rem;"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
