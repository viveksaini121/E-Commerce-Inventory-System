<?php
session_start();
// Require canonical session keys
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ./auth/login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">Admin Dashboard</a>
      <div class="d-flex">
        <span class="text-white me-3">Welcome, <?= htmlspecialchars(
          $_SESSION['username'] ?? ''
        ) ?></span>
        <a href="auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container text-center my-5">
    <h2>Admin Panel</h2>
    <a href="products/add_product.php" class="btn btn-success w-50 my-2">➕ Add Product</a><br>
    <a href="products/view_products.php" class="btn btn-secondary w-50 my-2">🧾 View Products</a><br>
    <a href="orders/view_orders.php" class="btn btn-primary w-50 my-2">📦 View Orders</a>
  </div>
</body>
</html>
