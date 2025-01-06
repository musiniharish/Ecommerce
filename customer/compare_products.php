<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Fetch all products for comparison
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Products</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="compare-products">
        <h2>Compare Products</h2>
        <form method="post" action="compare_results.php">
            <label for="product1">Select First Product:</label>
            <select id="product1" name="product1">
                <?php while ($product = $result->fetch_assoc()): ?>
                    <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="product2">Select Second Product:</label>
            <select id="product2" name="product2">
                <?php
                // Reset the result pointer to fetch the products again
                $result->data_seek(0);
                while ($product = $result->fetch_assoc()): ?>
                    <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
                <?php endwhile; ?>
            </select>
            
            <button type="submit">Compare</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
