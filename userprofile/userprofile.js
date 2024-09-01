// Function to change the profile picture
function changeProfilePicture() {
    var input = document.getElementById('profilePicInput');
    var file = input.files[0];

    if (file) {
        var formData = new FormData();
        formData.append('profile_pic', file);

        fetch('upload_profile_pic.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Create a URL for the uploaded image
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Update the profile image source with the new image
                    document.getElementById('profileImage').src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                alert(data.message || 'Error updating profile picture.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while uploading the image.');
        });
    } else {
        alert('Please select a file.');
    }
}
