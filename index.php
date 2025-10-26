<?php
session_start();

// =========================================================
//  DATABASE CONNECTION
// =========================================================
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("<h3 style='color:red;'>❌ Connection failed:</h3> " . $conn->connect_error);
}

// =========================================================
// ✅ HANDLE LOGIN (SECURE POST METHOD)
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Check user
        $stmt = $conn->prepare("SELECT role_name, password FROM roles WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($role, $hashedPassword);
                $stmt->fetch();

                // ✅ Verify hashed password
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = strtolower($role);

                    // ✅ Redirect based on role_name
                    switch (strtolower($role)) {
                        case "supplymanager":
                            header("Location: supply_manager_dashboard.php");
                            exit;
                        case "admin":
                            header("Location: dashboard.php");
                            exit;
                        case "customerrelation":
                            header("Location: customer_manager_dashboard.php");
                            exit;
                        case "financemanager":
                            header("Location: finance_manager_dashboard.php");
                            exit;
                        case "procurementmanager":
                            header("Location: procurement_manager_dashboard.php");
                            exit;
                        case "inventorymanager":
                            header("Location: inventory_manager_dashboard.php");
                            exit;
                        case "cashier":
                            header("Location: CashierDashboard.php");
                            exit;
                        default:
                            $error = "⚠️ No dashboard available for this role.";
                    }
                } else {
                    $error = "❌ Invalid password.";
                }
            } else {
                $error = "❌ Username not found.";
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "⚠️ Please fill in all fields.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="assets/logo.jpg" alt="Logo" style="width:80px;height:80px;border-radius:10px;">
            </div>
            <h2>Sign In</h2>
            <form id="loginForm" method="POST" action="">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?= $error ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
