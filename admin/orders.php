<?php
$host = 'localhost';
$dbname = 'e-commerce';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch orders with user details
    $sql = 'SELECT orders.id AS order_id, orders.user_id, orders.created_at, orders.total, orders.status,
                   users.full_name, users.phone, users.barangay, users.postal_code
            FROM orders
            JOIN users ON orders.user_id = users.id';
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output HTML
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orders</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                background-color: #eb7324; /* Color 2 */
                color: #ffffff;
                font-weight: bold;
            }

            table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            button {
                background-color: #eb7324; /* Color 2 */
                color: #ffffff;
                font-size: 14px;
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                cursor: pointer;
            }

            button:hover {
                background-color: #d15d00; /* Slightly darker shade of Color 2 */
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
        function updateOrderStatus(orderId, status) {
            $.ajax({
                url: "update_order_status.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({ orderId: orderId, status: status }),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert("Order status updated successfully!");
                        location.reload();
                    } else {
                        alert("Error updating order status: " + result.error);
                    }
                }
            });
        }

            function deleteOrder(orderId) {
                if (confirm("Are you sure you want to delete this order?")) {
                    $.ajax({
                        url: "delete_order.php",
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify({ orderId: orderId }),
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.success) {
                                alert("Order deleted successfully!");
                                location.reload();
                            } else {
                                alert("Error deleting order: " + result.error);
                            }
                        }
                    });
                }
            }
        </script>
    </head>
    <body>
        <div class="dashboard">
            <nav class="sidebar">
                <h2>Prime Tire\'s Dashboard</h2>
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
                
                <h2>Orders</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Barangay</th>
                                <th>Postal Code</th>
                                <th>Date</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';

    foreach ($orders as $order) {
        echo '<tr>
                <td>' . htmlspecialchars($order['order_id']) . '</td>
                <td>' . htmlspecialchars($order['user_id']) . '</td>
                <td>' . htmlspecialchars($order['full_name']) . '</td>
                <td>' . htmlspecialchars($order['phone']) . '</td>
                <td>' . htmlspecialchars($order['barangay']) . '</td>
                <td>' . htmlspecialchars($order['postal_code']) . '</td>
                <td>' . htmlspecialchars($order['created_at']) . '</td>
                <td>₱' . htmlspecialchars(number_format($order['total'], 2)) . '</td>
                <td>' . htmlspecialchars($order['status']) . '</td>
                <td>
                    <button class="btn btn-success" onclick="updateOrderStatus(' . htmlspecialchars($order['order_id']) . ', \'completed\')">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-warning" onclick="updateOrderStatus(' . htmlspecialchars($order['order_id']) . ', \'shipped\')">
                        <i class="fas fa-truck"></i>
                    </button>
                    <button class="btn btn-danger" onclick="updateOrderStatus(' . htmlspecialchars($order['order_id']) . ', \'canceled\')">
                        <i class="fas fa-times"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteOrder(' . htmlspecialchars($order['order_id']) . ')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>';
    }

    echo '        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
    </html>';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
