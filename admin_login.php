<?php
session_start();

// Admin credentials
$adminUsername = "muna";
$adminPassword = "muna";

$notification = "";  // To store messages
$attempts = isset($_SESSION['attempts']) ? $_SESSION['attempts'] : 0;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verify credentials
    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['logged_in'] = true;  // Set session to indicate the admin is logged in
        $_SESSION['attempts'] = 0;  // Reset attempts on successful login
        header("Location: admin.php");  // Redirect to admin dashboard
        exit();
    } else {
        $attempts++;
        $_SESSION['attempts'] = $attempts;
        $notification = "Invalid username or password. Attempt $attempts of 3.";
    }

    // Block access after 3 failed attempts
    if ($attempts >= 3) {
        $notification = "Too many failed attempts. Please contact the administrator.";
        session_destroy(); // Destroy session on too many failed attempts
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Admin Login Form -->
<main>
    <section>
        <header>
            <h2>Admin Login</h2>
        </header>
        <article>
            <?php if (!empty($notification)): ?>
                <div class="notification">
                    <p><?php echo htmlspecialchars($notification); ?></p>
                </div>
            <?php endif; ?>
            <form action="admin_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <input type="submit" name="login" value="Login">
                </div>
            </form>
        </article>
    </section>
</main>

<!-- Footer Section -->
<footer>
    <p>&copy; 2024 Car Dealership Admin Panel</p>
</footer>

</body>
</html>
