<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search query
$search_query = "";
$sql = "SELECT * FROM cars"; // Default SQL query to fetch all cars

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];

    // Modify SQL query to search for cars by name, model, or description (Prepared statement to avoid SQL injection)
    $sql = "SELECT * FROM cars WHERE name LIKE ? OR model LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_query = '%' . $search_query . '%';
    $stmt->bind_param("sss", $like_query, $like_query, $like_query);
} else {
    $stmt = $conn->prepare($sql);
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Add to cart logic
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['username'])) {
        $car_id = $_POST['car_id'];
        
        // Fetch car details to add to session cart
        $car_sql = "SELECT * FROM cars WHERE id = ?";
        $car_stmt = $conn->prepare($car_sql);
        $car_stmt->bind_param("i", $car_id);
        $car_stmt->execute();
        $car_result = $car_stmt->get_result()->fetch_assoc();

        // Store car details in session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][$car_id] = $car_result;

        // Insert into the database cart table
        $insert_sql = "INSERT INTO cart (username, name, price, image) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssss", $_SESSION['username'], $car_result['name'], $car_result['price'], $car_result['image']);
        $insert_stmt->execute();

        echo "<p class='message'>Car added to cart successfully!</p>";
    } else {
        echo "<p class='message'>Please log in to add items to your cart.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Dealership</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <!-- Navigation Links -->
    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Create Account</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="cart.php">View Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>

    <h1>Available Cars for Sale</h1>

    <!-- Search Form -->
    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Search cars..." value="<?php echo htmlspecialchars($search_query); ?>">
        <input type="submit" value="Search">
    </form>

    <div class="car-listings">
        <?php while ($car = $result->fetch_assoc()) { ?>
            <div class="car-item">
                <img src="uploads/<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" />
                <h2><?php echo htmlspecialchars($car['name']); ?> - <?php echo htmlspecialchars($car['model']); ?></h2>
                <p>Price: KES <?php echo number_format($car['price'], 2); ?></p>
                <a href="car_details.php?id=<?php echo htmlspecialchars($car['id']); ?>">View Details</a>

                <!-- Add to Cart Form -->
                <?php if (isset($_SESSION['username'])): ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                        <input type="submit" name="add_to_cart" value="Add to Cart">
                    </form>
                <?php else: ?>
                    <p>Please <a href="login.php">log in</a> to add to cart.</p>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>

    <?php
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
