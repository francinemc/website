<?php

require_once 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to view your cart.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = null;
$totalItems = 0; 
$subtotal = 0.00;


// Check if 'id' is set and is a valid integer
$data = null;

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $data = $mysqli->real_escape_string($_GET['id']);
    echo "Product ID: " . htmlspecialchars($data) . "<br>";
} else {
    echo "Invalid product ID. Taena kasing Pathing to, palitan nio images";
}

// Get the cart for the user
$sqlCart = "SELECT id FROM carts WHERE user_id = ?";
$stmtCart = $mysqli->prepare($sqlCart);
if ($stmtCart) {
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $resultCart = $stmtCart->get_result();

    if ($resultCart->num_rows > 0) {
        $cart = $resultCart->fetch_assoc();
        $cart_id = $cart['id'];

        // Get the cart items
        $sqlItems = "
        SELECT ci.*, p.name, p.price 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        WHERE ci.cart_id = ?";
        $stmtItems = $mysqli->prepare($sqlItems);
        if ($stmtItems) {
            $stmtItems->bind_param("i", $cart_id);
            $stmtItems->execute();
            $resultItems = $stmtItems->get_result();

            if ($resultItems->num_rows > 0) {
            } else {
                echo "<p>Your cart is empty.</p>";
            }
            $stmtItems->close();
        } else {
            echo "<p>Failed to retrieve cart items.</p>";
        }
    } else {
        echo "<p>Your cart is empty.</p>";
    }
    $stmtCart->close();
} else {
    echo "<p>Failed to retrieve cart.</p>";
}

