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

// Update order status to 'Confirmed'
$sql = "UPDATE orders SET status = 'Confirmed' WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Order confirmed successfully!'); window.location.href = 'vendor_orders.php';</script>";
} else {
    echo "<script>alert('Failed to confirm order.'); window.location.href = 'vendor_orders.php';</script>";
}

$stmt->close();
$conn->close();
