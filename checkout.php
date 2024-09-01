<?php
require_once 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to view your cart.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_GET['cart_id'] ?? null;
$subtotal = 0.00;
$totalItems = 0;

// Generate a unique transaction order number
$transaction_order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

// Fetch cart items and calculate totals
if ($cart_id) {
    $sqlCartItems = "
    SELECT p.id AS product_id, p.name, p.price, SUM(ci.quantity) AS quantity, (p.price * SUM(ci.quantity)) AS total
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.cart_id = ?
    GROUP BY p.id, p.name, p.price";
    $stmtCartItems = $mysqli->prepare($sqlCartItems);
    $stmtCartItems->bind_param("i", $cart_id);
    $stmtCartItems->execute();
    $resultCartItems = $stmtCartItems->get_result();

    $items = [];
    while ($row = $resultCartItems->fetch_assoc()) {
        $items[$row['product_id']] = $row;
        $subtotal += $row['total'];
        $totalItems += $row['quantity'];
    }
    $stmtCartItems->close();

    // Fetch product images for products in the cart
    $product_ids = implode(',', array_keys($items));
    $sqlImages = "
    SELECT product_id, image_path
    FROM product_images
    WHERE product_id IN ($product_ids)
    LIMIT 1";
    $resultImages = $mysqli->query($sqlImages);

    if ($resultImages && $resultImages->num_rows > 0) {
        while ($row = $resultImages->fetch_assoc()) {
            $productId = $row['product_id'];
            if (isset($items[$productId])) {
                $items[$productId]['image'] = $row['image_path'];
            }
        }
        $resultImages->free();
    }
} else {
    echo "<p>Invalid cart ID.</p>";
    exit;
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Summary</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            font-size: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header .order-number {
            font-size: 1rem;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .total-row {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            font-size: 1rem;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            font-size: 1rem;
            padding: 10px 20px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .product-img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        @media (max-width: 576px) {
            .card-header {
                font-size: 1.25rem;
            }
            .btn-primary, .btn-secondary {
                font-size: 0.875rem;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="summary-card">
        <div class="card-header">
            Checkout Summary
            <div class="order-number">Order Number: <?php echo htmlspecialchars($transaction_order_number); ?></div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['image'])): ?> 
                                <img class="product-img" src="admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image" />
                            <?php else: ?>
                                <img class="product-img" src="images/gulong.jpg" alt="Default Product Image" />
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>₱ <?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?> item/s</td>
                        <td>₱ <?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4">Subtotal</td>
                        <td>₱ <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4">Total Items</td>
                        <td><?php echo $totalItems; ?> items</td>
                    </tr>
                </tfoot>
            </table>
            <div class="d-flex justify-content-between">
                <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                <a href="process_order.php?cart_id=<?php echo $cart_id; ?>" class="btn btn-primary">Order Now</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>