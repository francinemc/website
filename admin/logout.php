<?php
session_start(); // Start the session

// Destroy the session to log out the user
session_unset();
session_destroy();

// Redirect to index.php
header("Location: ../index.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="logout.css">
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <h2>Prime Tire's Dashboard</h2>
            <ul>
                <li><a href="add-product.html">Help</a></li>
                <li><a href="settings.html">Settings</a></li>
                <li><a href="logout.html">Logout</a></li>
            </ul>
        </nav>
        <div class="main-content">
            <h2>Logout</h2>
            <p>You have been logged out. Thank you for using Prime Tire's Dashboard.</p>
        </div>
    </div>
</body>
</html>
