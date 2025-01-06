<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Menu</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start; /* Align items to the top */
    height: 100vh;
}

/* Vendor Menu Container */
.vendor-menu {
    width: 100%;
    max-width: 800px;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    text-align: center;
    margin-top: 20px; /* Add margin to create space from the top */
}

/* Header */
h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

/* Navigation Menu */
.vendor-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: space-between; /* Distribute space evenly */
    align-items: center;
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
}

/* Navigation Items */
.vendor-menu li {
    margin: 5px;
}

.vendor-menu a {
    display: block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    text-align: center;
    white-space: nowrap;
}

.vendor-menu a:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 600px) {
    .vendor-menu ul {
        flex-direction: column; /* Stack items vertically on small screens */
        align-items: stretch; /* Stretch items to fill the container width */
    }

    .vendor-menu li {
        margin: 10px 0;
    }
}

    </style>
</head>
<body>
    <div class="vendor-menu">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <ul>
            <li><a href="view_product.php">View Products</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="vendor_orders.php">Orders</a></li>
            <li><a href="../login.php">Logout</a></li>
        </ul>
    </div>
</body>
</html>
