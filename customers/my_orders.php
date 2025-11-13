<?php
session_start();
include("../config/db.php");

// Require authenticated user
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

$id = (int) $_SESSION['user_id'];

// Show orders for the logged-in user with totals per order
$stmt = $conn->prepare(
  "SELECT o.id AS order_id, o.order_date, SUM(oi.quantity * p.price) AS total_amount\n"
  . "FROM orders o\n"
  . "JOIN order_items oi ON o.id = oi.order_id\n"
  . "JOIN products p ON oi.product_id = p.id\n"
  . "WHERE o.user_id = ?\n"
  . "GROUP BY o.id, o.order_date\n"
  . "ORDER BY o.order_date DESC"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
  <div class="container">
    <h3 class="mb-4">My Orders</h3>
    <table class="table table-bordered">
      <thead class="table-primary">
        <tr><th>Order ID</th><th>Date</th><th>Total (₹)</th></tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
          <td>#<?= $row['order_id'] ?></td>
          <td><?= $row['order_date'] ?></td>
          <td>₹<?= number_format($row['total_amount'], 2) ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
