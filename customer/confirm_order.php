<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Invalid order.";
    exit();
}

// Fetch order details
$sql = "SELECT o.order_id, o.total_amount, o.order_date, o.status, u.first_name, u.last_name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch cart items for the order
$sql_items = "SELECT p.product_name, p.price, c.quantity 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $_SESSION['user_id']);
$stmt_items->execute();
$items = $stmt_items->get_result();

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update order status to 'Confirmed'
    $stmt = $conn->prepare("UPDATE orders SET status = 'Confirmed' WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Clear the cart after order is confirmed
    $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear_cart->bind_param("i", $_SESSION['user_id']);
    $stmt_clear_cart->execute();

    // Redirect to a thank you page or order confirmation page
    header("Location: thank_you.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Example CSS for confirm order */
        .confirm-order {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .confirm-order h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .confirm-order table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .confirm-order table, th, td {
            border: 1px solid #ddd;
        }
        .confirm-order th, td {
            padding: 10px;
            text-align: left;
        }
        .confirm-order .total {
            font-size: 1.2em;
            text-align: right;
            margin-bottom: 20px;
        }
        .confirm-order button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }
        .confirm-order button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="confirm-order">
        <h2>Confirm Order</h2>
        <p>Order Number: <?php echo htmlspecialchars($order['order_id']); ?></p>
        <p>Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p>Customer: <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
        <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="total">
            <strong>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></strong>
        </div>
        <form method="POST" action="">
            <button type="submit">Confirm Order</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