// Check if a remove action is requested
if (isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Get the cart id
    $sqlCartId = "SELECT id FROM carts WHERE user_id = ?";
    $stmtCartId = $mysqli->prepare($sqlCartId);
    $stmtCartId->bind_param("i", $user_id);
    $stmtCartId->execute();
    $resultCartId = $stmtCartId->get_result();
    $cart = $resultCartId->fetch_assoc();
    $cart_id = $cart['id'];
    $stmtCartId->close();

    // Delete from cart_items
    $sqlDeleteItem = "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmtDeleteItem = $mysqli->prepare($sqlDeleteItem);
    $stmtDeleteItem->bind_param("ii", $cart_id, $product_id);
    $stmtDeleteItem->execute();
    $stmtDeleteItem->close();

    // Check if the cart is empty, if so, delete the cart
    $sqlCheckItems = "SELECT COUNT(*) as itemCount FROM cart_items WHERE cart_id = ?";
    $stmtCheckItems = $mysqli->prepare($sqlCheckItems);
    $stmtCheckItems->bind_param("i", $cart_id);
    $stmtCheckItems->execute();
    $resultCheckItems = $stmtCheckItems->get_result();
    $itemCount = $resultCheckItems->fetch_assoc()['itemCount'];
    $stmtCheckItems->close();

    if ($itemCount === 0) {
        $sqlDeleteCart = "DELETE FROM carts WHERE id = ?";
        $stmtDeleteCart = $mysqli->prepare($sqlDeleteCart);
        $stmtDeleteCart->bind_param("i", $cart_id);
        $stmtDeleteCart->execute();
        $stmtDeleteCart->close();
    }

    // Respond with a success status
    echo json_encode(['status' => 'success']);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    // Get the cart id
    $sqlCartId = "SELECT id FROM carts WHERE user_id = ?";
    $stmtCartId = $mysqli->prepare($sqlCartId);
    $stmtCartId->bind_param("i", $user_id);
    $stmtCartId->execute();
    $resultCartId = $stmtCartId->get_result();
    $cart = $resultCartId->fetch_assoc();
    $cart_id = $cart['id'];
    $stmtCartId->close();
    
    // Update the quantity in cart_items
    $sqlUpdateQuantity = "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?";
    $stmtUpdateQuantity = $mysqli->prepare($sqlUpdateQuantity);
    $stmtUpdateQuantity->bind_param("iii", $quantity, $cart_id, $product_id);
    $stmtUpdateQuantity->execute();
    $stmtUpdateQuantity->close();
    
    // Respond with success status
    echo json_encode(['status' => 'success']);
    header ('Location: cart.php');
}
?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        /* Header */
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }
        nav {
            display: flex;
            justify-content: center;
        }
        /* Main Content */
        main {
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .cart-container {
            max-width: 1200px;
            width: 100%;
        }
        .cart {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            padding: 20px;
            flex-direction: column;
        }
        .cart-items {
            flex: 3;
            margin-right: 20px;
        }
        .cart-header {
            display: grid;
            grid-template-columns: 1fr 3fr 1fr 1fr 1fr;
            background-color: #efefef;
            padding: 10px;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        .header-item {
            text-align: center;
        }
        .cart-item {
            display: grid;
            grid-template-columns: 1fr 3fr 1fr 1fr 1fr;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            align-items: center;
        }
        .item-image {
            width: 100px;
            height: 100px;
            background-size: cover;
            background-position: center;
        }
        .item-product, .item-price, .item-quantity, .item-remove {
            text-align: center;
        }
        .item-quantity input {
            width: 60px;
            text-align: center;
        }
        .remove-btn, .update-btn {
            background-color: transparent;
            border: none;
            color: #333;
            cursor: pointer;
            font-size: 16px;
        }
        .remove-btn:hover, .update-btn:hover {
            color: #e74c3c;
        }
        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid transparent;
            color: #1a1919;
            background-color: transparent;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #d4d004;
            cursor: pointer;
        }
        .cart-totals {
            flex: 1;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .totals-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .label {
            font-weight: bold;
        }
        .value {
            color: #333;
        }
        .checkout-btn {
            background-color: transparent;
            border: 2px solid #28a745;
            color: #28a745;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
        }

        .checkout-btn:hover {
        background-color: #28a745 !important;
        color: white !important;
        transform: scale(1.05);
        }
        .product-details {
            padding-left: 20px;
        }
        .product-container {
            background-color: black;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .product-img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-left: 55px;
        }


        @media (max-width: 768px) {
            .cart {
                flex-direction: column;
            }
            .cart-items, .cart-totals {
                margin-right: 0;
            }
            .cart-header, .cart-item {
                grid-template-columns: 1fr 2fr 1fr 1fr 1fr;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'?>
    <main>
        <div class="cart-container">
            <div class="cart">
                <div class="cart-items">
                    <div class="cart-header">
                        <div class="header-item">Image</div>
                        <div class="header-item">Product</div>
                        <div class="header-item">Price</div>
                        <div class="header-item">Quantity</div>
                        <div class="header-item">Actions</div>
                    </div>
                    <?php if (isset($resultItems) && $resultItems->num_rows > 0): ?>
                        <?php while ($item = $resultItems->fetch_assoc()): ?>
                            <div class="cart-item">
                            <?php if (!empty($item['image'])): ?> 
                                <img class="product-img" src="admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image" />
                            <?php else: ?>
                                <img class="product-img" src="images/gulong.jpg" alt="Default Product Image" />
                            <?php endif; ?>
                                <div class="item-product"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-price"><?php echo number_format($item['price'], 2); ?></div>
                                <div class="item-quantity">
                                    <form class="update-form" method="post">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                        <button type="submit" class="btn update-btn"><i class="fas fa-sync-alt"></i></button>
                                    </form>
                                </div>
                                <div class="item-remove">
                                    <form method="post">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn remove-btn"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </div>
                            <?php
                            $totalItems += $item['quantity'];
                            $subtotal += $item['price'] * $item['quantity'];
                            ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div class="cart-totals">
                    <div class="totals-header">Cart Totals</div>
                    <div class="totals-row">
                        <div class="label">Total Items:</div>
                        <div class="value"><strong><?php echo $totalItems; ?></strong></div>
                    </div>
                    <div class="totals-row">
                        <div class="label">Subtotal:</div>
                        <div class="value">₱ <?php echo number_format($subtotal, 2); ?></div>
                    </div>
                    <div class="totals-row">
                        <div class="label">Shipping Fee:</div>
                        <div class="value">₱ <?php echo number_format($subtotal * .5, 2); ?></div>
                    </div>
                    <div class="totals-row">
                        <div class="label">Total:</div>
                        <div class="value">₱ <strong><?php echo number_format($subtotal * 1.3, 2); ?></strong></div>
                    </div>
                    <a href="checkout.php?cart_id=<?php echo $cart_id; ?>" class="btn btn-lg btn-block checkout-btn">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </main>

        <div class="row mt-4">
            
        </div>
    </div>

           
    <div class="container">
            <hr /><br>
            <p class="lead">Related Products</p>
            <div class="row">
                <?php   
                // Fetch related products
                $sqlRelatedProducts = "
                SELECT * FROM products
                WHERE id != '$data'
                LIMIT 4
                ";
                $relatedProducts = $mysqli->query($sqlRelatedProducts);

                if ($relatedProducts->num_rows > 0) {
                    while ($row = $relatedProducts->fetch_assoc()) {
                        $sqlRelatedImages = "SELECT image_path FROM product_images WHERE product_id='" . $row['id'] . "' LIMIT 1";
                        $relatedImage = $mysqli->query($sqlRelatedImages)->fetch_assoc();
                ?>
                <div class="col-sm-6 col-md-3 mb-4">
                    <div class="product-container">
                        <img class="img-fluid" src="admin/uploads/<?php echo htmlspecialchars($relatedImage['image_path']); ?>" alt="Related Product" />
                        <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p>₱<?php echo htmlspecialchars($row['price']); ?></p>
                        <a href="product_list.php?id=<?php echo $row['id']; ?>" class="btn-details">View Details</a>
                    </div>
                </div>
                <?php } } ?>
            </div>
        </div>
    </div>


    <!-- Add Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Add to Cart</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-to-cart-form">
                        <div class="form-group">
                            <label for="product-id">Product ID:</label>
                            <input type="text" class="form-control" id="product-id" name="product-id">
                        </div>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
         $(document).ready(function() {
            // Handle quantity update
            $('.quantity-input').on('change', function() {
                var productId = $(this).data('product-id');
                var quantity = $(this).val();

                $.ajax({
                    url: 'cart.php', // The PHP file handling the AJAX request
                    type: 'POST',
                    data: {
                        action: 'update',
                        product_id: productId,
                        quantity: quantity
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update the total price and total items dynamically
                            updateCartTotals();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing your request.');
                    }
                });
            });

            // Handle remove button click
            $(document).on('click', '.remove-btn', function() {
                var productId = $(this).data('product-id');

                $.ajax({
                    url: 'cart.php', // The PHP file handling the AJAX request
                    type: 'POST',
                    data: {
                        action: 'remove',
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('button[data-product-id="' + productId + '"]').closest('.cart-item').remove();
                            updateCartTotals();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing your request.');
                    }
                });
            });

            function updateCartTotals() {
                var subtotal = 0;
                var totalItems = 0;

                $('.cart-item').each(function() {
                    var quantity = $(this).find('.quantity-input').val();
                    var price = parseFloat($(this).find('.item-price').text().replace('₱', ''));
                    var total = quantity * price;
                    subtotal += total;
                    totalItems += parseInt(quantity);
                });

                // Check if subtotal is NaN
                if (isNaN(subtotal)) {
                    $('.value').first().text('Reload Page...');
                } else {
                    $('.value').first().text('₱' + subtotal.toFixed(2));
                }
                $('.value').last().text(totalItems);
            }
        });

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'update',
                product_id: productId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateCartTotals();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Log or display detailed error information
                console.log('AJAX Error:', status, error);
                alert('An error occurred while processing your request.');
            }
        });
    </script>
</body>
</html>
