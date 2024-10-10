<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "car_dealership");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

$username = $_SESSION['username']; // Retrieve username from session

// Handle removing item from the cart
if (isset($_POST['remove_item'])) {
    $itemIdToRemove = $_POST['remove_item'];

    // Prepare a statement to delete the item from the cart
    $deleteQuery = "DELETE FROM cart WHERE id = ? AND username = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("is", $itemIdToRemove, $username);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to the same page to update the cart after removal
    header('Location: cart.php');
    exit;
}

// Fetch user's cart items from the database
$userCartQuery = "SELECT * FROM cart WHERE username = ?";
$stmt = $conn->prepare($userCartQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Initialize cart array
$cartItems = [];
$totalPrice = 0;

while ($item = $result->fetch_assoc()) {
    $cartItems[] = $item;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <div class="container">
        <h1>Your Cart</h1>

        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
        <?php else: ?>
            <form method="POST" action="checkout.php">
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                            <p>Price: KES <?php echo number_format($item['price'], 2); ?></p>
                            <label for="quantity_<?php echo $item['id']; ?>">Quantity:</label>
                            <input type="number" id="quantity_<?php echo $item['id']; ?>" name="quantity[<?php echo $item['id']; ?>]" min="1" value="1">
                            <button type="submit" name="buy_item" value="<?php echo $item['id']; ?>" class="buy-btn">Buy</button>

                            <form method="POST" action="">
                                <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" class="remove-btn">Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
