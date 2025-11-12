<?php
include("../config/db.php");

/*
  Notes on visibility and transaction isolation (ACID demonstration):

  - After placing orders using the transaction in `customers/checkout.php`, a committed order
    will be visible to other sessions.
  - If a transaction is not yet committed, other sessions will NOT see those changes when using
    REPEATABLE READ / READ COMMITTED with proper locking (depending on isolation level).
  - To prevent race conditions when updating stock we use `SELECT ... FOR UPDATE` in checkout.php
    which acquires row locks until commit/rollback (demonstrates isolation and prevents lost updates).

  Example phpMyAdmin / SQL snippets to simulate concurrency (open two sessions):

  -- Session A (start a transaction and lock product row):
  SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
  START TRANSACTION;
  SELECT stock FROM products WHERE id = 1 FOR UPDATE;
  -- keep this transaction open (do not commit yet)

  -- Session B (tries to update same product):
  SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
  START TRANSACTION;
  UPDATE products SET stock = stock - 1 WHERE id = 1; -- will block until Session A commits
  COMMIT;

  -- Now commit Session A and observe the ordering and consistency

  For SERIALIZABLE isolation you can set: SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;
  and repeat the experiment to see stricter guarantees.

*/

$query = "
SELECT orders.id, customers.name AS customer, products.name AS product, order_items.quantity, orders.order_date
FROM orders
JOIN customers ON orders.customer_id = customers.id
JOIN order_items ON orders.id = order_items.order_id
JOIN products ON order_items.product_id = products.id
ORDER BY orders.order_date DESC;
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Order Details</h2>
<table class="table table-bordered table-striped">
<thead><tr><th>Order ID</th><th>Customer</th><th>Product</th><th>Quantity</th><th>Date</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['customer'] ?></td>
  <td><?= $row['product'] ?></td>
  <td><?= $row['quantity'] ?></td>
  <td><?= $row['order_date'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
