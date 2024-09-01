<?php
session_start();
include 'db.php';  // Include the DB connection

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
    $product_ids = implode(",", array_keys($cart));
    $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);
    
    while($row = $result->fetch_assoc()) {
        $quantity = $cart[$row['id']];
        $subtotal = $quantity * $row['price'];
        echo "
        <tr>
            <td>{$row['name']}</td>
            <td>{$quantity}</td>
            <td>\${$row['price']}</td>
            <td>\${$subtotal}</td>
            <td><button onclick=\"removeFromCart({$row['id']})\">Remove</button></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>Your cart is empty</td></tr>";
}
?>
