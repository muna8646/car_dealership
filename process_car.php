<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "car_dealership");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Handle file upload
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert car data into the database
        $sql = "INSERT INTO cars (name, model, price, description, image) 
                VALUES ('$name', '$model', '$price', '$description', '$image_name')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New car added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading image.";
    }
}

// Close the connection
$conn->close();
?>
