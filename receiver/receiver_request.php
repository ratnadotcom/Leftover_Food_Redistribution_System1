<?php
// receiver/my_requests.php — Track all my requests
require_once '../includes/config.php';
requireRole('receiver');
$uid = $_SESSION['user_id'];

// Cancel pending request
if (isset($_GET['cancel'])) {
    $id = (int)$_GET['cancel'];
    mysqli_query($conn, "DELETE FROM requests WHERE id=$id AND receiver_id=$uid AND status='pending'");
    $msg = 'Request cancelled.';
}

$requests = mysqli_query($conn, "
    SELECT r.*, f.food_name, f.location, f.quantity AS food_qty, f.unit,
           f.expiry, u.name AS donor_name,
           d.delivery_person, d.contact AS del_contact, d.delivery_status, d.notes
    FROM requests r
    JOIN food f  ON r.food_id = f.id
    JOIN users u ON f.donor_id = u.id
    LEFT JOIN delivery d ON d.request_id = r.id
    WHERE r.receiver_id=$uid
    ORDER BY r.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>My Requests — Receiver</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <a href="dashboard.php"      class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="available_food.php" class="nav-link"><i class="fas fa-search"></i> Browse Food</a>
  <a href="my_requests.php"    class="nav-link active"><i class="fas fa-clipboard-list"></i> My Requests</a>
</div>
<div class="main-content">
  <div class="page-header">
    <h2><i class="fas fa-clipboard-list me-2" style="color:var(--primary)"></i>My Requests</h2>
    <p>Track the status of all your food requests</p>
  </div>
  <?php if (isset($msg)): ?><div class="alert alert-info"><?= $msg ?></div><?php endif; ?>

  <?php if (mysqli_num_rows($requests) === 0): ?>
    <div class="empty-state card">
      <i class="fas fa-clipboard-list"></i>
      <h5>No requests yet</h5>
      <p>Browse available food and make a request.</p>
      <a href="available_food.php" class="btn btn-primary mt-2">Browse Food</a>
    </div>
  <?php else: ?>
  <div class="row g-3">
  <?php while ($r = mysqli_fetch_assoc($requests)): ?>
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-700 mb-1"><?= htmlspecialchars($r['food_name']) ?></h6>
            <div class="text-muted" style="font-size:0.82rem;">
              <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($r['location']) ?>
              &nbsp;·&nbsp;<i class="fas fa-user me-1"></i><?= htmlspecialchars($r['donor_name']) ?>
            </div>
          </div>
          <span class="status-badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span>
        </div>

        <!-- Progress tracker -->
        <?php
        $steps = ['pending'=>0,'approved'=>1,'delivered'=>2];
        $step  = $steps[$r['status']] ?? ($r['status']==='rejected' ? -1 : 0);
        $stepLabels = ['Requested','Approved','Delivered'];
        ?>
        <?php if ($r['status'] !== 'rejected'): ?>
        <div class="d-flex justify-content-between mb-3 position-relative" style="padding:0 10px;">
          <div style="position:absolute;top:12px;left:20px;right:20px;height:3px;background:#e0e0e0;z-index:0;">
            <div style="height:100%;background:var(--primary);width:<?= $step===0?'0':($step===1?'50':'100') ?>%;transition:width 0.4s;"></div>
          </div>
          <?php foreach ($stepLabels as $i => $label): ?>
          <div class="text-center" style="z-index:1;">
            <div style="width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;
              background:<?= $i<=$step?'var(--primary)':'#e0e0e0' ?>;color:<?= $i<=$step?'white':'#999' ?>;">
              <?= $i < $step ? '✓' : ($i+1) ?>
            </div>
            <div style="font-size:0.7rem;margin-top:4px;color:<?= $i<=$step?'var(--primary)':'#999' ?>;font-weight:<?= $i===$step?'700':'400'?>;"><?= $label ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div class="alert alert-danger py-2 mb-3" style="font-size:0.85rem;">
            <i class="fas fa-times-circle me-2"></i>This request was rejected.
          </div>
        <?php endif; ?>

        <!-- Details -->
        <div class="row text-center mb-3" style="font-size:0.8rem;">
          <div class="col-4 border-end">
            <div class="text-muted">Requested</div>
            <strong><?= $r['quantity'] ?> units</strong>
          </div>
          <div class="col-4 border-end">
            <div class="text-muted">Available</div>
            <strong><?= $r['food_qty'] ?> <?= $r['unit'] ?></strong>
          </div>
          <div class="col-4">
            <div class="text-muted">Date</div>
            <strong><?= date('d M', strtotime($r['created_at'])) ?></strong>
          </div>
        </div>

        <!-- Delivery info (if assigned) -->
        <?php if ($r['delivery_status']): ?>
        <div class="p-2 rounded mb-3" style="background:#E8F5E9;font-size:0.82rem;">
          <i class="fas fa-truck me-2 text-success"></i>
          <strong>Delivery:</strong> <?= str_replace('_',' ', $r['delivery_status']) ?>
          <?php if ($r['delivery_person']): ?>
            · <?= htmlspecialchars($r['delivery_person']) ?> (<?= $r['del_contact'] ?>)
          <?php endif; ?>
          <?php if ($r['notes']): ?>
            <div class="text-muted mt-1"><?= htmlspecialchars($r['notes']) ?></div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($r['status'] === 'pending'): ?>
          <a href="?cancel=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger"
             onclick="return confirm('Cancel this request?')">
             <i class="fas fa-times me-1"></i>Cancel Request
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
  </div>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
