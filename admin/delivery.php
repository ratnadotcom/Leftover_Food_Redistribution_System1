<?php
// ======================================
// admin/delivery.php
// Admin panel for managing deliveries
// ======================================

// Include database connection and helper functions
require_once '../includes/config.php';

// Restrict access only for admin users
requireRole('admin');

// Variable for showing success/error messages
$msg = '';


// ======================================
// UPDATE DELIVERY STATUS
// Handles form submission when admin
// updates a delivery
// ======================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
&& isset($_POST['update_delivery'])) {

    // Get delivery ID from form
    $id = (int)$_POST['delivery_id'];

    // Sanitize all user inputs
    $status = clean($conn, $_POST['delivery_status']);

    $person = clean($conn, $_POST['delivery_person']);

    $contact = clean($conn, $_POST['contact']);

    $notes = clean($conn, $_POST['notes']);

    // Start MySQL transaction
    // so all related queries run safely together
    mysqli_begin_transaction($conn);

    try {

        // ======================================
        // UPDATE DELIVERY TABLE
        // Update delivery information
        // ======================================

        mysqli_query($conn, "

            UPDATE delivery

            SET delivery_status='$status',

                delivery_person='$person',

                contact='$contact',

                notes='$notes'

            WHERE id=$id

        ");

        // ======================================
        // FETCH RELATED REQUEST ID
        // Every delivery belongs to a request
        // ======================================

        $d = mysqli_fetch_assoc(

            mysqli_query($conn,
            "SELECT request_id
             FROM delivery
             WHERE id=$id")

        );

        // Store request ID
        $request_id = $d['request_id'];



        // ======================================
        // SYNCHRONIZE REQUEST & FOOD STATUS
        // Keep request and food tables updated
        // based on delivery status
        // ======================================


        // --------------------------------------
        // CASE 1: Delivery Assigned
        // --------------------------------------
        if ($status === 'assigned') {

            // Update request status
            mysqli_query($conn,
            "UPDATE requests
             SET status='assigned'
             WHERE id=$request_id");

        }


        // --------------------------------------
        // CASE 2: Food Picked Up
        // --------------------------------------
        elseif ($status === 'picked_up') {

            // Keep request marked as assigned
            mysqli_query($conn,
            "UPDATE requests
             SET status='assigned'
             WHERE id=$request_id");

        }


        // --------------------------------------
        // CASE 3: Delivery Completed
        // --------------------------------------
        elseif ($status === 'delivered') {

            // Mark request as completed
            mysqli_query($conn,
            "UPDATE requests
             SET status='completed'
             WHERE id=$request_id");

            // Get food ID linked with request
            $req = mysqli_fetch_assoc(

                mysqli_query($conn,
                "SELECT food_id
                 FROM requests
                 WHERE id=$request_id")

            );

            // Mark food as completed/unavailable
            mysqli_query($conn,
            "UPDATE food
             SET status='completed'
             WHERE id={$req['food_id']}");

        }


        // --------------------------------------
        // CASE 4: Delivery Cancelled
        // --------------------------------------
        elseif ($status === 'cancelled') {

            // Cancel request
            mysqli_query($conn,
            "UPDATE requests
             SET status='cancelled'
             WHERE id=$request_id");

            // Get related food ID
            $req = mysqli_fetch_assoc(

                mysqli_query($conn,
                "SELECT food_id
                 FROM requests
                 WHERE id=$request_id")

            );

            // Restore food availability
            mysqli_query($conn,
            "UPDATE food
             SET status='available'
             WHERE id={$req['food_id']}");

        }

        // Save all database changes permanently
        mysqli_commit($conn);

        // Success message
        $msg = 'Delivery updated successfully!';

    } catch(Exception $e) {

        // Rollback transaction if error occurs
        mysqli_rollback($conn);

        // Show error message
        $msg = $e->getMessage();
    }
}



// ======================================
// FETCH ALL DELIVERIES
// Query joins multiple tables to show
// complete delivery information
// ======================================

$deliveries = mysqli_query($conn, "

    SELECT d.*,

           -- Request table data
           r.status AS req_status,

           -- Receiver information
           u.name AS receiver_name,
           u.phone AS receiver_phone,
           u.address AS receiver_addr,

           -- Food information
           f.food_name,
           f.quantity,
           f.unit,
           f.location

    FROM delivery d

    -- Join request table
    JOIN requests r
    ON d.request_id = r.id

    -- Join user table for receiver info
    JOIN users u
    ON r.receiver_id = u.id

    -- Join food table for food details
    JOIN food f
    ON r.food_id = f.id

    -- Show latest updated deliveries first
    ORDER BY d.updated_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>Deliveries — Admin</title>

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

<!-- Include navbar -->
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
    class="nav-link active">

        <i class="fas fa-truck"></i>
        Deliveries

    </a>

</div>



<!-- ======================================
     MAIN CONTENT AREA
====================================== -->

<div class="main-content">

    <!-- Page Header -->
    <div class="page-header">

        <h2>

            <i class="fas fa-truck me-2"
            style="color:var(--primary)"></i>

            Delivery Management

        </h2>

        <p>

            Assign and track all food deliveries

        </p>

    </div>


    <!-- Success/Error Alert -->
    <?php if ($msg): ?>

        <div class="alert alert-success">

            <?= $msg ?>

        </div>

    <?php endif; ?>


    <!-- ======================================
         DELIVERY TABLE CARD
    ====================================== -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <!-- Delivery Data Table -->
                <table class="table table-hover mb-0">

                    <thead>

                    <tr>

                        <th>#</th>
                        <th>Receiver</th>
                        <th>Food</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Delivery Person</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                    </thead>

                    <tbody>

                    <!-- Serial number counter -->
                    <?php $serial = 1; ?>

                    <!-- Loop through all deliveries -->
                    <?php while($d = mysqli_fetch_assoc($deliveries)): ?>

                    <tr>

                        <!-- SERIAL NUMBER -->
                        <td>

                            <?= $serial++ ?>

                        </td>


                        <!-- RECEIVER INFORMATION -->
                        <td>

                            <strong>

                                <?= htmlspecialchars($d['receiver_name']) ?>

                            </strong>

                            <br>

                            <small class="text-muted">

                                <?= $d['receiver_phone'] ?>

                            </small>

                        </td>


                        <!-- FOOD NAME -->
                        <td>

                            <?= htmlspecialchars($d['food_name']) ?>

                        </td>


                        <!-- FOOD QUANTITY -->
                        <td>

                            <?= $d['quantity'] ?>
                            <?= $d['unit'] ?>

                        </td>


                        <!-- DELIVERY LOCATION -->
                        <td>

                            <i class="fas fa-map-marker-alt text-danger me-1"></i>

                            <?= htmlspecialchars($d['location']) ?>

                        </td>


                        <!-- DELIVERY PERSON DETAILS -->
                        <td>

                            <?= htmlspecialchars($d['delivery_person']) ?>

                            <br>

                            <small class="text-muted">

                                <?= htmlspecialchars($d['contact']) ?>

                            </small>

                        </td>


                        <!-- DELIVERY STATUS -->
                        <td>

                            <?php

                            // Show different badge colors
                            // depending on delivery status

                            if($d['delivery_status'] == 'assigned'){

                                echo "<span class='badge bg-primary'>
                                Assigned
                                </span>";

                            }

                            elseif($d['delivery_status'] == 'picked_up'){

                                echo "<span class='badge bg-warning text-dark'>
                                Picked Up
                                </span>";

                            }

                            elseif($d['delivery_status'] == 'delivered'){

                                echo "<span class='badge bg-success'>
                                Delivered
                                </span>";

                            }

                            elseif($d['delivery_status'] == 'cancelled'){

                                echo "<span class='badge bg-danger'>
                                Cancelled
                                </span>";

                            }

                            ?>

                        </td>


                        <!-- ACTION BUTTON -->
                        <td>

                            <!-- Button opens update modal -->
                            <button class="btn btn-sm btn-outline-primary"

                            data-bs-toggle="modal"

                            data-bs-target="#editModal<?= $d['id'] ?>">

                                <i class="fas fa-edit"></i>

                            </button>

                        </td>

                    </tr>



                    <!-- ======================================
                         UPDATE DELIVERY MODAL
                    ====================================== -->

                    <div class="modal fade"
                    id="editModal<?= $d['id'] ?>"
                    tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Update Delivery

                                    </h5>

                                    <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>

                                </div>


                                <!-- Update Form -->
                                <form method="POST">

                                    <div class="modal-body">

                                        <!-- Hidden Delivery ID -->
                                        <input type="hidden"
                                        name="delivery_id"
                                        value="<?= $d['id'] ?>">

                                        <!-- Hidden Form Identifier -->
                                        <input type="hidden"
                                        name="update_delivery"
                                        value="1">


                                        <!-- DELIVERY PERSON -->
                                        <div class="mb-3">

                                            <label class="form-label">

                                                Delivery Person

                                            </label>

                                            <input type="text"
                                            name="delivery_person"
                                            class="form-control"

                                            value="<?= htmlspecialchars($d['delivery_person']) ?>">

                                        </div>


                                        <!-- CONTACT -->
                                        <div class="mb-3">

                                            <label class="form-label">

                                                Contact

                                            </label>

                                            <input type="text"
                                            name="contact"
                                            class="form-control"

                                            value="<?= htmlspecialchars($d['contact']) ?>">

                                        </div>


                                        <!-- DELIVERY STATUS -->
                                        <div class="mb-3">

                                            <label class="form-label">

                                                Status

                                            </label>

                                            <select name="delivery_status"
                                            class="form-select">

                                                <option value="assigned">

                                                    Assigned

                                                </option>

                                                <option value="picked_up">

                                                    Picked Up

                                                </option>

                                                <option value="delivered">

                                                    Delivered

                                                </option>

                                                <option value="cancelled">

                                                    Cancelled

                                                </option>

                                            </select>

                                        </div>


                                        <!-- DELIVERY NOTES -->
                                        <div class="mb-3">

                                            <label class="form-label">

                                                Notes

                                            </label>

                                            <textarea name="notes"
                                            class="form-control"
                                            rows="3"><?= htmlspecialchars($d['notes']) ?></textarea>

                                        </div>

                                    </div>


                                    <!-- Modal Footer -->
                                    <div class="modal-footer">

                                        <button type="submit"
                                        class="btn btn-primary">

                                            Update Delivery

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

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
