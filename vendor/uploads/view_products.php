<?php
include 'db_config.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>View Products</h2>";
    echo "<table>";
    echo "<tr><th>Product Name</th><th>Description</th><th>Price</th><th>Category</th><th>Image</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product_description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td><img src='" . htmlspecialchars($row['image_path']) . "' width='100' /></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No products found!</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="customer_home.php" class="back-link">Back to Customer Home</a>
</body>
</html>
