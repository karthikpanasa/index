<?php
session_start();
$conn = new mysqli("localhost", "root", "", "firstone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mobile   = trim($_POST['mobile']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE mobile = '$mobile'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid password'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('User not found'); window.history.back();</script>";
}

$conn->close();
?>
