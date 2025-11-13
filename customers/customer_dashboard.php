<?php
session_start();
// Require canonical session key set at login
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

include("../config/db.php");

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Add to cart
if (isset($_POST['add_to_cart'])) {
  $product_id = $_POST['product_id'];
  $product_name = $_POST['product_name'];
  $product_price = $_POST['product_price'];
  $product_quantity = 1;

  // If already in cart, increase quantity
  if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
  } else {
    $_SESSION['cart'][$product_id] = [
      'name' => $product_name,
      'price' => $product_price,
      'quantity' => $product_quantity
    ];
  }

  echo "<script>alert('Product added to cart!');</script>";
}

$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">E-Commerce</a>
      <div class="d-flex">
        <a href="view_cart.php" class="btn btn-light btn-sm me-2">
          🛒 Cart (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)
        </a>
        <span class="text-white me-3">Welcome, <?= htmlspecialchars(
          
          // prefer username session key
          $_SESSION['username'] ?? ''
        ) ?></span>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container my-5">
    <h2 class="text-center mb-4">Available Products</h2>
    <div class="row">
      <?php while ($row = $result->fetch_assoc()) { ?>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text text-muted small"><?= htmlspecialchars($row['description']) ?></p>
            <p class="fw-bold">₹<?= number_format($row['price'], 2) ?></p>
            <form method="POST">
              <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
              <input type="hidden" name="product_price" value="<?= $row['price'] ?>">
              <button type="submit" name="add_to_cart" class="btn btn-success w-100">
                Add to Cart
              </button>
            </form>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</body>
</html>
