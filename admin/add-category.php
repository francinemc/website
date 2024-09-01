<?php
// Database connection
require_once '../db.php';

$message = ''; // Variable to hold success or error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category-name'];

    // Prepare SQL statement to insert data into the categories table
    $sql = "INSERT INTO categories (category_name) VALUES (?)";
    $stmt = $mysqli->prepare($sql);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("s", $category_name); // Added closing parenthesis here

        if ($stmt->execute()) {
            $message = "Category added successfully.";
        } else {
            $message = "Failed to add category.";
        }

        $stmt->close();
    } else {
        $message = "Failed to prepare the SQL statement.";
    }
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="add-category.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #121416; /* Color 3 */
            padding: 20px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            color: #efc143; /* Color 1 */
            font-size: 22px;
            text-align: center;
            margin: 0;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            transition: 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #eb7324; /* Color 2 */
            border-radius: 5px;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 300px; /* Offset for sidebar */
        }

        .main-content header {
            background-color: #efc143; /* Color 1 */
            padding: 20px;
            border-bottom: 1px solid #e1e1e1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-content header input {
            width: 200px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #e1e1e1;
        }

        .main-content h2 {
            margin-top: 0;
            color: #121416; /* Color 3 */
        }

        .main-content form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content form label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #121416; /* Color 3 */
        }

        .main-content form input,
        .main-content form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
        }

        .main-content form button {
            background-color: #eb7324; /* Color 2 */
            color: #ffffff;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        .main-content form button:hover {
            background-color: #d15d00; /* Slightly darker shade of Color 2 */
        }

        .message {
            color: #d15d00; /* Error color */
            margin-bottom: 20px;
        }
    </style>
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
            <br>
            
            <h2>Add Category</h2>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="post" action="add-category.php">
                <label for="category-name">Category Name:</label>
                <input type="text" id="category-name" name="category-name" required>
                
                <button type="submit">Add Category</button>
            </form>
        </div>
    </div>
</body>
</html>
