<?php
$conn = new mysqli("localhost", "root", "", "car_dealership");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$notification = "";  // To store notification messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    // Check if username or email exists
    $checkUser = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkUser);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $notification = "Username or email already taken. <a href='login.php'>Login here</a>";
    } else {
        // Insert new user with email
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $notification = "Registration successful. <a href='login.php'>Login here</a>";
        } else {
            $notification = "Error: " . $conn->error;
        }
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
    <title>User Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <!-- Notification -->
        <?php if (!empty($notification)): ?>
            <div class="notification">
                <?php echo $notification; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="registerForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="submit" value="Register" class="submit-btn">

            <!-- Already registered? -->
            <div class="already-registered">
                <p>Already registered? <a href="login.php">Login here</a></p>
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
