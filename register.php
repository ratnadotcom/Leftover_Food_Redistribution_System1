<?php
// register.php — User Registration
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    redirect('/food_system/' . $_SESSION['role'] . '/dashboard.php');
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($conn, $_POST['name']);
    $email    = clean($conn, $_POST['email']);
    $phone    = clean($conn, $_POST['phone']);
    $address  = clean($conn, $_POST['address']);
    $role     = in_array($_POST['role'], ['donor','receiver']) ? $_POST['role'] : 'donor';
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Name, email, and password are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check duplicate email
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Email already registered. Please login.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql    = "INSERT INTO users (name, email, password, phone, address, role)
                       VALUES ('$name','$email','$hashed','$phone','$address','$role')";
            if (mysqli_query($conn, $sql)) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — FoodShare BD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card" style="max-width:520px;">
    <div class="text-center mb-4">
      <div class="auth-logo"><i class="fas fa-seedling me-2"></i>Food<span>Share</span></div>
      <p class="text-muted mt-1" style="font-size:0.9rem;">Create your account</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success py-2"><?= $success ?> <a href="login.php">Login now →</a></div>
    <?php endif; ?>

    <form method="POST">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" class="form-control" placeholder="Your name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" placeholder="017XXXXXXXX">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address *</label>
        <input type="email" name="email" class="form-control" placeholder="you@email.com" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" placeholder="Your area, city">
      </div>
      <div class="mb-3">
        <label class="form-label">Register As *</label>
        <div class="d-flex gap-3">
          <div class="form-check flex-fill p-3 border rounded" style="cursor:pointer;">
            <input class="form-check-input" type="radio" name="role" value="donor" id="donor" checked>
            <label class="form-check-label fw-bold" for="donor">
              <i class="fas fa-hand-holding-heart me-2 text-success"></i>Donor
            </label>
            <div class="text-muted" style="font-size:0.78rem;">I want to donate food</div>
          </div>
          <div class="form-check flex-fill p-3 border rounded" style="cursor:pointer;">
            <input class="form-check-input" type="radio" name="role" value="receiver" id="receiver">
            <label class="form-check-label fw-bold" for="receiver">
              <i class="fas fa-hands-helping me-2 text-primary"></i>Receiver
            </label>
            <div class="text-muted" style="font-size:0.78rem;">I need food assistance</div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Password *</label>
          <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Confirm Password *</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="fas fa-user-plus me-2"></i>Create Account
      </button>
    </form>

    <p class="text-center mt-3 text-muted" style="font-size:0.88rem;">
      Already have an account? <a href="login.php" class="fw-bold text-decoration-none" style="color:var(--primary);">Login</a>
    </p>
  </div>
</div>
</body>
</html>
