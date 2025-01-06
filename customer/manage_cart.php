<?php
session_start();
include '../db.php'; // Ensure this file correctly connects to your database

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle adding products to the cart
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']); // Sanitize input

    // Check if product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if already in cart
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        // Insert new item into cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $user_id, $product_id);
    }

    $stmt->execute();
    header("Location: manage_cart.php"); // Redirect to manage_cart after adding
    exit();
}

// Handle updating cart quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_cart') {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $quantity = intval($quantity); // Sanitize input
            if ($quantity > 0) {
                // Update cart item quantity
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
            } else {
                // Remove item if quantity is 0 or less
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            }
        }
    }
}

// Fetch cart items
$cart_items = [];
$total_price = 0;

$stmt = $conn->prepare("SELECT c.product_id, p.product_name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($item = $result->fetch_assoc()) {
    $item['total_price'] = $item['price'] * $item['quantity'];
    $total_price += $item['total_price'];
    $cart_items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cart</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .cart-container {
            width: 100%;
            max-width: 800px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td input[type="number"] {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .total-price {
            font-size: 1.5rem;
            color: #333;
            text-align: right;
            margin-bottom: 20px;
        }

        button[type="submit"], .checkout-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        button[type="submit"]:hover, .checkout-btn:hover {
            background-color: #0056b3;
        }

        a.checkout-btn {
            display: inline-block;
            text-decoration: none;
            font-size: 1.2rem;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .cart-container {
                padding: 15px;
            }

            h1 {
                font-size: 1.8rem;
            }

            th, td {
                padding: 8px;
            }

            .total-price {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1>Manage Cart</h1>
        <form method="POST" action="">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cart_items)): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $item['product_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0">
                                </td>
                                <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Your cart is empty.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($cart_items)): ?>
                <button type="submit" name="action" value="update_cart">Update Cart</button>
            <?php endif; ?>
        </form>
        <div class="total-price">
            Total Price: $<?php echo number_format($total_price, 2); ?>
        </div>
        <?php if (!empty($cart_items)): ?>
            <a href="order_summary.php" class="checkout-btn">Proceed to Checkout</a>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
