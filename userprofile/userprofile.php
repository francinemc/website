<?php
session_start();
include('db.php');

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
// Check if there is a profile image set
$profileImage = 'images/defaultprofile.webp'; // Default image path
    
if ($user && !empty($user['profile_image'])) {
    // Create a temporary image URL to serve the image from the database
    $profileImage = 'data:image/jpeg;base64,' . base64_encode($user['profile_image']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userprofile.css">
    <title>User Profile</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <div class="sidebar">
            <div class="profile-pic">
                <img src="<?php echo $profileImage; ?>" alt="Profile Picture" id="profileImage">
                <input type="file" id="profilePicInput" name="profile_pic" style="display: none;" onchange="changeProfilePicture()">
                <button onclick="document.getElementById('profilePicInput').click()">Change Profile Picture</button>
            </div>
            <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
        </div>
        <div class="main-content">
            <!-- Button to open the dialog -->
            <button id="editButton" onclick="showDialog()">Edit Information</button>
            
            <!-- Dialog containing the form -->
            <dialog id="editDialog">
                <h2>Edit Your Information</h2>
                    <!-- <?php 
                        // $query = "SELECT ";
                    // $stmt = mysqli_prepare($conn, $query);
                

                ?> -->
                <form action="update_profile.php" method="post">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">

                    <label>Full Name:</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">

                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">

                    <label>Phone Number:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                    <label>Birthday:</label>
                    <input type="date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>">

                    <label>Gender:</label>
                    <select name="gender">
                        <option value="Male" <?php if($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>

                    <label>Address Line 1:</label>
                    <input type="text" name="address1" value="<?php echo htmlspecialchars($user['address1']); ?>">

                    <label>Address Line 2:</label>
                    <input type="text" name="address2" value="<?php echo htmlspecialchars($user['address2']); ?>">

                    <button type="submit">Save Changes</button>
                    <button type="button" onclick="closeDialog()">Cancel</button>
                </form>
            </dialog>

            <h2>Your Orders</h2>
            <div class="orders">
                <?php
                $order_query = "SELECT * FROM orders WHERE user_id = '$user_id'";
                $order_result = mysqli_query($conn, $order_query);

                if (mysqli_num_rows($order_result) > 0) {
                    while ($order = mysqli_fetch_assoc($order_result)) {
                        echo "<div class='order'>";
                        echo "<p>Order ID: " . htmlspecialchars($order['order_id']) . "</p>";
                        echo "<p>Status: " . htmlspecialchars($order['status']) . "</p>";
                        echo "<p>Date: " . htmlspecialchars($order['order_date']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No orders found.</p>";
                }
                ?>
            </div>

            <h2>Order History</h2>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h4>About Us</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
            <div class="footer-section links">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="#">Home</a></li> 
                    <li><a href="#">Products</a></li> 
                    <li><a href="#">About Us</a></li> 
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section legal">
                <h4>Legal</h4>
                <ul class="footer-links">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-section social-media">
                <h4>Follow Us</h4>
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Prime Tire. All rights reserved.</p>
        </div>
    </footer>

    <script src="userprofile.js"></script>
    <script>
        // Function to show the dialog
        function showDialog() {
            var dialog = document.getElementById('editDialog');
            dialog.showModal();
        }

        // Function to close the dialog
        function closeDialog() {
            var dialog = document.getElementById('editDialog');
            dialog.close();
        }
    </script>
</body>
</html>
