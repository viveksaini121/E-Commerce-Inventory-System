<?php
include("../config/db.php");

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM products WHERE name LIKE ?";
$stmt = $conn->prepare($query);
$param = "%$search%";
$stmt->bind_param("s", $param);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Product List</h2>
<form method="GET" class="mb-3 d-flex" style="max-width:400px;">
  <input type="text" name="search" class="form-control me-2" placeholder="Search product" value="<?= htmlspecialchars($search) ?>">
  <button class="btn btn-outline-primary">Search</button>
</form>
<a href="add_product.php" class="btn btn-success mb-3">+ Add Product</a>
<table class="table table-striped">
<thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['name'] ?></td>
  <td>Rs.<?= $row['price'] ?></td>
  <td><?= $row['stock'] ?></td>
  <td>
    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
