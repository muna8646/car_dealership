<?php
session_start();
$conn = new mysqli("localhost", "root", "", "car_dealership");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$notification = "";  // To store notification messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;  // Set session
            header('Location: index.php');
            exit;
        } else {
            $notification = "Incorrect password.";
        }
    } else {
        $notification = "User not found.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <!-- Notification -->
        <?php if (!empty($notification)): ?>
            <div class="notification">
                <?php echo htmlspecialchars($notification); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="submit" value="Login" class="submit-btn">

            <!-- Already registered? -->
            <div class="already-registered">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </form>
    </div>

    <script>
        // Automatically hide notification after 5 seconds
        window.onload = function () {
            const notification = document.querySelector('.notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }
        };
    </script>
</body>
</html>
