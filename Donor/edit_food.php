<?php
// donor/edit_food.php
require_once '../includes/config.php';
requireRole('donor');
$uid = $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$food = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM food WHERE id=$id AND donor_id=$uid"));
if (!$food) { redirect('my_food.php'); }

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_name   = clean($conn, $_POST['food_name']);
    $quantity    = (int)$_POST['quantity'];
    $unit        = clean($conn, $_POST['unit']);
    $location    = clean($conn, $_POST['location']);
    $expiry      = clean($conn, $_POST['expiry']);
    $description = clean($conn, $_POST['description']);
    $status      = clean($conn, $_POST['status']);

    if (empty($food_name) || empty($location) || $quantity < 1) {
        $error = 'Please fill in required fields.';
    } else {
        mysqli_query($conn, "
            UPDATE food SET food_name='$food_name', quantity=$quantity, unit='$unit',
            location='$location', expiry='$expiry', description='$description', status='$status'
            WHERE id=$id AND donor_id=$uid
        ");
        $success = 'Food item updated successfully!';
        $food = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM food WHERE id=$id"));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Edit Food — Donor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="add_food.php"  class="nav-link"><i class="fas fa-plus-circle"></i> Add Food</a>
  <a href="my_food.php"   class="nav-link active"><i class="fas fa-utensils"></i> My Donations</a>
  <a href="requests.php"  class="nav-link"><i class="fas fa-inbox"></i> Requests</a>
</div>
<div class="main-content">
  <div class="page-header">
    <h2><i class="fas fa-edit me-2" style="color:var(--primary)"></i>Edit Food Item</h2>
  </div>
  <?php if ($error):   ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

  <div class="card" style="max-width:620px;">
    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Food Name *</label>
          <input type="text" name="food_name" class="form-control" value="<?= htmlspecialchars($food['food_name']) ?>" required>
        </div>
        <div class="row">
          <div class="col-6 mb-3">
            <label class="form-label">Quantity *</label>
            <input type="number" name="quantity" class="form-control" value="<?= $food['quantity'] ?>" min="1" required>
          </div>
          <div class="col-6 mb-3">
            <label class="form-label">Unit</label>
            <select name="unit" class="form-select">
              <?php foreach (['plates','kg','packets','boxes','liters'] as $u): ?>
                <option value="<?= $u ?>" <?= $food['unit']===$u?'selected':''?>><?= ucfirst($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Location *</label>
          <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($food['location']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Expiry Time *</label>
          <input type="datetime-local" name="expiry" class="form-control"
                 value="<?= date('Y-m-d\TH:i', strtotime($food['expiry'])) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="available"   <?= $food['status']==='available'   ?'selected':''?>>Available</option>
            <option value="reserved"    <?= $food['status']==='reserved'    ?'selected':''?>>Reserved</option>
            <option value="distributed" <?= $food['status']==='distributed' ?'selected':''?>>Distributed</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($food['description']) ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary px-4">Save Changes</button>
          <a href="my_food.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
