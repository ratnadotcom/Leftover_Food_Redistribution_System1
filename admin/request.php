```php id="p4x8mz"
<?php
// admin/requests.php — Manage food requests

require_once '../includes/config.php';

requireRole('admin');

$msg = '';

// =====================================
// APPROVE + ASSIGN DELIVERY
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
&& isset($_POST['approve_assign'])) {

    $request_id = (int)$_POST['request_id'];

    $delivery_person = clean($conn,
    $_POST['delivery_person']);

    $contact = clean($conn,
    $_POST['contact']);

    mysqli_begin_transaction($conn);

    try{

        // Request info

        $req = mysqli_fetch_assoc(

            mysqli_query($conn,
            "SELECT * FROM requests
            WHERE id=$request_id")

        );

        if(!$req){

            throw new Exception("Request not found");
        }

        // Food info

        $food = mysqli_fetch_assoc(

            mysqli_query($conn,
            "SELECT * FROM food
            WHERE id={$req['food_id']}")

        );

        if(!$food){

            throw new Exception("Food not found");
        }

        // Check quantity

        if($req['requested_quantity'] > $food['quantity']){

            throw new Exception("Insufficient quantity");
        }

        // Reduce quantity

        $new_qty =
        $food['quantity'] -
        $req['requested_quantity'];

        // Update food

        mysqli_query($conn,
        "UPDATE food

        SET quantity='$new_qty',
        status='reserved'

        WHERE id={$food['id']}");

        // Update request

        mysqli_query($conn,
        "UPDATE requests

        SET status='assigned'

        WHERE id=$request_id");

        // Create delivery

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

        mysqli_commit($conn);

        $msg = "Request approved and delivery assigned.";

    }catch(Exception $e){

        mysqli_rollback($conn);

        $msg = $e->getMessage();
    }
}

// =====================================
// REJECT REQUEST
// =====================================

if(isset($_GET['reject'])){

    $id = (int)$_GET['reject'];

    mysqli_query($conn,

    "UPDATE requests

    SET status='cancelled'

    WHERE id=$id");

    $msg = "Request cancelled.";
}

// =====================================
// FETCH REQUESTS
// =====================================

$requests = mysqli_query($conn, "

SELECT r.*,

u.name AS receiver_name,
u.phone AS receiver_phone,

f.food_name,
f.quantity AS available_qty,
f.unit,
f.location,
f.status AS food_status,

d.delivery_person,
d.delivery_status

FROM requests r

JOIN users u
ON r.receiver_id = u.id

JOIN food f
ON r.food_id = f.id

LEFT JOIN delivery d
ON r.id = d.request_id

ORDER BY r.created_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Requests — Admin</title>

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
        Users

    </a>

    <a href="food.php"
    class="nav-link">

        <i class="fas fa-utensils"></i>
        Food Items

    </a>

    <a href="requests.php"
    class="nav-link active">

        <i class="fas fa-inbox"></i>
        Requests

    </a>

    <a href="delivery.php"
    class="nav-link">

        <i class="fas fa-truck"></i>
        Deliveries

    </a>

</div>

<!-- MAIN CONTENT -->

<div class="main-content">

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

    <?php if($msg): ?>

        <div class="alert alert-success">

            <?= $msg ?>

        </div>

    <?php endif; ?>

    <!-- REQUEST TABLE -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

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

                    <?php $serial = 1; ?>

                    <?php while($r = mysqli_fetch_assoc($requests)): ?>

                    <tr>

                        <!-- SERIAL -->

                        <td>

                            <?= $serial++ ?>

                        </td>

                        <!-- RECEIVER -->

                        <td>

                            <strong>

                                <?= htmlspecialchars($r['receiver_name']) ?>

                            </strong>

                            <br>

                            <small class="text-muted">

                                <?= $r['receiver_phone'] ?>

                            </small>

                        </td>

                        <!-- FOOD -->

                        <td>

                            <?= htmlspecialchars($r['food_name']) ?>

                            <br>

                            <small class="text-muted">

                                <?= htmlspecialchars($r['location']) ?>

                            </small>

                        </td>

                        <!-- REQUESTED QTY -->

                        <td>

                            <?= $r['requested_quantity'] ?>
                            <?= $r['unit'] ?>

                        </td>

                        <!-- AVAILABLE -->

                        <td>

                            <?= $r['available_qty'] ?>
                            <?= $r['unit'] ?>

                        </td>

                        <!-- STATUS -->

                        <td>

                            <?php

                            if($r['status'] == 'pending'){

                                echo "<span class='badge bg-warning text-dark'>
                                Pending
                                </span>";

                            }

                            elseif($r['status'] == 'assigned'){

                                echo "<span class='badge bg-primary'>
                                Assigned
                                </span>";

                            }

                            elseif($r['status'] == 'completed'){

                                echo "<span class='badge bg-success'>
                                Completed
                                </span>";

                            }

                            elseif($r['status'] == 'cancelled'){

                                echo "<span class='badge bg-danger'>
                                Cancelled
                                </span>";

                            }

                            ?>

                        </td>

                        <!-- DELIVERY -->

                        <td>

                            <?php if($r['delivery_person']): ?>

                                <span class="badge bg-info">

                                    <?= htmlspecialchars($r['delivery_person']) ?>

                                </span>

                                <br>

                                <small>

                                    <?= ucfirst($r['delivery_status']) ?>

                                </small>

                            <?php else: ?>

                                <span class="text-muted">

                                    Not Assigned

                                </span>

                            <?php endif; ?>

                        </td>

                        <!-- ACTION -->

                        <td>

                            <?php if($r['status'] == 'pending'): ?>

                            <!-- APPROVE FORM -->

                            <button class="btn btn-sm btn-success"

                            data-bs-toggle="modal"

                            data-bs-target="#approveModal<?= $r['id'] ?>">

                                <i class="fas fa-check"></i>

                            </button>

                            <a href="?reject=<?= $r['id'] ?>"

                            class="btn btn-sm btn-danger"

                            onclick="return confirm('Reject this request?')">

                                <i class="fas fa-times"></i>

                            </a>

                            <?php else: ?>

                                <span class="text-muted">

                                    Processed

                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <!-- APPROVE MODAL -->

                    <div class="modal fade"
                    id="approveModal<?= $r['id'] ?>"
                    tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Approve & Assign Delivery

                                    </h5>

                                    <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>

                                </div>

                                <form method="POST">

                                    <div class="modal-body">

                                        <input type="hidden"
                                        name="request_id"
                                        value="<?= $r['id'] ?>">

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

                                        <!-- CONTACT -->

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
```
