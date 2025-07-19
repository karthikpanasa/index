<?php
session_start();
$conn = new mysqli("localhost", "root", "", "firstone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mobile = trim($_POST['mobile']);
$code   = trim($_POST['code']);

$sql = "SELECT * FROM customers WHERE mobile = '$mobile' AND code = '$code'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $_SESSION['customer_id'] = $row['id'];
    $_SESSION['customer_mobile'] = $row['mobile'];
    header("Location: customer_dashboard.php");
    exit;
} else {
    echo "<script>alert('Invalid mobile number or code'); window.history.back();</script>";
}

$conn->close();
?>
