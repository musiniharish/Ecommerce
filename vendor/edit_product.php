<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve and sanitize input
        $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
        $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $image_path = $_POST['current_image'];

        // Handle file upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if ($check === false) {
                $uploadOk = 0;
                $error = "File is not an image.";
            }

            // Check file size
            if ($_FILES["product_image"]["size"] > 500000) {
                $uploadOk = 0;
                $error = "Sorry, your file is too large.";
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $uploadOk = 0;
                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            }

            if ($uploadOk == 0) {
                $error = "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    $image_path = htmlspecialchars(basename($_FILES["product_image"]["name"]));
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            }
        }

        // Update product in the database
        if (!isset($error)) {
            $sql = "UPDATE products SET product_name = ?, product_description = ?, price = ?, quantity = ?, category = ?, image_path = ? WHERE product_id = ? AND vendor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdisisi", $product_name, $product_description, $price, $quantity, $category, $image_path, $product_id, $_SESSION['user_id']);

            if ($stmt->execute()) {
                $success = "Product updated successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $conn->close();
    }

    // Fetch existing product details
    $sql = "SELECT * FROM products WHERE product_id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="product-form">
        <h2>Edit Product</h2>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            
            <label for="product_description">Description:</label>
            <textarea id="product_description" name="product_description" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
            
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
            
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
            
            <label for="product_image">Product Image (optional):</label>
            <input type="file" id="product_image" name="product_image" accept="image/*">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image_path']); ?>">
            
            <?php if (!empty($product['image_path'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="Current Product Image" style="max-width: 200px; max-height: 200px;">
            <?php endif; ?>
            
            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
