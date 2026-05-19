<?php
// donor/my_food.php
require_once '../includes/config.php';
requireRole('donor');
$uid = $_SESSION['user_id'];

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM food WHERE id=$id AND donor_id=$uid");
    $msg = 'Food item deleted.';
}

$foods = mysqli_query($conn, "
    SELECT f.*, (SELECT COUNT(*) FROM requests WHERE food_id=f.id) AS req_count
    FROM food f WHERE donor_id=$uid ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>My Donations — Donor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <div class="sidebar-label">Donor Menu</div>
  <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="add_food.php"  class="nav-link"><i class="fas fa-plus-circle"></i> Add Food</a>
  <a href="my_food.php"   class="nav-link active"><i class="fas fa-utensils"></i> My Donations</a>
  <a href="requests.php"  class="nav-link"><i class="fas fa-inbox"></i> Requests</a>
</div>
<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-start">
    <div>
      <h2><i class="fas fa-utensils me-2" style="color:var(--primary)"></i>My Donations</h2>
      <p>All food items you have posted</p>
    </div>
    <a href="add_food.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Food</a>
  </div>

  <?php if (isset($msg)): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr>
            <th>#</th><th>Food Name</th><th>Quantity</th><th>Location</th>
            <th>Expiry</th><th>Requests</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($foods) === 0): ?>
            <tr><td colspan="8" class="text-center py-5 text-muted">No donations yet. <a href="add_food.php">Add your first one!</a></td></tr>
          <?php endif; ?>
          <?php $sl = 1 ?>
          <?php while ($f = mysqli_fetch_assoc($foods)):
            $expired = strtotime($f['expiry']) < time();
          ?>
          <tr>
            <td><?= $sl++ ?></td>
            <td><strong><?= htmlspecialchars($f['food_name']) ?></strong>
              <?php if ($f['description']): ?>
                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars(substr($f['description'],0,50)) ?>...</div>
              <?php endif; ?>
            </td>
            <td><?= $f['quantity'] ?> <?= $f['unit'] ?></td>
            <td><?= htmlspecialchars($f['location']) ?></td>
            <td>
              <?php if ($expired): ?>
                <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> Expired</span>
              <?php else: ?>
                <?= date('d M, h:i A', strtotime($f['expiry'])) ?>
              <?php endif; ?>
            </td>
            <td><span class="badge bg-secondary"><?= $f['req_count'] ?></span></td>
            <td><span class="status-badge badge-<?= $f['status'] ?>"><?= $f['status'] ?></span></td>
            <td>
              <a href="edit_food.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
              <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Delete this food item?')"><i class="fas fa-trash"></i></a>
            </td>
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
