<?php
session_start();
$conn = new mysqli("localhost", "root", "", "firstone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name     = trim($_POST['name']);
$email    = trim($_POST['email']);
$mobile   = trim($_POST['mobile']);
$password = trim($_POST['password']);
$cpass    = trim($_POST['confirm_password']);

// Check if mobile already exists
$check = $conn->query("SELECT * FROM users WHERE mobile='$mobile'");
if ($check->num_rows > 0) {
    echo "<script>alert('Mobile number already registered!'); window.history.back();</script>";
    exit;
}

// Check password match
if ($password !== $cpass) {
    echo "<script>alert('Passwords do not match'); window.history.back();</script>";
    exit;
}

// Generate new s_no
$sno_result = $conn->query("SELECT MAX(s_no) AS max_sno FROM users");
$row = $sno_result->fetch_assoc();
$new_sno = $row['max_sno'] ? $row['max_sno'] + 1 : 1;

// Generate random alphanumeric user_code (6 characters)
function generateUserCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

$user_code = generateUserCode();

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$sql = "INSERT INTO users (s_no, user_code, name, email, mobile, password)
        VALUES ($new_sno, '$user_code', '$name', '$email', '$mobile', '$hashed')";

if ($conn->query($sql)) {
    echo "<script>alert('Registration successful! Your code is $user_code'); window.location.href='index.php';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
