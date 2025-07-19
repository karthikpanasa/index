<?php
// Start session and check login
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_code'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
    <div>
        Welcome, <strong><?= $_SESSION['username'] ?></strong> (Code: <strong><?= $_SESSION['user_code'] ?></strong>)
    </div>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
</header>
