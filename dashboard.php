<?php
session_start();

// --- Session check ---
if (!isset($_SESSION['username']) || !isset($_SESSION['user_code'])) {
    header("Location: login.php");
    exit();
}

// --- Database Connection ---
$host = "localhost";
$user = "root";
$password = "";
$dbname = "project2";

$conn = mysqli_connect($host, $user, $password, $dbname);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_code = $_SESSION['user_code'];

// --- Fetch Total Customers ---
$sql1 = "SELECT COUNT(*) FROM customer_records WHERE created_by = '$user_code'";
$result1 = mysqli_query($conn, $sql1);
$total_customers = mysqli_fetch_row($result1)[0] ?? 0;

// --- Fetch Total Payments ---
$sql2 = "SELECT SUM(amount) FROM payments WHERE created_by = '$user_code'";
$result2 = mysqli_query($conn, $sql2);
$total_payments = mysqli_fetch_row($result2)[0] ?? 0;

// --- Fetch Pending Dues ---
$sql3 = "SELECT SUM(due_amount) FROM customer_records WHERE created_by = '$user_code'";
$result3 = mysqli_query($conn, $sql3);
$pending_dues = mysqli_fetch_row($result3)[0] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Loan Dashboard</a>
        <div class="d-flex">
            <span class="navbar-text text-white me-3">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a class="btn btn-outline-light" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<main class="container mt-5">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow border-0 text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Customers</h5>
                    <h3 class="card-text"><?= $total_customers ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-0 text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Payments</h5>
                    <h3 class="card-text">₹<?= number_format($total_payments) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-0 text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Pending Dues</h5>
                    <h3 class="card-text">₹<?= number_format($pending_dues) ?></h3>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="text-center mt-5 mb-3 text-muted">
    &copy; <?= date('Y') ?> Loan Manager System
</footer>

</body>
</html>
