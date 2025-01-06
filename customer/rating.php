<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Handle rating submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Prepare and execute the SQL statement
    $sql = "INSERT INTO reviews (user_id, product_id, rating, review_text) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $_SESSION['user_id'], $product_id, $rating, $review);

    if ($stmt->execute()) {
        $success = "Thank you for your review!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch products for rating
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Products</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
    <style>
        .rating {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .rating h2 {
            text-align: center;
        }
        .rating form {
            display: flex;
            flex-direction: column;
        }
        .rating label {
            margin-bottom: 5px;
        }
        .rating input[type="number"],
        .rating textarea {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .rating button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .rating button:hover {
            background-color: #0056b3;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="rating">
        <h2>Rate Products</h2>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="">
            <label for="product_id">Select Product:</label>
            <select id="product_id" name="product_id" required>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required>

            <label for="review">Review:</label>
            <textarea id="review" name="review" rows="4" required></textarea>

            <button type="submit">Submit Review</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
