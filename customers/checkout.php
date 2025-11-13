<?php
session_start();
include("../config/db.php");

// Require canonical session key
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
  echo "<script>alert('Your cart is empty!'); window.location='view_cart.php';</script>";
  exit();
}

// Use logged-in user's id from session
$customer_id = (int) $_SESSION['user_id'];
// --- Transactional order placement ---
// We wrap the following operations in a single DB transaction to ensure ACID:
// 1) Insert into `orders`
// 2) For each cart item: lock product row (SELECT ... FOR UPDATE), verify stock, insert into `order_items`, update `products.stock`
// If any step fails we rollback so the DB remains consistent (atomicity).

try {
  // Start transaction (autocommit=false)
  $conn->begin_transaction();

  // Insert order (records order header) - orders now references users via user_id
  $stmt = $conn->prepare("INSERT INTO orders (user_id, order_date) VALUES (?, NOW())");
  $stmt->bind_param("i", $customer_id);
  $stmt->execute();
  $order_id = $conn->insert_id;

  // Prepare statements that will be reused inside loop for efficiency
  $selectStock = $conn->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
  $insertItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
  $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

  foreach ($cart as $product_id => $item) {
    $quantity = (int)$item['quantity'];

    // Lock the product row to prevent concurrent modifications (this demonstrates isolation)
    $selectStock->bind_param("i", $product_id);
    $selectStock->execute();
    $stockRes = $selectStock->get_result();
    if ($stockRes->num_rows === 0) {
      throw new Exception("Product not found: " . $product_id);
    }
    $row = $stockRes->fetch_assoc();
    $available = (int)$row['stock'];

    // Check consistency: ensure enough stock
    if ($available < $quantity) {
      throw new Exception("Insufficient stock for product ID {$product_id}. Available: {$available}, requested: {$quantity}");
    }

    // Insert item
    $insertItem->bind_param("iii", $order_id, $product_id, $quantity);
    $insertItem->execute();

    // Update stock
    $updateStock->bind_param("ii", $quantity, $product_id);
    $updateStock->execute();
  }

  // All queries succeeded — commit transaction (durability)
  $conn->commit();

  // Clear cart only after a successful commit
  unset($_SESSION['cart']);

} catch (Exception $e) {
  // Any exception triggers a rollback ensuring atomicity. Use @ to suppress any warning if
  // rollback is called and there is no active transaction on older PHP versions.
  @ $conn->rollback();

  // Log or surface the error - for demo we show a message. In production log this instead.
  $errorMsg = "Order failed: " . $e->getMessage();
  // Show a simple error page and stop (do not commit partial changes)
  echo "<!DOCTYPE html><html><body><div style='margin:2rem'><h3>Order Failed</h3><p>" . htmlspecialchars($errorMsg) . "</p><a href='view_cart.php'>Back to cart</a></div></body></html>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Successful</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container my-5 text-center">
    <div class="alert alert-success shadow-sm">
      <h3>✅ Order Placed Successfully!</h3>
      <p>Your order ID is <strong>#<?= $order_id ?></strong></p>
      <p>Thank you for shopping with us!</p>
    </div>
    <a href="customer_dashboard.php" class="btn btn-primary">Continue Shopping</a>
  </div>
</body>
</html>
