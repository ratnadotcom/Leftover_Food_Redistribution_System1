<?php
// admin/food.php — Admin view all food items
require_once '../includes/config.php';
requireRole('admin');

// Delete food
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM food WHERE id=$id");
    $msg = 'Food item deleted.';
}

$search = isset($_GET['q']) ? clean($conn, $_GET['q']) : '';
$where  = $search ? "WHERE f.food_name LIKE '%$search%' OR f.location LIKE '%$search%'" : '';

$foods = mysqli_query($conn, "
    SELECT f.*, u.name AS donor_name,
      (SELECT COUNT(*) FROM requests WHERE food_id=f.id) AS req_count
    FROM food f JOIN users u ON f.donor_id = u.id
    $where ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Food Items — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="users.php"     class="nav-link"><i class="fas fa-users"></i> All Users</a>
  <a href="food.php"      class="nav-link active"><i class="fas fa-utensils"></i> Food Items</a>
  <div class="sidebar-label">Manage</div>
  <a href="requests.php"  class="nav-link"><i class="fas fa-inbox"></i> Requests</a>
  <a href="delivery.php"  class="nav-link"><i class="fas fa-truck"></i> Deliveries</a>
</div>
<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-start">
    <div>
      <h2><i class="fas fa-utensils me-2" style="color:var(--primary)"></i>All Food Items</h2>
      <p>View and manage all food donations across the platform</p>
    </div>
  </div>
  <?php if (isset($msg)): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

  <!-- Search -->
  <form method="GET" class="mb-3 d-flex gap-2">
    <input type="text" name="q" class="form-control" placeholder="Search by food name or location..." value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-primary"><i class="fas fa-search"></i></button>
    <?php if ($search): ?><a href="food.php" class="btn btn-outline-secondary">Clear</a><?php endif; ?>
  </form>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr>
            <th>#</th><th>Food Name</th><th>Donor</th><th>Qty</th>
            <th>Location</th><th>Expiry</th><th>Requests</th><th>Status</th><th>Action</th>
          </tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($foods) === 0): ?>
            <tr><td colspan="9" class="text-center py-5 text-muted">No food items found.</td></tr>
          <?php endif; ?>
          <?php $sl = 1 ?>
          <?php while ($f = mysqli_fetch_assoc($foods)):
            $expired = strtotime($f['expiry']) < time();
          ?>
          <tr class="<?= $expired ? 'table-danger' : '' ?>">
            <td><?= $sl++ ?></td> 
            <td><strong><?= htmlspecialchars($f['food_name']) ?></strong></td>
            <td><?= htmlspecialchars($f['donor_name']) ?></td>
            <td><?= $f['quantity'] ?> <?= $f['unit'] ?></td>
            <td><?= htmlspecialchars($f['location']) ?></td>
            <td>
              <?php if ($expired): ?>
                <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> EXPIRED</span>
              <?php else: ?>
                <?= date('d M, h:i A', strtotime($f['expiry'])) ?>
              <?php endif; ?>
            </td>
            <td><span class="badge bg-secondary"><?= $f['req_count'] ?></span></td>
            <td><span class="status-badge badge-<?= $f['status'] ?>"><?= $f['status'] ?></span></td>
            <td>
              <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Delete this food item?')">
                 <i class="fas fa-trash"></i>
              </a>
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
