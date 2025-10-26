<?php
// =====================================================
// ✅ ADMIN SIGN-UP PAGE  —  FINAL STABLE VERSION
// Compatible with table: roles(id, role_name, username, password, description)
// =====================================================

// --- Database Connection ---
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("<h3 style='color:red;'>❌ Database Connection Failed:</h3> " . $conn->connect_error);
}

// --- Sign-up handler ---
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username    = trim($_POST["username"]);
    $password    = trim($_POST["password"]);
    $description = trim($_POST["description"]);

    if (empty($username) || empty($password)) {
        $message = "⚠️ All fields are required!";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM roles WHERE username = ?");
        if (!$stmt) {
            die("<pre>SQL Prepare Failed (SELECT): " . $conn->error . "</pre>");
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ Username already exists!";
        } else {
            $stmt->close();

            // Hash password for security
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insert new admin record
            $stmt = $conn->prepare(
                "INSERT INTO roles (role_name, username, password, description)
                 VALUES ('admin', ?, ?, ?)"
            );
            if (!$stmt) {
                die("<pre>SQL Prepare Failed (INSERT): " . $conn->error . "</pre>");
            }

            $stmt->bind_param("sss", $username, $hashed, $description);
            if ($stmt->execute()) {
                $message = "✅ Admin account created successfully! <a href='index.php'>Go to Login</a>";
            } else {
                $message = "❌ Database error: " . htmlspecialchars($stmt->error);
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Sign Up</title>

<!-- ✅ use same CSS as index page -->
<link rel="stylesheet" href="../assets/css/index.css" />
<style>
/* fallback styling if index.css missing */
body {
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #1d1f21, #2a2d31);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
    color: #fff;
}
.signup-container {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    padding: 40px;
    width: 380px;
    text-align: center;
}
h2 { margin-bottom: 20px; color: #00bcd4; }
.form-group { margin-bottom: 20px; text-align: left; }
label { display: block; font-weight: 500; margin-bottom: 5px; }
input, textarea {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background-color: rgba(255,255,255,0.1);
    color: #fff;
    resize: none;
}
input:focus, textarea:focus { outline: 2px solid #00bcd4; }
button {
    background: #00bcd4;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: 0.3s;
}
button:hover { background: #0097a7; }
.message { margin-top: 20px; font-weight: 500; }
a { color: #00bcd4; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>

<body>
<div class="signup-container">
    <h2>Admin Sign-Up</h2>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username"
                   required placeholder="Enter username" />
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password"
                   required placeholder="Enter password" />
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea name="description" id="description" rows="3"
                      placeholder="Short description"></textarea>
        </div>

        <button type="submit">Register Admin</button>
    </form>

    <?php if (!empty($message)) : ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <p style="margin-top:15px;">Already have an account? 
       <a href="index.php">Login here</a></p>
</div>
</body>
</html>
