<?php
session_start();
include("../config/db.php");

// Require logged-in user
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login_customer.php");
  exit();
}

$user_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: customer_dashboard.php');
  exit();
}

$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
if ($product_id <= 0) {
  header('Location: customer_dashboard.php');
  exit();
}

// If the product already exists in cart_items for this user, increment quantity
$stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
  $row = $res->fetch_assoc();
  $newQty = (int)$row['quantity'] + 1;
  $u = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
  $u->bind_param("ii", $newQty, $row['id']);
  $u->execute();
} else {
  $i = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)");
  $i->bind_param("ii", $user_id, $product_id);
  $i->execute();
}

// Redirect back to dashboard or to cart with a success message
header('Location: customer_dashboard.php?added=1');
exit();

?>
