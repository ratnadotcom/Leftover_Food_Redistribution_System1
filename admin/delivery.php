```php id="f9x2mb"
<?php
// admin/delivery.php — Manage deliveries

require_once '../includes/config.php';

requireRole('admin');

$msg = '';

// ======================================
// UPDATE DELIVERY STATUS
// ======================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
&& isset($_POST['update_delivery'])) {

    $id      = (int)$_POST['delivery_id'];

    $status  = clean($conn,
    $_POST['delivery_status']);

    $person  = clean($conn,
    $_POST['delivery_person']);

    $contact = clean($conn,
    $_POST['contact']);

    $notes   = clean($conn,
    $_POST['notes']);

    mysqli_begin_transaction($conn);

    try{

        // Update delivery

        mysqli_query($conn, "

            UPDATE delivery

            SET delivery_status='$status',

            delivery_person='$person',

            contact='$contact',

            notes='$notes'

            WHERE id=$id

        ");

        // Get request ID

        $d = mysqli_fetch_assoc(

            mysqli_query($conn,
            "SELECT request_id
            FROM delivery
            WHERE id=$id")

        );

        $request_id = $d['request_id'];

        // ======================================
        // SYNCHRONIZE STATUS
        // ======================================

        if ($status === 'assigned') {

            mysqli_query($conn,
            "UPDATE requests
            SET status='assigned'
            WHERE id=$request_id");

        }

        elseif ($status === 'picked_up') {

            mysqli_query($conn,
            "UPDATE requests
            SET status='assigned'
            WHERE id=$request_id");

        }

        elseif ($status === 'delivered') {

            // Request complete

            mysqli_query($conn,
            "UPDATE requests
            SET status='completed'
            WHERE id=$request_id");

            // Food complete

            $req = mysqli_fetch_assoc(

                mysqli_query($conn,
                "SELECT food_id
                FROM requests
                WHERE id=$request_id")

            );

            mysqli_query($conn,
            "UPDATE food
            SET status='completed'
            WHERE id={$req['food_id']}");

        }

        elseif ($status === 'cancelled') {

            // Cancel request

            mysqli_query($conn,
            "UPDATE requests
            SET status='cancelled'
            WHERE id=$request_id");

            // Restore food

            $req = mysqli_fetch_assoc(

                mysqli_query($conn,
                "SELECT food_id
                FROM requests
                WHERE id=$request_id")

            );

            mysqli_query($conn,
            "UPDATE food
            SET status='available'
            WHERE id={$req['food_id']}");

        }

        mysqli_commit($conn);

        $msg = 'Delivery updated successfully!';

    }catch(Exception $e){

        mysqli_rollback($conn);

        $msg = $e->getMessage();
    }
}

// ======================================
// FETCH DELIVERIES
// ======================================

$deliveries = mysqli_query($conn, "

    SELECT d.*,

           r.status AS req_status,

           u.name AS receiver_name,
           u.phone AS receiver_phone,
           u.address AS receiver_addr,

           f.food_name,
           f.quantity,
           f.unit,
           f.location

    FROM delivery d

    JOIN requests r
    ON d.request_id = r.id

    JOIN users u
    ON r.receiver_id = u.id

    JOIN food f
    ON r.food_id = f.id

    ORDER BY d.updated_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>Deliveries — Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
rel="stylesheet">

<link href="../css/style.css"
rel="stylesheet">

</head>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- SIDEBAR -->

<div class="sidebar">

    <a href="dashboard.php"
    class="nav-link">

        <i class="fas fa-tachometer-alt"></i>
        Dashboard

    </a>

    <a href="users.php"
    class="nav-link">

        <i class="fas fa-users"></i>
        All Users

    </a>

    <a href="food.php"
    class="nav-link">

        <i class="fas fa-utensils"></i>
        Food Items

    </a>

    <div class="sidebar-label">

        Manage

    </div>

    <a href="requests.php"
    class="nav-link">

        <i class="fas fa-inbox"></i>
        Requests

    </a>

    <a href="delivery.php"
    class="nav-link active">

        <i class="fas fa-truck"></i>
        Deliveries

    </a>

</div>

<!-- MAIN CONTENT -->

<div class="main-content">

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

    <?php if ($msg): ?>

        <div class="alert alert-success">

            <?= $msg ?>

        </div>

    <?php endif; ?>

    <!-- DELIVERY TABLE -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

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

                    <?php $serial = 1; ?>

                    <?php while($d = mysqli_fetch_assoc($deliveries)): ?>

                    <tr>

                        <!-- SERIAL -->

                        <td>

                            <?= $serial++ ?>

                        </td>

                        <!-- RECEIVER -->

                        <td>

                            <strong>

                                <?= htmlspecialchars($d['receiver_name']) ?>

                            </strong>

                            <br>

                            <small class="text-muted">

                                <?= $d['receiver_phone'] ?>

                            </small>

                        </td>

                        <!-- FOOD -->

                        <td>

                            <?= htmlspecialchars($d['food_name']) ?>

                        </td>

                        <!-- QUANTITY -->

                        <td>

                            <?= $d['quantity'] ?>
                            <?= $d['unit'] ?>

                        </td>

                        <!-- LOCATION -->

                        <td>

                            <i class="fas fa-map-marker-alt text-danger me-1"></i>

                            <?= htmlspecialchars($d['location']) ?>

                        </td>

                        <!-- DELIVERY PERSON -->

                        <td>

                            <?= htmlspecialchars($d['delivery_person']) ?>

                            <br>

                            <small class="text-muted">

                                <?= htmlspecialchars($d['contact']) ?>

                            </small>

                        </td>

                        <!-- STATUS -->

                        <td>

                            <?php

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

                        <!-- ACTION -->

                        <td>

                            <button class="btn btn-sm btn-outline-primary"

                            data-bs-toggle="modal"

                            data-bs-target="#editModal<?= $d['id'] ?>">

                                <i class="fas fa-edit"></i>

                            </button>

                        </td>

                    </tr>

                    <!-- MODAL -->

                    <div class="modal fade"
                    id="editModal<?= $d['id'] ?>"
                    tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Update Delivery

                                    </h5>

                                    <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>

                                </div>

                                <form method="POST">

                                    <div class="modal-body">

                                        <input type="hidden"
                                        name="delivery_id"
                                        value="<?= $d['id'] ?>">

                                        <input type="hidden"
                                        name="update_delivery"
                                        value="1">

                                        <!-- PERSON -->

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

                                        <!-- STATUS -->

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

                                        <!-- NOTES -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Notes

                                            </label>

                                            <textarea name="notes"
                                            class="form-control"
                                            rows="3"><?= htmlspecialchars($d['notes']) ?></textarea>

                                        </div>

                                    </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
```
