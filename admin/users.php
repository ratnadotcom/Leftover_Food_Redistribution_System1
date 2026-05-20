<?php
// ======================================
// admin/users.php
// Admin panel to manage all users
// ======================================


// Include database configuration
// and helper functions
require_once '../includes/config.php';


// Restrict access only for admins
requireRole('admin');



// ======================================
// DELETE USER
// Runs when admin clicks delete button
// ======================================

if (isset($_GET['delete'])) {

    // Get user ID from URL
    $id = (int)$_GET['delete'];


    // Prevent admin from deleting own account
    if ($id !== $_SESSION['user_id']) {

        // Delete selected user
        mysqli_query($conn,
        "DELETE FROM users WHERE id=$id");

        // Success message
        $msg = 'User deleted successfully.';
    }
}



// ======================================
// ROLE FILTER
// Filter users by role
// ======================================

// Get role from URL
$filter = isset($_GET['role'])

? clean($conn, $_GET['role'])

: '';


// Dynamic WHERE condition
// If filter exists → show selected role
// Otherwise → show all except admin
$where = $filter

? "WHERE role='$filter'"

: "WHERE role != 'admin'";



// ======================================
// FETCH USERS
// Get all user information along with
// activity statistics
// ======================================

$users = mysqli_query($conn, "

    SELECT u.*,

      -- Count donated food items
      (SELECT COUNT(*)
       FROM food
       WHERE donor_id=u.id) AS food_count,

      -- Count receiver requests
      (SELECT COUNT(*)
       FROM requests
       WHERE receiver_id=u.id) AS req_count

    FROM users u

    -- Apply role filter
    $where

    -- Show latest users first
    ORDER BY u.created_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>All Users — Admin</title>


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


    <!-- Users Page -->
    <a href="users.php"
    class="nav-link active">

        <i class="fas fa-users"></i>

        All Users

    </a>


    <!-- Food Management -->
    <a href="food.php"
    class="nav-link">

        <i class="fas fa-utensils"></i>

        Food Items

    </a>


    <!-- Sidebar Label -->
    <div class="sidebar-label">

        Manage

    </div>


    <!-- Request Management -->
    <a href="requests.php"
    class="nav-link">

        <i class="fas fa-inbox"></i>

        Requests

    </a>


    <!-- Delivery Management -->
    <a href="delivery.php"
    class="nav-link">

        <i class="fas fa-truck"></i>

        Deliveries

    </a>

</div>



<!-- ======================================
     MAIN CONTENT AREA
====================================== -->

<div class="main-content">


    <!-- PAGE HEADER -->
    <div class="page-header">

        <h2>

            <i class="fas fa-users me-2"
            style="color:var(--primary)"></i>

            All Users

        </h2>

    </div>



    <!-- SUCCESS MESSAGE -->
    <?php if (isset($msg)): ?>

        <div class="alert alert-success">

            <?= $msg ?>

        </div>

    <?php endif; ?>



    <!-- ======================================
         FILTER BUTTONS
         Filter users by role
    ====================================== -->

    <div class="mb-3 d-flex gap-2">


        <!-- Show All Users -->
        <a href="users.php"

        class="btn btn-sm
        <?= !$filter
        ? 'btn-primary'
        : 'btn-outline-secondary' ?>">

            All

        </a>



        <!-- Donor Filter -->
        <a href="?role=donor"

        class="btn btn-sm
        <?= $filter==='donor'
        ? 'btn-success'
        : 'btn-outline-success' ?>">

            Donors

        </a>



        <!-- Receiver Filter -->
        <a href="?role=receiver"

        class="btn btn-sm
        <?= $filter==='receiver'
        ? 'btn-primary'
        : 'btn-outline-primary' ?>">

            Receivers

        </a>



        <!-- Delivery Staff Filter -->
        <a href="?role=delivery"

        class="btn btn-sm
        <?= $filter==='delivery'
        ? 'btn-warning'
        : 'btn-outline-warning' ?>">

            Delivery

        </a>

    </div>



    <!-- ======================================
         USERS TABLE CARD
    ====================================== -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">


                <!-- USERS TABLE -->
                <table class="table table-hover mb-0">

                    <thead>

                    <tr>

                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Joined</th>
                        <th>Action</th>

                    </tr>

                    </thead>

                    <tbody>


                    <!-- SERIAL NUMBER -->
                    <?php $serial = 1; ?>


                    <!-- ======================================
                         LOOP THROUGH USERS
                    ====================================== -->

                    <?php while (
                    $u = mysqli_fetch_assoc($users)): ?>

                    <tr>


                        <!-- SERIAL -->
                        <td>

                            <?= $serial++ ?>

                        </td>



                        <!-- USER NAME -->
                        <td>

                            <div class="d-flex
                            align-items-center gap-2">


                                <!-- User Avatar -->
                                <div class="avatar"

                                style="width:28px;
                                height:28px;
                                font-size:0.75rem;">

                                    <!-- First letter of username -->
                                    <?= strtoupper(
                                    substr($u['name'],0,1)) ?>

                                </div>


                                <!-- Full Name -->
                                <?= htmlspecialchars(
                                $u['name']) ?>

                            </div>

                        </td>



                        <!-- EMAIL -->
                        <td>

                            <?= htmlspecialchars(
                            $u['email']) ?>

                        </td>



                        <!-- PHONE -->
                        <td>

                            <!-- Show dash if phone empty -->
                            <?= $u['phone']
                            ?: '—' ?>

                        </td>



                        <!-- ADDRESS -->
                        <td>

                            <!-- Show dash if address empty -->
                            <?= htmlspecialchars(
                            $u['address']
                            ?: '—') ?>

                        </td>



                        <!-- ROLE -->
                        <td>

                            <?php

                            // Role color mapping
                            $rc = [

                                'donor'   => 'success',
                                'receiver'=> 'primary',
                                'delivery'=> 'warning',
                                'admin'   => 'danger'

                            ];

                            ?>


                            <!-- Role Badge -->
                            <span class="badge
                            bg-<?= $rc[$u['role']]
                            ?? 'secondary' ?>">

                                <?= ucfirst(
                                $u['role']) ?>

                            </span>

                        </td>



                        <!-- USER ACTIVITY -->
                        <td>

                            <?php if (
                            $u['role'] === 'donor'): ?>


                                <!-- Donor Activity -->
                                <span class="badge
                                bg-light text-dark">

                                    <?= $u['food_count'] ?>
                                    foods

                                </span>

                            <?php elseif (
                            $u['role'] === 'receiver'): ?>


                                <!-- Receiver Activity -->
                                <span class="badge
                                bg-light text-dark">

                                    <?= $u['req_count'] ?>
                                    requests

                                </span>

                            <?php else: ?>


                                <!-- Delivery Staff -->
                                <span class="badge
                                bg-light text-dark">

                                    Delivery Staff

                                </span>

                            <?php endif; ?>

                        </td>



                        <!-- JOIN DATE -->
                        <td style="font-size:0.8rem;">


                            <!-- Format join date -->
                            <?= date(
                            'd M Y',
                            strtotime($u['created_at'])) ?>

                        </td>



                        <!-- DELETE ACTION -->
                        <td>

                            <!-- Delete User Button -->
                            <a href="?delete=<?= $u['id'] ?>"

                            class="btn btn-sm
                            btn-outline-danger"

                            onclick="return confirm(
                            'Delete this user?')">

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
