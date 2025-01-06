<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: vendor_orders.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch the current status of the order
$sql = "SELECT status FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$current_status = $order['status'];

// Handle the status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Order status updated successfully!'); window.location.href = 'vendor_orders.php';</script>";
    } else {
        echo "<script>alert('Failed to update order status.'); window.location.href = 'vendor_orders.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order Status</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        .edit-status-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .edit-status-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-status-form label {
            font-size: 1.1em;
            margin-bottom: 10px;
            display: block;
        }
        .edit-status-form select, .edit-status-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .edit-status-form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .edit-status-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="edit-status-form">
        <h2>Edit Order Status</h2>
        <form method="POST" action="">
            <label for="status">Select New Status</label>
            <select id="status" name="status" required>
                <option value="Pending" <?php echo ($current_status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Confirmed" <?php echo ($current_status === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Delivered" <?php echo ($current_status === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                <option value="Cancelled" <?php echo ($current_status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit">Update Status</button>
        </form>
    </div>
</body>
</html>
