<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Fetch products from the database
$sql = "SELECT * FROM products WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: calc(33% - 20px); /* Adjust width as needed */
            box-sizing: border-box;
        }
        .product-item img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .product-item a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .product-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="product-list">
        <h2>Your Products</h2>
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product-item">
                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                <p><?php echo htmlspecialchars($product['product_description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                
                <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                <?php if (!empty($product['image_path'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">Edit</a>
                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
