<?php

// Get the absolute path for uploads
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/rentacar/uploads/profile_images/';
$web_path = '/rentacar/uploads/profile_images/';

// Create upload directory if it doesn't exist
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
    chmod($upload_dir, 0755);
}

// Handle profile image upload
if ($_POST && isset($_POST["upload_profile_image"])) {
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["size"] > 0) {
        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . '_profile.' . $file_extension;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $user->profile_image = $web_path . $file_name;
            if ($user->updateProfile()) {
                $message = '<div class="alert alert-success">Profile image uploaded successfully!</div>';
                // Refresh user data
                $user->getUserById($user->id);
            } else {
                $message = '<div class="alert alert-danger">Failed to update profile image in database.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Failed to upload image. Check permissions.</div>';
        }
    }
}

?>
