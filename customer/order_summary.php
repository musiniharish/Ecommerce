<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the user
$sql = "SELECT c.cart_id, p.product_name, p.price, c.quantity FROM cart c 
        JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_amount = 0;
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $total_price = $row['price'] * $row['quantity'];
    $total_amount += $total_price;
    $cart_items[] = $row + ['total_price' => $total_price];
}

// Calculate discount and delivery charges
$discount = $total_amount * 0.05; // 5% discount
$delivery_charge = 40.00; // Flat delivery charge
$final_amount = $total_amount - $discount + $delivery_charge;

// Handle placing the order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert the order record
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_date, status) VALUES (?, ?, NOW(), 'Pending')");
    $stmt->bind_param("id", $user_id, $final_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Redirect to payment page
    header("Location: payment.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Example CSS for order summary */
        .order-summary {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .order-summary h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-summary table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-summary table, th, td {
            border: 1px solid #ddd;
        }
        .order-summary th, td {
            padding: 10px;
            text-align: left;
        }
        .order-summary .total {
            font-size: 1.2em;
            text-align: right;
            margin-bottom: 20px;
        }
        .order-summary .summary {
            text-align: right;
            margin-bottom: 10px;
        }
        .order-summary button {
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
        .order-summary button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="order-summary">
        <h2>Order Summary</h2>
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
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary">
            <strong>Subtotal: ₹<?php echo number_format($total_amount, 2); ?></strong><br>
            <strong>Discount (5%): -₹<?php echo number_format($discount, 2); ?></strong><br>
            <strong>Delivery Charge: ₹<?php echo number_format($delivery_charge, 2); ?></strong><br>
        </div>
        <div class="total">
            <strong>Final Amount: ₹<?php echo number_format($final_amount, 2); ?></strong>
        </div>
        <form method="POST" action="">
            <button type="submit">Place Order</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
