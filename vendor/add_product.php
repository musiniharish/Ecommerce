<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Ensure the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and file upload
    $vendor_id = $_SESSION['user_id'];
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
    $product_description = isset($_POST['product_description']) ? trim($_POST['product_description']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $image_path = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';

    // Check if all required fields are provided
    if (!empty($product_name) && !empty($product_description) && !empty($price) && !empty($category) && !empty($image_path)) {
        // Define the path for image upload
        $target_dir = "../customer/uploads/";
        $target_file = $target_dir . basename($image_path);
        
        // Attempt to upload the image
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Prepare the SQL query
            $sql = "INSERT INTO products (vendor_id, product_name, product_description, price, category, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters (i = integer, s = string, d = double)
                $stmt->bind_param('issdss', $vendor_id, $product_name, $product_description, $price, $category, $image_path);
                
                // Execute the statement
                if ($stmt->execute()) {
                    echo "Product added successfully.";
                } else {
                    echo "Error executing query: " . $stmt->error;
                }
                
                // Close the statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error uploading file. Please check the file and try again.";
        }
    } else {
        echo "Please fill in all required fields and upload an image.";
    }
} else {
    // Display the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" id="product_name" required>
        
        <label for="product_description">Product Description:</label>
        <textarea name="product_description" id="product_description" required></textarea>
        
        <label for="price">Price:</label>
        <input type="text" name="price" id="price" required>
        
        <label for="category">Category:</label>
        <input type="text" name="category" id="category" required>
        
        <label for="image">Image:</label>
        <input type="file" name="image" id="image" required>
        
        <input type="submit" value="Add Product">
    </form>
</body>
</html>
<?php
}

// Close the database connection
$conn->close();
?>
