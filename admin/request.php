<?php
// =====================================
// admin/requests.php
// Admin panel for managing food requests
// =====================================


// Include database connection
// and helper functions
require_once '../includes/config.php';


// Restrict access only for admin users
requireRole('admin');


// Variable for success/error messages
$msg = '';



// =====================================
// APPROVE REQUEST + ASSIGN DELIVERY
// Runs when admin approves a request
// and assigns a delivery person
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
&& isset($_POST['approve_assign'])) {

    // Get request ID from form
    $request_id = (int)$_POST['request_id'];

    // Sanitize delivery person input
    $delivery_person = clean($conn,
    $_POST['delivery_person']);

    // Sanitize contact number
    $contact = clean($conn,
    $_POST['contact']);



    // Start database transaction
    mysqli_begin_transaction($conn);

    try {

        // =====================================
        // FETCH REQUEST INFORMATION
        // =====================================

        $req = mysqli_fetch_assoc(

            mysqli_query($conn,
            "SELECT * FROM requests
             WHERE id=$request_id")

        );

        // Check if request exists
        if(!$req){

            throw new Exception(
            "Request not found");

        }



        // =====================================
        // FETCH FOOD INFORMATION
        // =====================================

        $food = mysqli_fetch_assoc(

            mysqli_query($conn,
            "SELECT * FROM food
             WHERE id={$req['food_id']}")

        );

        // Check if food exists
        if(!$food){

            throw new Exception(
            "Food not found");

        }



        // =====================================
        // CHECK AVAILABLE QUANTITY
        // Prevent over-requesting
        // =====================================

        if($req['requested_quantity']
        > $food['quantity']){

            throw new Exception(
            "Insufficient quantity");

        }



        // =====================================
        // CALCULATE NEW FOOD QUANTITY
        // =====================================

        $new_qty =
        $food['quantity']
        - $req['requested_quantity'];



        // =====================================
        // UPDATE FOOD TABLE
        // Reduce food quantity and
        // mark food as reserved
        // =====================================

        mysqli_query($conn,
        "UPDATE food

         SET quantity='$new_qty',
             status='reserved'

         WHERE id={$food['id']}");



        // =====================================
        // UPDATE REQUEST STATUS
        // =====================================

        mysqli_query($conn,
        "UPDATE requests

         SET status='assigned'

         WHERE id=$request_id");



        // =====================================
        // CREATE DELIVERY RECORD
        // Insert delivery information
        // =====================================

        mysqli_query($conn,

        "INSERT INTO delivery

        (request_id,
         delivery_person,
         contact,
         delivery_status,
         created_at)

         VALUES

        ($request_id,
         '$delivery_person',
         '$contact',
         'assigned',
         NOW())");



        // Save all changes permanently
        mysqli_commit($conn);

        // Success message
        $msg =
        "Request approved and delivery assigned.";

    } catch(Exception $e) {

        // Undo all queries if error occurs
        mysqli_rollback($conn);

        // Show error message
        $msg = $e->getMessage();
    }
}



// =====================================
// REJECT REQUEST
// Runs when admin rejects request
// =====================================

if(isset($_GET['reject'])){

    // Get request ID from URL
    $id = (int)$_GET['reject'];

    // Update request status to cancelled
    mysqli_query($conn,

    "UPDATE requests

     SET status='cancelled'

     WHERE id=$id");

    // Success message
    $msg = "Request cancelled.";
}



// =====================================
// FETCH ALL REQUESTS
// Join multiple tables to show
// complete request information
// =====================================

$requests = mysqli_query($conn, "

SELECT r.*,

       -- Receiver Information
       u.name AS receiver_name,
       u.phone AS receiver_phone,

       -- Food Information
       f.food_name,
       f.quantity AS available_qty,
       f.unit,
       f.location,
       f.status AS food_status,

       -- Delivery Information
       d.delivery_person,
       d.delivery_status

FROM requests r

-- Join users table
JOIN users u
ON r.receiver_id = u.id

-- Join food table
JOIN food f
ON r.food_id = f.id

-- Left join delivery table
-- because some requests may
-- not yet have delivery assigned
LEFT JOIN delivery d
ON r.id = d.request_id

-- Show newest requests first
ORDER BY r.created_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,
initial-scale=1.0">

<title>Requests — Admin</title>


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



<!-- =====================================
     SIDEBAR NAVIGATION
===================================== -->

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

        Users

    </a>


    <!-- Food Management -->
    <a href="food.php"
    class="nav-link">

        <i class="fas fa-utensils"></i>

        Food Items

    </a>


    <!-- Request Management -->
    <a href="requests.php"
    class="nav-link active">

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



<!-- =====================================
     MAIN CONTENT AREA
===================================== -->

<div class="main-content">


    <!-- PAGE HEADER -->
    <div class="page-header">

        <h2>

            <i class="fas fa-inbox me-2"
            style="color:var(--primary)"></i>

            Food Requests

        </h2>

        <p>

            Approve and manage food requests

        </p>

    </div>



    <!-- SUCCESS / ERROR MESSAGE -->
    <?php if($msg): ?>

        <div class="alert alert-success">

            <?= $msg ?>

        </div>

    <?php endif; ?>



    <!-- =====================================
         REQUEST TABLE
    ===================================== -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">


                <!-- REQUESTS DATA TABLE -->
                <table class="table table-hover mb-0">

                    <thead>

                    <tr>

                        <th>#</th>
                        <th>Receiver</th>
                        <th>Food</th>
                        <th>Requested Qty</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Delivery</th>
                        <th>Action</th>

                    </tr>

                    </thead>

                    <tbody>


                    <!-- SERIAL NUMBER -->
                    <?php $serial = 1; ?>


                    <!-- =====================================
                         LOOP THROUGH ALL REQUESTS
                    ===================================== -->

                    <?php while($r =
                    mysqli_fetch_assoc($requests)): ?>

                    <tr>


                        <!-- SERIAL -->
                        <td>

                            <?= $serial++ ?>

                        </td>



                        <!-- RECEIVER INFORMATION -->
                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                $r['receiver_name']) ?>

                            </strong>

                            <br>

                            <small class="text-muted">

                                <?= $r['receiver_phone'] ?>

                            </small>

                        </td>



                        <!-- FOOD INFORMATION -->
                        <td>

                            <?= htmlspecialchars(
                            $r['food_name']) ?>

                            <br>

                            <small class="text-muted">

                                <?= htmlspecialchars(
                                $r['location']) ?>

                            </small>

                        </td>



                        <!-- REQUESTED QUANTITY -->
                        <td>

                            <?= $r['requested_quantity'] ?>
                            <?= $r['unit'] ?>

                        </td>



                        <!-- AVAILABLE QUANTITY -->
                        <td>

                            <?= $r['available_qty'] ?>
                            <?= $r['unit'] ?>

                        </td>



                        <!-- REQUEST STATUS -->
                        <td>

                            <?php

                            // Different badge colors
                            // for different request status

                            if($r['status'] == 'pending'){

                                echo "<span class='badge
                                bg-warning text-dark'>

                                Pending

                                </span>";

                            }

                            elseif($r['status']
                            == 'assigned'){

                                echo "<span class='badge
                                bg-primary'>

                                Assigned

                                </span>";

                            }

                            elseif($r['status']
                            == 'completed'){

                                echo "<span class='badge
                                bg-success'>

                                Completed

                                </span>";

                            }

                            elseif($r['status']
                            == 'cancelled'){

                                echo "<span class='badge
                                bg-danger'>

                                Cancelled

                                </span>";

                            }

                            ?>

                        </td>



                        <!-- DELIVERY INFORMATION -->
                        <td>

                            <?php if(
                            $r['delivery_person']): ?>

                                <!-- Delivery Person -->
                                <span class="badge bg-info">

                                    <?= htmlspecialchars(
                                    $r['delivery_person']) ?>

                                </span>

                                <br>

                                <!-- Delivery Status -->
                                <small>

                                    <?= ucfirst(
                                    $r['delivery_status']) ?>

                                </small>

                            <?php else: ?>

                                <!-- No delivery assigned -->
                                <span class="text-muted">

                                    Not Assigned

                                </span>

                            <?php endif; ?>

                        </td>



                        <!-- ACTION BUTTONS -->
                        <td>

                            <?php if(
                            $r['status']
                            == 'pending'): ?>


                            <!-- APPROVE BUTTON -->
                            <button class="btn
                            btn-sm btn-success"

                            data-bs-toggle="modal"

                            data-bs-target=
                            "#approveModal<?= $r['id'] ?>">

                                <i class="fas fa-check"></i>

                            </button>



                            <!-- REJECT BUTTON -->
                            <a href="?reject=<?= $r['id'] ?>"

                            class="btn btn-sm btn-danger"

                            onclick="return confirm(
                            'Reject this request?')">

                                <i class="fas fa-times"></i>

                            </a>

                            <?php else: ?>

                                <!-- Already Processed -->
                                <span class="text-muted">

                                    Processed

                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>



                    <!-- =====================================
                         APPROVE REQUEST MODAL
                    ===================================== -->

                    <div class="modal fade"
                    id="approveModal<?= $r['id'] ?>"
                    tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">


                                <!-- Modal Header -->
                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Approve & Assign Delivery

                                    </h5>

                                    <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal">
                                    </button>

                                </div>



                                <!-- Approval Form -->
                                <form method="POST">

                                    <div class="modal-body">


                                        <!-- Hidden Request ID -->
                                        <input type="hidden"
                                        name="request_id"
                                        value="<?= $r['id'] ?>">


                                        <!-- Hidden Identifier -->
                                        <input type="hidden"
                                        name="approve_assign"
                                        value="1">



                                        <!-- DELIVERY PERSON -->
                                        <div class="mb-3">

                                            <label class="form-label">

                                                Delivery Person

                                            </label>

                                            <input type="text"
                                            name="delivery_person"
                                            class="form-control"
                                            required>

                                        </div>



                                        <!-- CONTACT NUMBER -->
                                        <div class="mb-3">

                                            <label class="form-label">

                                                Contact Number

                                            </label>

                                            <input type="text"
                                            name="contact"
                                            class="form-control"
                                            required>

                                        </div>

                                    </div>



                                    <!-- Modal Footer -->
                                    <div class="modal-footer">

                                        <button type="submit"
                                        class="btn btn-success">

                                            Approve & Assign

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
