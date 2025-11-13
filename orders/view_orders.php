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

// Determine which foreign key column exists on `orders` (user_id vs customer_id)
$fk = null;
$check = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'user_id'");
if ($check && $check->num_rows > 0) {
  $fk = 'user_id';
} else {
  $check2 = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'customer_id'");
  if ($check2 && $check2->num_rows > 0) {
    $fk = 'customer_id';
  }
}

if (!$fk) {
  // If neither column exists, show helpful error (schema mismatch)
  die("<p style='color:red'>Database schema error: 'orders' table has neither 'user_id' nor 'customer_id'.</p>");
}

// Determine which date/timestamp column exists on `orders` (prefer created_at, fall back to order_date)
$dateCol = null;
$c1 = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'created_at'");
if ($c1 && $c1->num_rows > 0) {
  $dateCol = 'created_at';
} else {
  $c2 = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'order_date'");
  if ($c2 && $c2->num_rows > 0) {
    $dateCol = 'order_date';
  }
}

if (!$dateCol) {
  die("<p style='color:red'>Database schema error: 'orders' table has no timestamp column (expected 'created_at' or 'order_date').</p>");
}

// Build query using the detected FK column and date column
$query = "SELECT o.id AS order_id, u.username AS username, u.email AS email, "
       . "o." . $dateCol . " AS order_date, SUM(oi.quantity * p.price) AS total_amount "
       . "FROM orders o "
       . "JOIN users u ON o.`" . $fk . "` = u.id "
       . "JOIN order_items oi ON o.id = oi.order_id "
       . "JOIN products p ON oi.product_id = p.id "
       . "GROUP BY o.id, u.username, u.email, o." . $dateCol . " "
       . "ORDER BY o." . $dateCol . " DESC";

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
<thead><tr><th>Order ID</th><th>Username</th><th>Email</th><th>Date</th><th>Total (₹)</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
  <td>#<?= $row['order_id'] ?></td>
  <td><?= htmlspecialchars($row['username']) ?></td>
  <td><?= htmlspecialchars($row['email']) ?></td>
  <td><?= $row['order_date'] ?></td>
  <td>₹<?= number_format($row['total_amount'], 2) ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
