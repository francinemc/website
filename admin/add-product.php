<?php
include '../db.php'; // Adjust path as necessary

$message = ''; // Initialize message variable

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $sizes = $_POST['sizes'] ?? []; // Array of selected sizes
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Initialize an array to hold image paths
    $imagePaths = [];

    // Handle image uploads
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        
        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $originalName = $_FILES['images']['name'][$key];
            // Generate a unique name for the image
            $uniqueName = uniqid() . '-' . basename($originalName);
            $imagePath = $uploadDir . $uniqueName;
    
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($tmpName, $imagePath)) {
                // Store only the file name, not the full path
                $imagePaths[] = $uniqueName;
            } else {
                $message = "Failed to upload image: $originalName";
                break;
            }
        }
    
    

        if (empty($message)) {
            // Prepare SQL statement to insert product data
            $stmt = $mysqli->prepare("INSERT INTO products (name, category, brand_id, description, stock, price) VALUES (?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                // Bind parameters
                $stmt->bind_param('ssisii', $productName, $category, $brand, $description, $stock, $price);

                // Execute the statement
                if ($stmt->execute()) {
                    $productId = $stmt->insert_id;
                    $stmt->close();

                    // Insert sizes into the product_sizes table
                    if (!empty($sizes)) {
                        $stmt = $mysqli->prepare("INSERT INTO product_sizes (id, size_id) VALUES (?, ?)");
                        
                        if ($stmt) {
                            foreach ($sizes as $sizeId) {
                                if (isset($productId) && isset($sizeId)) {
                                    $stmt->bind_param('ii', $productId, $sizeId);
                                    $stmt->execute();
                                }
                            }
                            $stmt->close();
                        } else {
                            $message = "Failed to prepare SQL statement for product_sizes.";
                        }
                    } else {
                        $message = "No sizes selected.";
                    }

                    // Insert image paths into the product_images table
                    $stmt = $mysqli->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                    
                    if ($stmt) {
                        foreach ($imagePaths as $path) {
                            $stmt->bind_param('is', $productId, $path);
                            $stmt->execute();
                        }
                        $stmt->close();
                        $message = "Product added successfully with images.";
                    } else {
                        $message = "Failed to prepare image SQL statement.";
                    }
                } else {
                    $message = "Failed to add product.";
                }
            } else {
                $message = "Failed to prepare the SQL statement.";
            }
        }
    } else {
        $message = "No images uploaded or upload error.";
    }
}

// Fetch categories, sizes, and brands for dropdowns
$categories = [];
$sizes = [];
$brands = []; // Initialize brands array

try {
    // Fetch categories
    $result = $mysqli->query("SELECT category_id, category_name FROM categories");
    if ($result) {
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
    }

    // Fetch sizes
    $result = $mysqli->query("SELECT size_id, size_value FROM sizes");
    if ($result) {
        $sizes = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
    }

    // Fetch brands
    $result = $mysqli->query("SELECT brand_id, brand_name FROM brands"); // Adjust table and column names as needed
    if ($result) {
        $brands = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
    }
} catch (mysqli_sql_exception $e) {
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
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: beige;
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

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 20px; /* Adds space between form elements */
        }

        .container form {
            display: flex;
            flex-direction: column;
            gap: 20px; /* Adds space between form elements */
        }

        .container label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .container input[type="text"],
        .container textarea,
        .container select,
        .container input[type="number"] {
            padding: 10px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box; /* Ensures padding and border are included in element's total width and height */
        }

        .container input[type="file"] {
            padding: 3px; /* Adjust padding for file input */
        }

        .size-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .size-container label {
            margin: 0;
        }

        .size-container input[type="checkbox"] {
            margin-right: 10px;
            vertical-align: middle; /* Align checkbox with text */
        }

        .size-container input[type="checkbox"] + label {
            vertical-align: middle; /* Align label with checkbox */
        }

        .container button {
            padding: 10px;
            background-color: #efc143; /* Yellow color */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .container button:hover {
            background-color: #d15d00; /* Darker orange shade */
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
            <h2>Add Product</h2>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <div class="container">
                <form action="add-product.php" method="POST" enctype="multipart/form-data">
                    <label for="product-name">Product Name:</label>
                    <input type="text" id="product-name" name="product_name" required>

                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category_id']); ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="brand">Brand:</label>
                    <select id="brand" name="brand" required>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo htmlspecialchars($brand['brand_id']); ?>">
                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>

                    <div class="size-container">
                        <label>Size:</label>
                        <?php foreach ($sizes as $sz): ?>
                            <div>
                                <input type="checkbox" id="size-<?php echo $sz['size_id']; ?>" name="sizes[]" value="<?php echo htmlspecialchars($sz['size_id']); ?>">
                                <label for="size-<?php echo $sz['size_id']; ?>"><?php echo htmlspecialchars($sz['size_value']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required>

                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required>

                    <label for="images">Images:</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple required>

                    <button type="submit">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
