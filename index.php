<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Commerce Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .hero {
      padding: 80px 20px;
      text-align: center;
      background: linear-gradient(120deg, #007bff, #6610f2);
      color: white;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .btn-lg { font-size: 1.1rem; font-weight: 500; }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="hero">
      <h1 class="display-5 fw-bold">Welcome to E-Commerce Inventory System</h1>
      <p class="lead mt-3">Manage products, customers, and orders efficiently.</p>
      <div class="mt-4">
        <a href="auth/login.php" class="btn btn-dark btn-lg me-2">
          <i class="bi bi-person-lock"></i> Admin Login
        </a>
        <a href="auth/login_customer.php" class="btn btn-success btn-lg me-2">
          <i class="bi bi-person-circle"></i> Customer Login
        </a>
        <a href="auth/register_customer.php" class="btn btn-primary btn-lg">
          <i class="bi bi-person-plus"></i> Customer Register
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
