```php id="v8rx1o"
<?php
// admin/users.php — View and manage all users

require_once '../includes/config.php';

requireRole('admin');

// Delete user

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    if ($id !== $_SESSION['user_id']) {

        mysqli_query($conn,
        "DELETE FROM users WHERE id=$id");

        $msg = 'User deleted successfully.';
    }
}

// Role filter

$filter = isset($_GET['role'])
? clean($conn, $_GET['role'])
: '';

$where  = $filter
? "WHERE role='$filter'"
: "WHERE role != 'admin'";

// Fetch users

$users = mysqli_query($conn, "

    SELECT u.*,

      (SELECT COUNT(*)
       FROM food
       WHERE donor_id=u.id) AS food_count,

      (SELECT COUNT(*)
       FROM requests
       WHERE receiver_id=u.id) AS req_count

    FROM users u

    $where

    ORDER BY u.created_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>All Users — Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
rel="stylesheet">

<link href="../css/style.css"
rel="stylesheet">

</head>

<body>

<?php include '../includes/navbar.php'; ?>

<div class="sidebar">

    <a href="dashboard.php"
    class="nav-link">

        <i class="fas fa-tachometer-alt"></i>
        Dashboard

    </a>

    <a href="users.php"
    class="nav-link active">

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
    class="nav-link">

        <i class="fas fa-truck"></i>
        Deliveries

    </a>

</div>

<div class="main-content">

    <div class="page-header">

        <h2>

            <i class="fas fa-users me-2"
            style="color:var(--primary)"></i>

            All Users

        </h2>

    </div>

    <?php if (isset($msg)): ?>

        <div class="alert alert-success">

            <?= $msg ?>

        </div>

    <?php endif; ?>

    <!-- Filter Buttons -->

    <div class="mb-3 d-flex gap-2">

        <a href="users.php"
        class="btn btn-sm <?= !$filter ? 'btn-primary' : 'btn-outline-secondary' ?>">

            All

        </a>

        <a href="?role=donor"
        class="btn btn-sm <?= $filter==='donor' ? 'btn-success' : 'btn-outline-success' ?>">

            Donors

        </a>

        <a href="?role=receiver"
        class="btn btn-sm <?= $filter==='receiver' ? 'btn-primary' : 'btn-outline-primary' ?>">

            Receivers

        </a>

        <a href="?role=delivery"
        class="btn btn-sm <?= $filter==='delivery' ? 'btn-warning' : 'btn-outline-warning' ?>">

            Delivery

        </a>

    </div>

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

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

                    <?php $serial = 1; ?>

                    <?php while ($u = mysqli_fetch_assoc($users)): ?>

                    <tr>

                        <!-- SERIAL NUMBER -->

                        <td>

                            <?= $serial++ ?>

                        </td>

                        <!-- NAME -->

                        <td>

                            <div class="d-flex align-items-center gap-2">

                                <div class="avatar"
                                style="width:28px;height:28px;font-size:0.75rem;">

                                    <?= strtoupper(substr($u['name'],0,1)) ?>

                                </div>

                                <?= htmlspecialchars($u['name']) ?>

                            </div>

                        </td>

                        <!-- EMAIL -->

                        <td>

                            <?= htmlspecialchars($u['email']) ?>

                        </td>

                        <!-- PHONE -->

                        <td>

                            <?= $u['phone'] ?: '—' ?>

                        </td>

                        <!-- ADDRESS -->

                        <td>

                            <?= htmlspecialchars($u['address'] ?: '—') ?>

                        </td>

                        <!-- ROLE -->

                        <td>

                            <?php

                            $rc = [

                                'donor'   => 'success',
                                'receiver'=> 'primary',
                                'delivery'=> 'warning',
                                'admin'   => 'danger'

                            ];

                            ?>

                            <span class="badge bg-<?= $rc[$u['role']] ?? 'secondary' ?>">

                                <?= ucfirst($u['role']) ?>

                            </span>

                        </td>

                        <!-- ACTIVITY -->

                        <td>

                            <?php if ($u['role'] === 'donor'): ?>

                                <span class="badge bg-light text-dark">

                                    <?= $u['food_count'] ?> foods

                                </span>

                            <?php elseif ($u['role'] === 'receiver'): ?>

                                <span class="badge bg-light text-dark">

                                    <?= $u['req_count'] ?> requests

                                </span>

                            <?php else: ?>

                                <span class="badge bg-light text-dark">

                                    Delivery Staff

                                </span>

                            <?php endif; ?>

                        </td>

                        <!-- JOIN DATE -->

                        <td style="font-size:0.8rem;">

                            <?= date('d M Y', strtotime($u['created_at'])) ?>

                        </td>

                        <!-- ACTION -->

                        <td>

                            <a href="?delete=<?= $u['id'] ?>"
                            class="btn btn-sm btn-outline-danger"

                            onclick="return confirm('Delete this user?')">

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
```
