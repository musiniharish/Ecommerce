<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Handle order tracking
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Fetch order details
    $sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="track-order">
        <h2>Track Order</h2>
        <form method="post" action="">
            <label for="order_id">Enter Order ID:</label>
            <input type="text" id="order_id" name="order_id" required>
            <button type="submit">Track Order</button>
        </form>

        <?php if (isset($order)): ?>
            <h3>Order Details</h3>
            <p>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></p>
            <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
            <p>Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
