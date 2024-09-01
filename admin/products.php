<?php
// Include the database connection file
include '../db.php'; // Adjust path as necessary

// Initialize variables
$products = [];
$message = ''; // For error messages

try {
    // Query to fetch products with their sizes, categories, brands, and images
    $query = "
        SELECT p.id AS product_id, p.name AS product_name, b.brand_name, p.stock, p.price, p.description,
               GROUP_CONCAT(DISTINCT s.size_value ORDER BY s.size_value ASC) AS sizes,
               GROUP_CONCAT(DISTINCT pi.image_path ORDER BY pi.image_path ASC) AS images
        FROM products p
        JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN product_sizes ps ON p.id = ps.id
        LEFT JOIN sizes s ON ps.size_id = s.size_id
        LEFT JOIN product_images pi ON p.id = pi.product_id
        GROUP BY p.id
        ORDER BY p.id
    ";

    $result = $mysqli->query($query);

    if (!$result) {
        throw new Exception("Query failed: " . $mysqli->error);
    }

    // Fetch all products
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    if (empty($products)) {
        $message = "No products found.";
    }
} catch (Exception $e) {
    $message = "Database error: " . $e->getMessage();
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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
            background-color: #121416; /* Dark background color */
            padding: 20px;
            min-height: 100vh;
        }

        .sidebar h2 {
            color: #efc143; /* Yellow color */
            font-size: 22px;
            text-align: center;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
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
            background-color: #eb7324; /* Orange color */
            border-radius: 5px;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .main-content header {
            background-color: #efc143; /* Yellow color */
            padding: 20px;
            border-bottom: 1px solid #e1e1e1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-content h2 {
            margin-top: 0;
            color: #121416; /* Dark color */
        }

        .main-content input[type="text"] {
            padding: 10px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
        }

        .user-info {
            color: #121416; /* Dark color */
        }

        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #e1e1e1;
            text-align: left;
        }

        table th {
            background-color: #eb7324; /* Orange color */
            color: #ffffff;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Ensure table size fits well */
        table th, table td {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
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

            <h2>Products</h2>

            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Sizes</th>
                            <th>Images</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                                    <td><?php echo htmlspecialchars($product['sizes']); ?></td>
                                    <td>
                                        <?php if (!empty($product['images'])): ?>
                                            <?php $images = explode(',', $product['images']); ?>
                                            <?php foreach ($images as $image): ?>
                                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Product Image" style="max-width: 100px; max-height: 100px;">
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
