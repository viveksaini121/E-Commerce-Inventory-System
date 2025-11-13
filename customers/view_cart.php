<?php
session_start();
// Use unified session key
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

include("../config/db.php");
$user_id = (int) $_SESSION['user_id'];

// Handle removal of cart item
if (isset($_POST['remove_item'])) {
  $remove_id = (int) $_POST['remove_id'];
  $d = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
  $d->bind_param("ii", $remove_id, $user_id);
  $d->execute();
}

// Fetch cart from DB
$stmt = $conn->prepare("SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
  $row['subtotal'] = $row['price'] * $row['quantity'];
  $total += $row['subtotal'];
  $cart[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container my-5">
    <h2 class="mb-4 text-center">🛒 Your Shopping Cart</h2>

    <?php if (empty($cart)) { ?>
      <div class="alert alert-warning text-center">Your cart is empty.</div>
    <?php } else { ?>
      <table class="table table-bordered">
        <thead class="table-primary">
          <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart as $item) { ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>₹<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₹<?= number_format($item['subtotal'], 2) ?></td>
            <td>
              <form method="POST">
                <input type="hidden" name="remove_id" value="<?= $item['cart_id'] ?>">
                <button name="remove_item" class="btn btn-danger btn-sm">Remove</button>
              </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>

      <h4 class="text-end">Total: ₹<?= number_format($total, 2) ?></h4>

      <div class="d-flex justify-content-between mt-4">
        <a href="customer_dashboard.php" class="btn btn-secondary">← Continue Shopping</a>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
      </div>
    <?php } ?>
  </div>
</body>
</html>
