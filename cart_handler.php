<?php
session_start();

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
        // Sanitize and validate input
        $productName = sanitize_input($_POST['name']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default to 1 if not set
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00; // Default to 0.00 if not set
        $size = sanitize_input($_POST['size']);

        // Validate quantity
        if ($quantity <= 0) {
            echo 'Invalid quantity.';
            exit;
        }

        // Validate price
        if ($price <= 0) {
            echo 'Invalid price.';
            exit;
        }

        // Add product to session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product is already in the cart
        $productFound = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['name'] === $productName && $cartItem['size'] === $size) {
                // Update quantity if the product is already in the cart
                $cartItem['quantity'] += $quantity;
                $productFound = true;
                break;
            }
        }

        if (!$productFound) {
            // Add new product if it's not in the cart
            $_SESSION['cart'][] = [
                'name' => $productName,
                'quantity' => $quantity,
                'price' => $price,
                'size' => $size
            ];
        }

        echo 'Product added to cart';
    } else {
        echo 'Invalid action.';
    }
} else {
    echo 'Invalid request method.';
}
?>
