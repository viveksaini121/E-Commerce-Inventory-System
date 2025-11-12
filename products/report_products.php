<?php
include("../config/db.php");
$result = $conn->query("SELECT name, stock, price, (stock * price) AS total_value FROM products");
$total_stock = 0; $total_value = 0;
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Product Stock Report</h2>
<table class="table table-hover">
<thead><tr><th>Name</th><th>Stock</th><th>Price</th><th>Total Value</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()) {
  $total_stock += $row['stock'];
  $total_value += $row['total_value'];
?>
<tr>
  <td><?= $row['name'] ?></td>
  <td><?= $row['stock'] ?></td>
  <td>$<?= $row['price'] ?></td>
  <td>$<?= $row['total_value'] ?></td>
</tr>
<?php } ?>
<tr class="fw-bold">
  <td>Total</td><td><?= $total_stock ?></td><td>-</td><td>$<?= $total_value ?></td>
</tr>
</tbody>
</table>
</body>
</html>
