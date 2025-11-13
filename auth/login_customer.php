<?php
session_start();
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND role='customer'");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user["password"])) {
      // Set canonical session keys used across the app
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      header("Location: ../customers/customer_dashboard.php");
      exit();
    } else {
      echo "<div class='text-danger text-center mt-3'>Invalid password!</div>";
    }
  } else {
    echo "<div class='text-danger text-center mt-3'>Customer not found!</div>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
  <div class="container w-50">
    <h3>Customer Login</h3>
    <form method="POST">
      <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
      <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
      <button type="submit" class="btn btn-success w-100">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="register_customer.php">Register here</a></p>
  </div>
</body>
</html>
