<?php
// Start session to use session variables
session_start();

// Include the database connection file
include('db.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user ID from the hidden input field
    $user_id = $_POST['user_id'];

    // Get the form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address1 = mysqli_real_escape_string($conn, $_POST['address1']);
    $address2 = mysqli_real_escape_string($conn, $_POST['address2']);

    // Create an update query
    $query = "
        UPDATE users 
        SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone',
            birth_date = '$birth_date',
            gender = '$gender',
            address1 = '$address1',
            address2 = '$address2'
        WHERE id = '$user_id'
    ";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Redirect back to the profile page with a success message
        header('Location: userprofile.php?update=success');
        exit;
    } else {
        // Redirect back to the profile page with an error message
        header('Location: userprofile.php?update=error');
        exit;
    }
}
?>
