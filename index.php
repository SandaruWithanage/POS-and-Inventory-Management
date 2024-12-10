<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login request via GET method (using URL parameters)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Get username and password from URL
    if (isset($_GET['username']) && isset($_GET['password'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];

        // Prepare and execute query
        $stmt = $conn->prepare("SELECT role_name, password FROM roles WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($role, $hashedPassword);
        $stmt->fetch();
        $stmt->close();

        // Validate credentials
        if ($role && $password == $hashedPassword) {
            // Store user information in the session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect based on role
            switch ($role) {
                case "SupplyManager":
                    header("Location: supply_manager_dashboard.php");
                    break;
                case "Admin":
                    header("Location: dashboard.html");
                    break;
                case "CustomerRelation":
                    header("Location: customer_manager_dashboard.php");
                    break;
                case "FinanceManager":
                    header("Location: finance_manager_dashboard.php");
                    break;
                case "ProcurementManager":
                    header("Location: procurement_manager_dashboard.php");
                    break;
                case "Cashier":
                    header("Location: CashierDashboard.php");
                    break;
                default:
                    echo "<p style='color:red;'>No dashboard available for this role</p>";
            }
        } else {
            echo "<p style='color:red;'>Invalid username or password</p>";
        }
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
                <img src="assets/logo.jpg" alt="Logo" />
            </div>
            <h2>Sign In</h2>
            <form id="loginForm" method="GET">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
                <div id="error-message" class="error-message"></div>
            </form>
        </div>
    </div>

</body>
</html>
