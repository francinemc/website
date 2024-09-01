<?php
// Include the database connection file
include '../db.php'; // Ensure this file contains the database connection setup

// Fetch the total count of registered users
$sql = "SELECT COUNT(*) as total_users FROM users WHERE role != 0";
$result = $mysqli->query($sql);
$total_users = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_users = $row['total_users'];
}

// Fetch the total count of products
$sql = "SELECT COUNT(*) as total_products FROM products";
$result = $mysqli->query($sql);
$total_products = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_products = $row['total_products'];
}

// Fetch the total count of orders
$sql = "SELECT COUNT(*) as total_orders FROM orders";
$result = $mysqli->query($sql);
$total_orders = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_orders = $row['total_orders'];
}

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $mysqli->real_escape_string($_GET['search']);
}

// Fetch the 5 most recent registered users with role = 1
$sql = "SELECT full_name, email FROM users WHERE role = 1 AND (full_name LIKE '%$search_query%' OR email LIKE '%$search_query%') ORDER BY id DESC LIMIT 5";
$recent_users = $mysqli->query($sql);

// Fetch the 5 most recent registered users with role = 1
$sql = "SELECT full_name, email FROM users WHERE role = 1 ORDER BY id DESC LIMIT 5";
$recent_users = $mysqli->query($sql);

// Get counts of pending and confirmed orders
$pendingSql = "SELECT COUNT(*) as count FROM orders WHERE status = 'Pending'";
$confirmedSql = "SELECT COUNT(*) as count FROM orders WHERE status = 'Confirmed'";

$pendingResult = $mysqli->query($pendingSql);
$confirmedResult = $mysqli->query($confirmedSql);

$pendingCount = $pendingResult->fetch_assoc()['count'];
$confirmedCount = $confirmedResult->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: beige;
        }

        .dashboard {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            background-color: #121416; /* Color 3 */
            width: 200px;
            padding: 20px;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar h1 {
            font-size: 30px;
            margin-bottom: 20px;
            color: #efc143; /* Color 1 */
        }

        .sidebar .menu {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar .menu li {
            margin-bottom: 15px;
        }

        .sidebar .menu li a {
            text-decoration: none;
            color: white; /* Color 2 */
            font-size: 20px;
            padding: 10px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #efc143; /* Color 1 */
            padding: 10px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-bar input {
            width: 200px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info .role {
            font-size: 14px;
            color: #121416; /* Color 3 */
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-item {
            background-color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin-right: 20px;
        }

        .stat-item h2 {
            font-size: 36px;
            margin: 0 0 10px;
            color: #121416; /* Color 3 */
        }

        .stat-item p {
            margin: 0;
            color: #888;
        }

        .content {
            display: flex;
            justify-content: space-between;
        }

        .followers {
            flex: 1;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }

        .followers table {
            width: 100%;
            border-collapse: collapse;
        }

        .followers table th, .followers table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
            font-size: 12px;
        }

        .status.in-progress {
            background-color: #f39c12;
        }

        .status.completed {
            background-color: #27ae60;
        }

        .map {
            flex: 1;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .map img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <nav class="sidebar">
            <h1>Prime Tire's Dashboard</h1>
            <ul class="menu">
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
                <div class="search-bar">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Search...">
                    </form>
                </div>
                <div class="user-info">
                    <span class="role">Administrator</span>
                </div>
            </header>

            <div class="stats">
                <div class="stat-item">
                    <h2><?php echo $total_users; ?></h2>
                    <p>Registered Users</p>
                </div>
                <div class="stat-item">
                    <h2><?php echo $total_products; ?></h2>
                    <p>Total Products</p>
                </div>
                <div class="stat-item">
                    <h3>Pending Orders</h3>
                    <p><?php echo $pendingCount; ?></p>
                </div>
                <div class="stat-item">
                   <h3>Confirmed Orders</h3>
                   <p><?php echo $confirmedCount; ?></p>
                </div>
            </div>
    
            <div class="content">
                <div class="followers">
                    <h3>Recent Followers</h3>
                    <table>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                        </tr>
                        <?php
                        if ($recent_users->num_rows > 0) {
                            $i = 1;
                            while ($row = $recent_users->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$i}</td>
                                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                      </tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='3'>No recent followers found</td></tr>";
                        }
                        ?>
                    </table>
                </div>

                <div class="map">
                    <h3>World Market</h3>
                    <img src="world-map.jpg" alt="World Map">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
