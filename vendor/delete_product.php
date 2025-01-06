<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Delete related cart entries
        $sql_cart = "DELETE FROM cart WHERE product_id = ?";
        $stmt_cart = $conn->prepare($sql_cart);
        $stmt_cart->bind_param("i", $product_id);
        $stmt_cart->execute();
        $stmt_cart->close();

        // Delete the product from the database
        $sql_product = "DELETE FROM products WHERE product_id = ? AND vendor_id = ?";
        $stmt_product = $conn->prepare($sql_product);
        $stmt_product->bind_param("ii", $product_id, $_SESSION['user_id']);
        $stmt_product->execute();
        $stmt_product->close();

        // Commit transaction
        $conn->commit();

        $success = "Product deleted successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = "Error: " . $e->getMessage();
    }

    $conn->close();

    // Redirect back to view products
    header("Location: view_product.php");
    exit();
} else {
    die("Invalid product ID.");
}
