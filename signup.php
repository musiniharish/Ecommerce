<?php
// db_connection.php (Ensure this file exists and is correct)
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP
$dbname = "ecommerce_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
// signup.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $sql = "SELECT username, email FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            // Insert new user into database
            $sql = "INSERT INTO users (first_name, last_name, username, email, password, user_type) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $first_name, $last_name, $username, $email, $hashed_password, $user_type);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    <style>
        /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color :antiquewhite;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* Container for both Login and Signup */
.signup-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 28px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 500px; /* Increased width for two-column layout */
    text-align: center;
}

/* Form Heading */
h2 {
    margin-bottom: 20px;
    font-size: 22px;
    color: #333;
    font-weight: 600;
}

/* Form Layout */
form {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

/* Two-Column Layout for Labels and Inputs */
form .form-group {
    display: flex;
    flex-direction: column;
    width: 48%; /* Each input takes 48% of the form width */
}

label {
    font-size: 13px;
    margin-bottom: 5px;
    color: #555;
    text-align: left;
}

input[type="text"], input[type="email"], input[type="password"], select {
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: border 0.3s ease;
}

input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Full-width elements for dropdown and button */
.full-width {
    width: 100%;
    margin-top: 20px; /* Space above user type dropdown */
    display: flex;
    justify-content: center;
}

.full-width select {
    width: 100%; /* Make the dropdown full width */
}

/* Button Styling */
button[type="submit"] {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Error Message */
.error {
    color: red;
    margin-bottom: 10px;
    font-size: 13px;
}

/* Adjusted paragraph size */
p {
    font-size: 15px; /* Increased font size */
    color: #555;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 600px) {
    .signup-container, .login-container {
        width: 100%;
    }

    form .form-group {
        width: 100%;
    }
}

    </style>
</head>
<body>
<div class="signup-container">
    <h2>Sign Up</h2>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group full-width">
            <label for="user_type">User Type:</label>
            <select id="user_type" name="user_type" required>
                <option value="vendor">Vendor</option>
                <option value="customer">Customer</option>
                <option value="super_admin">Super Admin</option>
                <option value="delivery_partner">Delivery Partner</option>
                <option value="customer-care">Customer Care</option>
            </select>
        </div>
        
        <button type="submit" class="full-width">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</div>

</body>
</html>
