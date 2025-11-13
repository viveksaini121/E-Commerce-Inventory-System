<?php
session_start();
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  // Use prepared statement to prevent SQL injection
  $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check for both hashed and plain-text passwords
    if (password_verify($password, $user["password"]) || $password === $user["password"]) {
      // Use canonical session keys used across the app
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];

      // Redirect based on user role
      if ($user['role'] === 'admin') {
        header("Location: ../admin_dashboard.php");
      } else {
        header("Location: ../customers/customer_dashboard.php");
      }
      exit(); // Important: Always exit after redirect

    } else {
      $error = "Invalid password!";
    }
  } else {
    $error = "User not found!";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
<div class="container text-center">
  <h3>Login</h3>
  <?php if (isset($error)): ?>
    <div class="alert alert-danger w-25 mx-auto mb-3"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="POST" class="w-25 mx-auto mt-4">
    <input class="form-control mb-2" type="text" name="username" placeholder="Username" required>
    <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>
</body>
</html>
