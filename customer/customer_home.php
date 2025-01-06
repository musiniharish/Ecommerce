<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

// Retrieve customer data if necessary
include '../db.php';

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists and fetch their data
if ($row = $result->fetch_assoc()) {
    $first_name = htmlspecialchars($row['first_name']);
    $last_name = htmlspecialchars($row['last_name']);
} else {
    $first_name = 'Guest';
    $last_name = '';
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Home</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    height: 100vh;
    background-image: url("../images/customer_home.webp");
    background-size: cover;
    background-position: center;
   }
   .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 20px;
    padding: 0 15px;
  }
  .navigation ul {
    list-style-type: none;
    padding: 0;
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
    margin: 0;
}

.navigation ul li {
    margin: 0 10px 10px 0;
}

.navigation ul li a {
    text-decoration: none;
    color: #ffffff;
    background-color: #007bff;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    display: inline-block;
}

.navigation ul li a:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
        padding: 0 15px;
    }

    .header h1 {
        font-size: 2em;
        margin-bottom: 10px;
    }

    .navigation ul {
        justify-content: center;
    }

    .navigation ul li {
        margin: 5px;
    }
}

@media (max-width: 600px) {
    .customer-home {
        padding: 15px;
    }

    .navigation ul {
        flex-direction: column;
        align-items: flex-start;
        margin-top: 15px;
    }

    .navigation ul li {
        width: 100%;
        margin: 5px 0;
    }

    .navigation ul li a {
        width: 100%;
        text-align: center;
    }
}

    </style>
</head>
<body>
    <div class="customer-home">
        <h1>Welcome, <?php echo $first_name; ?>!</h1>
        <h3>Last Name: <?php echo $last_name; ?></h3>
        
        <div class="navigation">
            <ul>
                <li><a href="customer_profile.php">Profile</a></li>
                <li><a href="view_products.php">View Products</a></li>
                <li><a href="manage_cart.php">Manage Cart</a></li>
                <li><a href="order_summary.php">Order Summary</a></li>
                <li><a href="rating.php">Rate Products</a></li>
                <li><a href="view_order_history.php">Order History</a></li>
                <li><a href="track_order.php">Track Order</a></li>
                <li><a href="../login.php">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
