<?php
// donor/add_food.php
require_once '../includes/config.php';
requireRole('donor');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_name   = clean($conn, $_POST['food_name']);
    $quantity    = (int)$_POST['quantity'];
    $unit        = clean($conn, $_POST['unit']);
    $location    = clean($conn, $_POST['location']);
    $expiry      = clean($conn, $_POST['expiry']);
    $description = clean($conn, $_POST['description']);
    $donor_id    = $_SESSION['user_id'];

    if (empty($food_name) || empty($location) || empty($expiry) || $quantity < 1) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($expiry) <= time()) {
        $error = 'Expiry time must be in the future.';
    } else {
        mysqli_query($conn, "
            INSERT INTO food (donor_id, food_name, quantity, unit, location, expiry, description)
            VALUES ($donor_id, '$food_name', $quantity, '$unit', '$location', '$expiry', '$description')
        ");
        $success = 'Food donation added successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Add Food — Donor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <div class="sidebar-label">Donor Menu</div>
  <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="add_food.php"  class="nav-link active"><i class="fas fa-plus-circle"></i> Add Food</a>
  <a href="my_food.php"   class="nav-link"><i class="fas fa-utensils"></i> My Donations</a>
  <a href="requests.php"  class="nav-link"><i class="fas fa-inbox"></i> Requests</a>
</div>
<div class="main-content">
  <div class="page-header">
    <h2><i class="fas fa-plus-circle me-2" style="color:var(--primary)"></i>Add Food Donation</h2>
    <p>Share your excess food with people who need it.</p>
  </div>

  <?php if ($error):   ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="my_food.php">View my donations →</a></div><?php endif; ?>

  <div class="card" style="max-width:620px;">
    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Food Name *</label>
          <input type="text" name="food_name" class="form-control" placeholder="e.g. Chicken Biriyani, Rice & Dal" required>
        </div>
        <div class="row">
          <div class="col-6 mb-3">
            <label class="form-label">Quantity *</label>
            <input type="number" name="quantity" class="form-control" placeholder="e.g. 30" min="1" required>
          </div>
          <div class="col-6 mb-3">
            <label class="form-label">Unit</label>
            <select name="unit" class="form-select">
              <option value="plates">Plates</option>
              <option value="kg">Kilograms (kg)</option>
              <option value="packets">Packets</option>
              <option value="boxes">Boxes</option>
              <option value="liters">Liters</option>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Pickup Location *</label>
          <input type="text" name="location" class="form-control" placeholder="e.g. Narayanganj, near bus stand" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Expiry / Best Before *</label>
          <input type="datetime-local" name="expiry" class="form-control" required
                 min="<?= date('Y-m-d\TH:i') ?>">
          <div class="form-text">Set when the food will no longer be safe to eat.</div>
        </div>
        <div class="mb-4">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="2"
                    placeholder="Any notes about the food (e.g. vegetarian, contains nuts)..."></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary px-4">
            <i class="fas fa-check me-2"></i>Add Donation
          </button>
          <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
