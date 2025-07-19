<?php
session_start();

// DB Connection
$pdo = new PDO("mysql:host=localhost;dbname=firstone", "root", "");


// Registration Logic
$reg_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $reg_msg = "❌ Passwords do not match!";
    } else {
        $check = $pdo->prepare("SELECT * FROM users WHERE mobile = ?");
        $check->execute([$mobile]);

        if ($check->rowCount() > 0) {
            $reg_msg = "❌ Mobile already registered!";
        } else {
            $user_code = strtoupper(substr(md5(uniqid()), 0, 4));
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, mobile, password, user_code) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $mobile, $hashed_pass, $user_code]);
            $reg_msg = "✅ Registration successful!";
        }
    }
}

// User Login Logic
$login_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_login"])) {
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile = ?");
    $stmt->execute([$mobile]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["username"] = $user["name"];
        $_SESSION["user_code"] = $user["user_code"];
        header("Location: dashboard.php");
        exit();
    } else {
        $login_msg = "❌ Invalid credentials!";
    }
}

// Dummy Customer Login Logic (You can expand this as needed)
$customer_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["customer_login"])) {
    $mobile = $_POST["mobile"];
    $code = $_POST["code"];

    // Add your customer table check here
    if ($code === "12345") { // Example only
        $_SESSION["customer"] = $mobile;
        header("Location: customer_dashboard.php");
        exit();
    } else {
        $customer_msg = "❌ Invalid customer code!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Loan System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet" />

</head>
<body>
<div class="container-fluid d-flex flex-column align-items-center justify-content-center min-vh-100">
  <ul class="nav nav-tabs mb-4" id="tabMenu" role="tablist">
    <li class="nav-item"><button class="nav-link active" id="welcome-tab" data-bs-toggle="tab" data-bs-target="#welcome" type="button">🏠 Welcome</button></li>
    <li class="nav-item"><button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">📝 Register</button></li>
    <li class="nav-item"><button class="nav-link" id="userlogin-tab" data-bs-toggle="tab" data-bs-target="#userlogin" type="button">🔐 User Login</button></li>
    <li class="nav-item"><button class="nav-link" id="customerlogin-tab" data-bs-toggle="tab" data-bs-target="#customerlogin" type="button">📱 Customer Login</button></li>
  </ul>

  <div class="tab-content w-100" id="tabContent">

    <!-- Welcome -->
    <div class="tab-pane fade show active" id="welcome">
      <div class="text-center">
        <h2 class="mb-5">Welcome to Smart Loan System</h2>
      </div>
    </div>

    <!-- Register -->
    <div class="tab-pane fade" id="register">
      <div class="form-box">
        <h3 class="text-center mb-4">👤 Create an Account</h3>
        <?php if ($reg_msg) echo "<div class='alert alert-info'>$reg_msg</div>"; ?>
        <form method="POST">
          <input type="hidden" name="register" />
          <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required />
          <input type="email" name="email" class="form-control mb-3" placeholder="Email ID" required />
          <input type="tel" name="mobile" class="form-control mb-3" placeholder="Mobile Number" required pattern="[0-9]{10}" />
          <input type="password" id="reg_pass" name="password" class="form-control mb-3" placeholder="Password" required />
          <input type="password" id="reg_cpass" name="confirm_password" class="form-control mb-3" placeholder="Confirm Password" required />
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" onclick="togglePasswords('reg_pass','reg_cpass')" />
            <label class="form-check-label">Show Password</label>
          </div>
          <button type="submit" class="btn btn-success w-100">Register</button>
        </form>
      </div>
    </div>

    <!-- User Login -->
    <div class="tab-pane fade" id="userlogin">
      <div class="form-box">
        <h3 class="text-center mb-4">🔐 User Login</h3>
        <?php if ($login_msg) echo "<div class='alert alert-danger'>$login_msg</div>"; ?>
        <form method="POST">
          <input type="hidden" name="user_login" />
          <input type="tel" name="mobile" class="form-control mb-3" placeholder="Mobile Number" required pattern="[0-9]{10}" />
          <input type="password" id="user_pass" name="password" class="form-control mb-3" placeholder="Password" required />
          <div class="form-check mb-1">
            <input class="form-check-input" type="checkbox" onclick="togglePasswords('user_pass')" />
            <label class="form-check-label">Show Password</label>
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>

    <!-- Customer Login -->
    <div class="tab-pane fade" id="customerlogin">
      <div class="form-box">
        <h3 class="text-center mb-4">📱 Customer Login</h3>
        <?php if ($customer_msg) echo "<div class='alert alert-warning'>$customer_msg</div>"; ?>
        <form method="POST">
          <input type="hidden" name="customer_login" />
          <input type="tel" name="mobile" class="form-control mb-3" placeholder="Mobile Number" required pattern="[0-9]{10}" />
          <input type="password" id="cust_code" name="code" class="form-control mb-3" placeholder="5-digit Unique Code" required pattern="[0-9]{5}" />
          <div class="form-check mb-1">
            <input class="form-check-input" type="checkbox" onclick="togglePasswords('cust_code')" />
            <label class="form-check-label">Show Code</label>
          </div>
          <button type="submit" class="btn btn-dark w-100">Login</button>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePasswords(...ids) {
    ids.forEach(id => {
      const field = document.getElementById(id);
      if (field) field.type = field.type === 'password' ? 'text' : 'password';
    });
  }
</script>
</body>
</html>
