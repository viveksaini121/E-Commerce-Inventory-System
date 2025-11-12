<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $phone = $_POST["phone"];
  $address = $_POST["address"];
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

  $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
  $check->bind_param("ss", $username, $email);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Username or Email already exists!');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, 'customer')");
    $stmt->bind_param("sssss", $username, $email, $phone, $address, $password);
    $stmt->execute();
    echo "<script>alert('Registration successful! You can now log in.'); window.location='login_customer.php';</script>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
  <div class="container w-50 bg-white p-4 rounded shadow-sm">
    <h3 class="mb-3 text-center">Customer Registration</h3>
    <form method="POST">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <p class="text-center mt-3">
      Already have an account? <a href="login_customer.php">Login here</a>
    </p>
  </div>
</body>
</html>
