<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION["customer"])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

$username = $_SESSION["customer"];
$q = $conn->prepare("SELECT id FROM users WHERE username=?");
$q->bind_param("s", $username);
$q->execute();
$cust = $q->get_result()->fetch_assoc();
$id = $cust['id'];

$result = $conn->query("
SELECT o.id AS order_id, o.order_date, p.name, i.quantity
FROM orders o
JOIN order_items i ON o.id = i.order_id
JOIN products p ON i.product_id = p.id
WHERE o.customer_id = $id
ORDER BY o.order_date DESC;
");
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
        <tr><th>Order ID</th><th>Product</th><th>Quantity</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
          <td>#<?= $row['order_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= $row['order_date'] ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
