<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" id="product_name" required>
        
        <label for="product_description">Product Description:</label>
        <textarea name="product_description" id="product_description" required></textarea>
        
        <label for="price">Price:</label>
        <input type="text" name="price" id="price" required>
        
        <label for="category">Category:</label>
        <input type="text" name="category" id="category" required>
        
        <label for="image">Image:</label>
        <input type="file" name="image" id="image" required>
        
        <input type="submit" value="Add Product">
    </form>
</body>
</html>
