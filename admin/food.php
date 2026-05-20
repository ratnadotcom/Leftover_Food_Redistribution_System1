<?php
// ======================================
// admin/food.php
// Admin panel to view and manage all
// food donations in the system
// ======================================


// Include database connection and helper functions
require_once '../includes/config.php';


// Restrict page access only for admin users
requireRole('admin');



// ======================================
// DELETE FOOD ITEM
// Runs when admin clicks delete button
// ======================================

if (isset($_GET['delete'])) {

    // Get food ID from URL
    $id = (int)$_GET['delete'];

    // Delete selected food item
    mysqli_query($conn,
    "DELETE FROM food WHERE id=$id");

    // Success message
    $msg = 'Food item deleted.';
}



// ======================================
// SEARCH FUNCTIONALITY
// Search food by food name or location
// ======================================

// Get search keyword from URL
$search = isset($_GET['q'])
? clean($conn, $_GET['q'])
: '';


// Dynamic WHERE condition
// If search exists → filter results
// Otherwise → show all foods
$where = $search

? "WHERE f.food_name LIKE '%$search%'
   OR f.location LIKE '%$search%'"

: '';



// ======================================
// FETCH ALL FOOD ITEMS
// Join food table with users table
// and count related requests
// ======================================

$foods = mysqli_query($conn, "

    SELECT f.*,

           -- Get donor name from users table
           u.name AS donor_name,

           -- Count how many requests exist
           -- for each food item
           (SELECT COUNT(*)
            FROM requests
            WHERE food_id=f.id) AS req_count

    FROM food f

    -- Join users table using donor_id
    JOIN users u
    ON f.donor_id = u.id

    -- Apply search filter if exists
    $where

    -- Show latest food items first
    ORDER BY f.created_at DESC

");

?>

<!DOCTYPE html>

<html lang='en'>

<head>

<meta charset="UTF-8">

<title>Food Items — Admin</title>


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Font Awesome Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
rel="stylesheet">


<!-- Custom CSS -->
<link href="../css/style.css"
rel="stylesheet">

</head>

<body>


<!-- Include Navbar -->
<?php include '../includes/navbar.php'; ?>



<!-- ======================================
     SIDEBAR NAVIGATION
====================================== -->

<div class="sidebar">

  <!-- Dashboard -->
  <a href="dashboard.php"
  class="nav-link">

    <i class="fas fa-tachometer-alt"></i>

    Dashboard

  </a>


  <!-- User Management -->
  <a href="users.php"
  class="nav-link">

    <i class="fas fa-users"></i>

    All Users

  </a>


  <!-- Food Management -->
  <a href="food.php"
  class="nav-link active">

    <i class="fas fa-utensils"></i>

    Food Items

  </a>


  <!-- Sidebar Section Label -->
  <div class="sidebar-label">

    Manage

  </div>


  <!-- Requests -->
  <a href="requests.php"
  class="nav-link">

    <i class="fas fa-inbox"></i>

    Requests

  </a>


  <!-- Deliveries -->
  <a href="delivery.php"
  class="nav-link">

    <i class="fas fa-truck"></i>

    Deliveries

  </a>

</div>



// ======================================
// MAIN CONTENT AREA
// ======================================

<div class="main-content">


  <!-- PAGE HEADER -->
  <div class="page-header
              d-flex
              justify-content-between
              align-items-start">

    <div>

      <h2>

        <i class="fas fa-utensils me-2"
        style="color:var(--primary)"></i>

        All Food Items

      </h2>

      <p>

        View and manage all food donations
        across the platform

      </p>

    </div>

  </div>



  <!-- SUCCESS MESSAGE -->
  <?php if (isset($msg)): ?>

    <div class="alert alert-success">

      <?= $msg ?>

    </div>

  <?php endif; ?>



  <!-- ======================================
       SEARCH FORM
  ====================================== -->

  <form method="GET"
  class="mb-3 d-flex gap-2">

    <!-- Search Input -->
    <input type="text"
    name="q"
    class="form-control"

    placeholder="Search by food name or location..."

    value="<?= htmlspecialchars($search) ?>">


    <!-- Search Button -->
    <button class="btn btn-primary">

      <i class="fas fa-search"></i>

    </button>


    <!-- Clear Search Button -->
    <?php if ($search): ?>

      <a href="food.php"
      class="btn btn-outline-secondary">

        Clear

      </a>

    <?php endif; ?>

  </form>



  <!-- ======================================
       FOOD TABLE CARD
  ====================================== -->

  <div class="card">

    <div class="card-body p-0">

      <div class="table-responsive">


        <!-- FOOD ITEMS TABLE -->
        <table class="table table-hover mb-0">

          <thead>

            <tr>

              <th>#</th>
              <th>Food Name</th>
              <th>Donor</th>
              <th>Qty</th>
              <th>Location</th>
              <th>Expiry</th>
              <th>Requests</th>
              <th>Status</th>
              <th>Action</th>

            </tr>

          </thead>

          <tbody>


          <!-- ======================================
               SHOW MESSAGE IF NO FOOD FOUND
          ====================================== -->

          <?php if (mysqli_num_rows($foods) === 0): ?>

            <tr>

              <td colspan="9"
              class="text-center py-5 text-muted">

                No food items found.

              </td>

            </tr>

          <?php endif; ?>



          <!-- SERIAL NUMBER -->
          <?php $sl = 1 ?>


          <!-- ======================================
               LOOP THROUGH ALL FOOD ITEMS
          ====================================== -->

          <?php while ($f = mysqli_fetch_assoc($foods)):

            // Check whether food is expired
            $expired = strtotime($f['expiry']) < time();

          ?>



          <!-- Highlight expired food rows -->
          <tr class="<?= $expired ? 'table-danger' : '' ?>">


            <!-- SERIAL -->
            <td>

              <?= $sl++ ?>

            </td>


            <!-- FOOD NAME -->
            <td>

              <strong>

                <?= htmlspecialchars($f['food_name']) ?>

              </strong>

            </td>


            <!-- DONOR NAME -->
            <td>

              <?= htmlspecialchars($f['donor_name']) ?>

            </td>


            <!-- QUANTITY -->
            <td>

              <?= $f['quantity'] ?>
              <?= $f['unit'] ?>

            </td>


            <!-- LOCATION -->
            <td>

              <?= htmlspecialchars($f['location']) ?>

            </td>



            <!-- EXPIRY STATUS -->
            <td>

              <?php if ($expired): ?>

                <!-- Expired Warning -->
                <span class="text-danger fw-bold">

                  <i class="fas fa-exclamation-triangle"></i>

                  EXPIRED

                </span>

              <?php else: ?>

                <!-- Show expiry date -->
                <?= date('d M, h:i A',
                strtotime($f['expiry'])) ?>

              <?php endif; ?>

            </td>



            <!-- REQUEST COUNT -->
            <td>

              <span class="badge bg-secondary">

                <?= $f['req_count'] ?>

              </span>

            </td>



            <!-- FOOD STATUS -->
            <td>

              <span class="status-badge
              badge-<?= $f['status'] ?>">

                <?= $f['status'] ?>

              </span>

            </td>



            <!-- DELETE ACTION -->
            <td>

              <!-- Delete Button -->
              <a href="?delete=<?= $f['id'] ?>"

                 class="btn btn-sm btn-outline-danger"

                 onclick="return confirm(
                 'Delete this food item?')">

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



<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
