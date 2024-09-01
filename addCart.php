<?php
include 'db.php';
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Check if POST request and required data are present
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['productId']) || !isset($_POST['quantity'])) {
    die("Invalid request.");
}

$product_id = filter_var($_POST['productId'], FILTER_VALIDATE_INT);
$quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

if (!$product_id || !$quantity) {
    die("Invalid product ID or quantity.");
}

// Check if a cart already exists for the user
$sqlCart = "SELECT id FROM carts WHERE user_id = ?";
$stmtCart = $mysqli->prepare($sqlCart);
$stmtCart->bind_param("i", $user_id);
$stmtCart->execute();
$resultCart = $stmtCart->get_result();

if ($resultCart->num_rows > 0) {
    // Cart exists, get the cart id
    $cart = $resultCart->fetch_assoc();
    $cart_id = $cart['id'];
} else {
    // No cart, create a new one
    $sqlCreateCart = "INSERT INTO carts (user_id) VALUES (?)";
    $stmtCreateCart = $mysqli->prepare($sqlCreateCart);
    $stmtCreateCart->bind_param("i", $user_id);
    $stmtCreateCart->execute();
    $cart_id = $stmtCreateCart->insert_id;
    $_SESSION['cart_id'] = $cart_id; // Set cart_id in session
}

// Insert or update item in cart_items table
$sqlInsertItem = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE quantity = quantity + ?";
$stmtInsertItem = $mysqli->prepare($sqlInsertItem);
$stmtInsertItem->bind_param("iiii", $cart_id, $product_id, $quantity, $quantity);
$stmtInsertItem->execute();

// Redirect back to the product list with a success message
header("Location: product_list.php?id=" . $product_id . "&success=1");
exit;
?>
