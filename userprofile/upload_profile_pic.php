<?php
session_start();
include('db.php');

if (isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id']; // Ensure correct session variable name
    $file_tmp = $_FILES['profile_pic']['tmp_name'];
    $file_data = file_get_contents($file_tmp);
    
    // Prepare the SQL query to update the profile image
    $query = "UPDATE users SET profile_image = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, 'bi', $file_data, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating profile image']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing SQL statement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
}

mysqli_close($conn);
?>
