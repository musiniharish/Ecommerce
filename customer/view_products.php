<?php
// Database connection
include "../db.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e0f7fa;
    margin: 0;
    padding: 20px;
    color: #444;
}
h2 {
    text-align: center;
    color: #00796b;
    font-size: 2.5em;
    margin-bottom: 40px;
    text-transform: uppercase;
}
.product-list {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
}
.product-item {
    background-color: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    max-width: 300px;
    text-align: center;
    padding: 25px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
.product-item img {
    max-width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 2px solid #00796b;
    margin-bottom: 20px;
    transition: transform 0.3s ease; /* Add transition for smooth zoom */
}
.product-item:hover img {
    transform: scale(1.1); /* Zoom effect on hover */
}
.product-item h3 {
    font-size: 1.7em;
    margin: 15px 0;
    color: #00796b;
}
.product-item p {
    color: #777;
    margin: 15px 0;
    font-size: 1.2em;
}
.product-item .btn {
    display: block;
    margin: 15px 0;
    padding: 12px 25px;
    background-color: #007BFF;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}
.product-item .btn:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}
.product-item .buy-now {
    background-color: #28a745;
}
.product-item .buy-now:hover {
    background-color: #218838;
}

    </style>
</head>
<body>
    <h2>Product List</h2>
    <div class="product-list">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='product-item'>";
                echo "<img src='../customer/uploads/" . $row["image_path"] . "' alt='" . htmlspecialchars($row["product_name"]) . "'>";
                echo "<h3>" . htmlspecialchars($row["product_name"]) . "</h3>";
                echo "<p>Price: $" . htmlspecialchars($row["price"]) . "</p>";
                echo "<a href='manage_cart.php?action=add&product_id=" . $row["product_id"] . "' class='btn'>Add to Cart</a>";
                echo "<a href='payment.php?product_id=" . $row["product_id"] . "' class='btn buy-now'>Buy Now</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
