<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "car_dealership");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get car ID from URL
$car_id = $_GET['id'];

// Fetch car details
$sql = "SELECT * FROM cars WHERE id = $car_id";
$result = $conn->query($sql);
$car = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car['name']; ?> - Details</title>
    <link rel="stylesheet" href="st.css">
</head>
<body>
    <div class="container">
        <h1><?php echo $car['name']; ?> - <?php echo $car['model']; ?></h1>
        <div class="car-details">
            <!-- Image on the left -->
            <img src="uploads/<?php echo $car['image']; ?>" alt="<?php echo $car['name']; ?>" />
            
            <!-- Description on the right -->
            <div class="car-info">
                <p><span>Price:</span> KES <?php echo number_format($car['price'], 2); ?></p>
                <p><span>Description:</span> <?php echo $car['description']; ?></p>
                <a href="index.php">Back to Listings</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
