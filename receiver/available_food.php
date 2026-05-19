<?php
// receiver/available_food.php — Browse food & make requests
require_once '../includes/config.php';
requireRole('receiver');
$uid = $_SESSION['user_id'];

$msg = '';

// Submit request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_food'])) {
    $food_id  = (int)$_POST['food_id'];
    $quantity = (int)$_POST['quantity'];
    $message  = clean($conn, $_POST['message']);

    // Check if already requested
    $exists = mysqli_fetch_row(mysqli_query($conn, "
        SELECT id FROM requests WHERE food_id=$food_id AND receiver_id=$uid AND status IN ('pending','approved')
    "));
    if ($exists) {
        $msg = 'danger|You already have an active request for this food.';
    } else {
        mysqli_query($conn, "
            INSERT INTO requests (food_id, receiver_id, quantity, message)
            VALUES ($food_id, $uid, $quantity, '$message')
        ");
        $msg = 'success|Request submitted! You will be notified once approved.';
    }
}

// Search/filter
$search   = isset($_GET['q'])   ? clean($conn, $_GET['q'])   : '';
$location = isset($_GET['loc']) ? clean($conn, $_GET['loc']) : '';

$where = "WHERE f.status='available' AND f.expiry > NOW()";
if ($search)   $where .= " AND f.food_name LIKE '%$search%'";
if ($location) $where .= " AND f.location LIKE '%$location%'";

$foods = mysqli_query($conn, "
    SELECT f.*, u.name AS donor_name, u.phone AS donor_phone
    FROM food f
    JOIN users u ON f.donor_id = u.id
    $where
    ORDER BY f.expiry ASC
");

// Get distinct locations for filter
$locations = mysqli_query($conn, "SELECT DISTINCT location FROM food WHERE status='available' AND expiry > NOW() ORDER BY location");

[$msgType, $msgText] = $msg ? explode('|', $msg, 2) : ['',''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Browse Food — Receiver</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="sidebar">
  <div class="sidebar-label">Receiver Menu</div>
  <a href="dashboard.php"      class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="available_food.php" class="nav-link active"><i class="fas fa-search"></i> Browse Food</a>
  <a href="my_requests.php"    class="nav-link"><i class="fas fa-clipboard-list"></i> My Requests</a>
</div>
<div class="main-content">
  <div class="page-header">
    <h2><i class="fas fa-search me-2" style="color:var(--primary)"></i>Available Food</h2>
    <p>Browse food available for pickup in your area.</p>
  </div>

  <?php if ($msgText): ?><div class="alert alert-<?= $msgType ?>"><?= $msgText ?></div><?php endif; ?>

  <!-- Search + Filter -->
  <form method="GET" class="card p-3 mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Search Food</label>
        <input type="text" name="q" class="form-control" placeholder="e.g. Biriyani, Rice..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Filter by Location</label>
        <select name="loc" class="form-select">
          <option value="">All Locations</option>
          <?php while ($loc = mysqli_fetch_row($locations)): ?>
            <option value="<?= htmlspecialchars($loc[0]) ?>" <?= $location===$loc[0]?'selected':''?>>
              <?= htmlspecialchars($loc[0]) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary flex-fill"><i class="fas fa-search me-1"></i>Search</button>
        <a href="available_food.php" class="btn btn-outline-secondary">Clear</a>
      </div>
    </div>
  </form>

  <!-- Request Modal -->
  <div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-hand-holding-heart me-2 text-success"></i>Request Food</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <input type="hidden" name="request_food" value="1">
          <input type="hidden" name="food_id" id="req_food_id">
          <div class="modal-body">
            <div class="p-3 rounded mb-3" style="background:var(--primary-bg);">
              <strong id="req_food_name"></strong><br>
              <span id="req_food_detail" class="text-muted" style="font-size:0.85rem;"></span>
            </div>
            <div class="mb-3">
              <label class="form-label">Quantity Needed</label>
              <input type="number" name="quantity" id="req_qty" class="form-control" min="1" value="1">
            </div>
            <div class="mb-3">
              <label class="form-label">Message to Donor (optional)</label>
              <textarea name="message" class="form-control" rows="2" placeholder="Why do you need this food?"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Food Cards -->
  <div class="row g-3">
    <?php $count = mysqli_num_rows($foods); ?>
    <?php if ($count === 0): ?>
      <div class="col-12">
        <div class="empty-state card">
          <i class="fas fa-utensils"></i>
          <h5>No food available</h5>
          <p>Try a different search or location filter.</p>
        </div>
      </div>
    <?php endif; ?>
    <?php while ($f = mysqli_fetch_assoc($foods)):
      $hoursLeft  = round((strtotime($f['expiry']) - time()) / 3600, 1);
      $urgentClass = $hoursLeft < 2 ? 'border-danger' : ($hoursLeft < 5 ? 'border-warning' : '');
    ?>
    <div class="col-md-6 col-lg-4">
      <div class="card food-card <?= $urgentClass ?>" style="<?= $urgentClass ? 'border-width:2px;' : '' ?>">
        <div class="food-img" style="<?= $hoursLeft < 2 ? 'background:linear-gradient(135deg,#FFEBEE,#FFCDD2)' : '' ?>">
          <?php
          $emojis = ['🍱','🍛','🍚','🥘','🍲','🥗','🍜','🥙'];
          echo $emojis[crc32($f['food_name']) % count($emojis)];
          ?>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-1">
            <h6 class="fw-700 mb-0"><?= htmlspecialchars($f['food_name']) ?></h6>
            <?php if ($hoursLeft < 2): ?>
              <span class="badge bg-danger" style="font-size:0.7rem;">⚡ URGENT</span>
            <?php endif; ?>
          </div>
          <div class="text-muted mb-2" style="font-size:0.82rem;">
            <i class="fas fa-box me-1"></i><?= $f['quantity'] ?> <?= $f['unit'] ?>
            &nbsp;·&nbsp;<i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($f['location']) ?>
          </div>
          <div class="mb-1" style="font-size:0.78rem;">
            <i class="fas fa-user me-1 text-muted"></i>
            <strong>Donor:</strong> <?= htmlspecialchars($f['donor_name']) ?>
          </div>
          <div class="mb-3 <?= $hoursLeft < 2 ? 'expiry-warn' : 'text-muted' ?>" style="font-size:0.78rem;">
            <i class="fas fa-clock me-1"></i>
            Expires in <strong><?= $hoursLeft ?>h</strong> (<?= date('h:i A', strtotime($f['expiry'])) ?>)
          </div>
          <?php if ($f['description']): ?>
            <p class="text-muted mb-3" style="font-size:0.8rem;"><?= htmlspecialchars($f['description']) ?></p>
          <?php endif; ?>
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#requestModal"
            onclick="fillRequest(<?= $f['id'] ?>, '<?= addslashes($f['food_name']) ?>', '<?= $f['quantity'].' '.$f['unit'].' · '.addslashes($f['location']) ?>', <?= $f['quantity'] ?>)">
            <i class="fas fa-hand-holding-heart me-2"></i>Request This Food
          </button>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function fillRequest(id, name, detail, maxQty) {
  document.getElementById('req_food_id').value   = id;
  document.getElementById('req_food_name').innerText   = name;
  document.getElementById('req_food_detail').innerText = detail;
  document.getElementById('req_qty').max = maxQty;
}
</script>
</body>
</html>
