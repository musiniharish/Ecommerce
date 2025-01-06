<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

// Check if the user is logged in as a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

// Fetch all orders for the vendor to manage
$sql = "SELECT order_id, user_id, total_amount, status, order_date FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Orders</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        .order-list {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .order-list h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-list table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-list table, th, td {
            border: 1px solid #ddd;
        }
        .order-list th, td {
            padding: 10px;
            text-align: left;
        }
        .order-list th {
            background-color: #f1f1f1;
        }
        .order-list .confirm-button,
        .order-list .edit-button,
        .order-list .view-button {
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            border: none;
        }
        .order-list .confirm-button {
            background-color: #28a745;
        }
        .order-list .confirm-button:hover {
            background-color: #218838;
        }
        .order-list .edit-button {
            background-color: #ffc107;
        }
        .order-list .edit-button:hover {
            background-color: #e0a800;
        }
        .order-list .view-button {
            background-color: #007bff;
        }
        .order-list .view-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="order-list">
        <h2>Manage Orders</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td>
                                <?php if ($order['status'] === 'Pending'): ?>
                                    <a href="vendor_confirm_order.php?order_id=<?php echo $order['order_id']; ?>" class="confirm-button">Confirm Order</a>
                                <?php elseif ($order['status'] === 'Confirmed'): ?>
                                    Confirmed
                                <?php elseif ($order['status'] === 'Delivered'): ?>
                                    Delivered
                                <?php elseif ($order['status'] === 'Cancelled'): ?>
                                    Cancelled
                                <?php endif; ?>
                                <a href="vendor_edit_order.php?order_id=<?php echo $order['order_id']; ?>" class="edit-button">Edit Status</a>
                                <a href="vendor_order_details.php?order_id=<?php echo $order['order_id']; ?>" class="view-button">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
