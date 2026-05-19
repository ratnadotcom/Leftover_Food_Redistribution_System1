<?php
// donor/requests.php — Requests on my food
require_once '../includes/config.php';
requireRole('donor');
$uid = $_SESSION['user_id'];

// Approve/Reject from donor side
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE requests SET status='approved' WHERE id=$id");
    $req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT food_id FROM requests WHERE id=$id"));
    mysqli_query($conn, "UPDATE food SET status='reserved' WHERE id={$req['food_id']}");
    $exists = mysqli_fetch_row(mysqli_query($conn, "SELECT id FROM delivery WHERE request_id=$id"));
    if (!$exists) mysqli_query($conn, "INSERT INTO delivery (request_id) VALUES ($id)");
    $msg = 'Request approved!';
}
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn, "UPDATE requests SET status='rejected' WHERE id=$id");
    $msg = 'Request rejected.';
}

$requests = mysqli_query($conn, "
    SELECT r.*, u.name AS receiver_name, u.phone, u.address,
           f.food_name, f.location, r.quantity, f.unit
    FROM requests r
    JOIN food f  ON r.food_id = f.id
    JOIN users u ON r.receiver_id = u.id
    WHERE f.donor_id = $uid
    ORDER BY r.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Requests — Donor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="add_food.php"  class="nav-link"><i class="fas fa-plus-circle"></i> Add Food</a>
  <a href="my_food.php"   class="nav-link"><i class="fas fa-utensils"></i> My Donations</a>
  <a href="requests.php"  class="nav-link active"><i class="fas fa-inbox"></i> Requests</a>
</div>
<div class="main-content">
  <div class="page-header">
    <h2><i class="fas fa-inbox me-2" style="color:var(--primary)"></i>Food Requests</h2>
    <p>Requests made by receivers for your donated food</p>
  </div>
  <?php if (isset($msg)): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr>
            <th>#</th><th>Receiver</th><th>Food Item</th><th>Qty Requested</th>
            <th>Message</th><th>Date</th><th>Status</th>
          </tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($requests) === 0): ?>
            <tr><td colspan="8" class="text-center py-5 text-muted">No requests on your food yet.</td></tr>
          <?php endif; ?>
          <?php $sl = 1 ?>
          <?php while ($r = mysqli_fetch_assoc($requests)): ?>
          <tr>
            <td><?= $sl++ ?></td>
            <td>
              <strong><?= htmlspecialchars($r['receiver_name']) ?></strong><br>
              <span class="text-muted" style="font-size:0.78rem;"><?= $r['phone'] ?></span>
            </td>
            <td><?= htmlspecialchars($r['food_name']) ?> <span class="text-muted">(<?= $r['location'] ?>)</span></td>
            <td><?= $r['quantity'] ?></td>
            <td style="font-size:0.82rem;"><?= $r['message'] ? htmlspecialchars(substr($r['message'],0,60)) : '—' ?></td>
            <td style="font-size:0.8rem;"><?= date('d M, h:i A', strtotime($r['created_at'])) ?></td>
            <td><span class="status-badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
            <!-- <td>
              <?php if ($r['status'] === 'pending'): ?>
                <a href="?approve=<?= $r['id'] ?>" class="btn btn-sm btn-success"
                   onclick="return confirm('Approve?')"><i class="fas fa-check"></i></a>
                <a href="?reject=<?= $r['id'] ?>"  class="btn btn-sm btn-danger"
                   onclick="return confirm('Reject?')"><i class="fas fa-times"></i></a>
              <?php else: ?>—<?php endif; ?>
            </td> -->
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
