<?php
// login.php — User Login
require_once 'includes/config.php';

// Already logged in? redirect to dashboard
if (isset($_SESSION['user_id'])) {
    redirect('/food_system/' . $_SESSION['role'] . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $sql  = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $res  = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($res);

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['email']   = $user['email'];

            // Role-based redirect
            redirect('/food_system/' . $user['role'] . '/dashboard.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — FoodShare BD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="auth-logo"><i class="fas fa-seedling me-2"></i>Food<span>Share</span></div>
      <p class="text-muted mt-1" style="font-size:0.9rem;">Reducing waste, feeding hope</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
          <input type="email" name="email" class="form-control" placeholder="you@email.com" required
                 value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 py-2" style="font-size:1rem;">
        <i class="fas fa-sign-in-alt me-2"></i>Login
      </button>
    </form>

    <hr class="my-4">
    <p class="text-center text-muted" style="font-size:0.88rem;">
      Don't have an account? <a href="register.php" class="text-decoration-none fw-bold" style="color:var(--primary);">Register here</a>
    </p>

    <!-- Demo credentials box -->
    <div class="mt-3 p-3 rounded" style="background:#f8f9fa;font-size:0.78rem;">
      <strong>Demo Accounts (password: <code>password</code>)</strong><br>
      <span class="badge bg-danger me-1">Admin</span> admin@food.com<br>
      <span class="badge bg-success me-1">Donor</span> rahim@donor.com<br>
      <span class="badge bg-primary me-1">Receiver</span> ngo@receiver.com
    </div>
  </div>
</div>
</body>
</html>
