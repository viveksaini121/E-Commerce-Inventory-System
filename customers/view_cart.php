<?php
session_start();
if (!isset($_SESSION["customer"])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

$cart = $_SESSION['cart'] ?? [];

if (isset($_POST['remove_item'])) {
  $id = $_POST['remove_id'];
  unset($_SESSION['cart'][$id]);
}

$total = 0;
foreach ($cart as $item) {
  $total += $item['price'] * $item['quantity'];
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
          <?php foreach ($cart as $id => $item) { ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>₹<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            <td>
              <form method="POST">
                <input type="hidden" name="remove_id" value="<?= $id ?>">
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
