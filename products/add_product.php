<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssdi", $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock']);
  $stmt->execute();
  header("Location: view_products.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Add Product</h2>
<form method="POST" class="w-50">
  <input class="form-control mb-2" name="name" placeholder="Product Name" required>
  <textarea class="form-control mb-2" name="description" placeholder="Description"></textarea>
  <input class="form-control mb-2" type="number" step="0.01" name="price" placeholder="Price" required>
  <input class="form-control mb-2" type="number" name="stock" placeholder="Stock" required>
  <button class="btn btn-primary">Add Product</button>
</form>
</body>
</html>
