<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: order_history.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order details
$sql = "SELECT o.order_id, o.total_amount, o.status, o.order_date, p.product_name, p.price, op.quantity 
        FROM orders o 
        JOIN order_products op ON o.order_id = op.order_id 
        JOIN products p ON op.product_id = p.product_id 
        WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$order_details = [];

while ($row = $result->fetch_assoc()) {
    $order_details[] = $row;
}

if (empty($order_details)) {
    echo "<p>Order details not found or you don't have permission to view this order.</p>";
    exit();
}

$order_info = $order_details[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Example CSS for order details */
        .order-details {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .order-details h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-details table, th, td {
            border: 1px solid #ddd;
        }
        .order-details th, td {
            padding: 10px;
            text-align: left;
        }
        .order-details th {
            background-color: #f1f1f1;
        }
        .order-summary {
            font-size: 1.2em;
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="order-details">
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_info['order_id']); ?></p>
        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_info['order_date']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order_info['status']); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="order-summary">
            <strong>Total Amount: $<?php echo number_format($order_info['total_amount'], 2); ?></strong>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
