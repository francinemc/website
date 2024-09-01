<?php
session_start(); // Start the session at the beginning of the script

// Database connection details
$host = 'localhost'; // Change to your database host
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password
$database = 'e-commerce'; // Change to your database name

// Create a new mysqli object
$mysqli = new mysqli($host, $username, $password, $database);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize update message variable
$update_message = '';
$error_message = '';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve the user ID from the session
$id = $_SESSION['user_id'];

// Fetch current user details
$sql = "SELECT full_name, email, password FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $stored_password);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form input values
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate current password
    if (!password_verify($current_password, $stored_password)) {
        $error_message = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } else {
        // Hash the new password before storing it if it's not empty
        $hashed_password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : $stored_password;

        // Prepare SQL for updating user details
        if ($hashed_password) {
            $sql = "UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssi", $full_name, $email, $hashed_password, $id);
        } else {
            $sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $full_name, $email, $id);
        }

        // Execute the statement and provide feedback
        if ($stmt->execute()) {
            $update_message = "Account updated successfully!";
        } else {
            $error_message = "Error updating account: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Close the connection
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <h2>Prime Tire's Dashboard</h2>
            <ul class="menu">
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="add-category.php">Add Category</a></li>
                <li><a href="add-product.php">Add Product</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <header>
                <input type="text" placeholder="Search...">
                <div class="user-info">
                    <span>Administrator</span>
                </div>
            </header>
            
            <h2>Update Account</h2>

            <?php if ($update_message): ?>
                <div class="update-message">
                    <?php echo htmlspecialchars($update_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>

                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password">

                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                    
                    <button type="submit">Update Settings</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
