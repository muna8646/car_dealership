<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: admin_login.php");  // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Site Header -->
<header>
    <nav>
        <h1>Admin Dashboard</h1>
    </nav>
    <nav>
    <a href="logout.php">Logout</a> <!-- Logout link -->
</nav>
</header>

<!-- Main Content -->
<main>
    <section>
        <header>
            <h2>Welcome, Admin!</h2>
        </header>
        
        <article>
            <h3>Add a New Car</h3>
            <form action="process_car.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Car Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="model">Car Model:</label>
                    <input type="text" id="model" name="model" required>
                </div>

                <div class="form-group">
                    <label for="price">Price (KES):</label>
                    <input type="number" step="0.01" id="price" name="price" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Car Image:</label>
                    <input type="file" id="image" name="image" required>
                </div>

                <div class="form-group">
                    <input type="submit" value="Add Car">
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
