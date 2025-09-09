<?php
session_start();
include "db.php"; 
// Redirect to login page if the user is not logged in.
if (!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$alertMessage = ""; // Initialize an empty message for user feedback.
include "navbar.php"; 

// --- BACKEND LOGIC ---

//  Handle Remove Photo Request 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_photo'])) {
    // Get current photo path from DB
    $result = $conn->query("SELECT profile_image FROM users WHERE user_id='$user_id'");
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $photoPath = $row['profile_image'];
        // Delete the photo file if it exists
        if ($photoPath && file_exists($photoPath)) {
            unlink($photoPath);
        }
    }
    // Remove photo reference from DB
    $query = "UPDATE users SET profile_image=NULL WHERE user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $alertMessage = "Profile photo removed successfully!";
    } else {
        $alertMessage = "Failed to remove profile photo.";
    }
}

// 1. Handle the Cropped Image Upload from Cropper.js
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cropped_image'])) {
    $data = $_POST['cropped_image'];
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    $target_dir = "uploads/";
    $filename = time() . "_" . $user_id . ".png";
    $target_path = $target_dir . $filename;

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (file_put_contents($target_path, $data)) {
        $update_query = "UPDATE users SET profile_image=? WHERE user_id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $target_path, $user_id);
        
        if ($stmt->execute()) {
            $alertMessage = "Profile image updated successfully!";
        } else {
            $alertMessage = "Database update failed.";
        }
    } else {
        $alertMessage = "Failed to save the cropped image.";
    }
}

// 2. Handle Name and Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_name'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone_no'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $password = $_POST['password'];

    if (!empty($fname) && !empty($lname)) {
        if (!empty($password)) {
            $query = "UPDATE users SET fname=?, lname=?, bio=?, phone_no=?, password=? WHERE user_id=?";
            $stmt = $conn->prepare($query);
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $fname, $lname, $bio, $phone, $hashed, $user_id);
        } else {
            $query = "UPDATE users SET fname=?, lname=?, bio=?, phone_no=? WHERE user_id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssi", $fname, $lname, $bio, $phone, $user_id);
        }

        if ($stmt->execute()) {
            $alertMessage = "Profile updated successfully!";
            $_SESSION['user_name'] = $fname;
        } else {
            $alertMessage = "Failed to update profile.";
        }
    } else {
        $alertMessage = "First and Last name are required.";
    }
}

// Get the user's current info to display on the page.
$result = $conn->query("SELECT fname, lname, phone_no, bio, profile_image FROM users WHERE user_id='$user_id'");
if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $fname = $user['fname'];
    $lname = $user['lname'];
    $phone = $user['phone_no'] ?? '';
    $bio = $user['bio'] ?? '';
    $avatar_initial = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
    $profile_img = $user['profile_image'] ?? '';
} else {
    header("Location: signout.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - CourseCompass</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css"> <!-- Main stylesheet -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Cropper.js Library CSS from CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
</head>


<body>
    <section class="profile-section">
        <div class="container">
            <div class="profile-header">
                <h1>My Profile</h1>
            </div>

            <!-- Feedback Alert Box -->
            <?php if ($alertMessage): ?>
                <div class="alert-box">
                    <?= htmlspecialchars($alertMessage) ?>
                </div>
            <?php endif; ?>

            <!-- Avatar Section -->
            <div class="profile-avatar">
                <div class="avatar-circle">
                    <?php if ($profile_img): ?>
                        <img src="<?= htmlspecialchars($profile_img) ?>" alt="Avatar" class="avatar-img">
                    <?php else: ?>
                        <span class="avatar-initial"><?= $avatar_initial ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- This label acts as a styled button for changing the photo -->
                <label for="profile_image_input" class="avatar-btn">Change Photo</label>
                <input type="file" id="profile_image_input" accept="image/*" style="display: none;">
                
                <!-- Remove Photo Button: Only show if a photo exists -->
                <?php if ($profile_img): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="remove_photo" value="1">
                        <button type="submit" class="avatar-btn">Remove Photo</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- User Info Form -->
            <div class="profile-info">
                <form method="POST" class="info-card">
                    <h2>Your Profile</h2>
                    <div class="info-grid">

                        <div class="info-item">
                            <label class="info-label" for="fname">First Name</label>
                            <div class="info-value">
                                <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($fname) ?>" required>
                            </div>
                        </div>

                        <div class="info-item">
                            <label class="info-label" for="lname">Last Name</label>
                            <div class="info-value">
                                <input type="text" id="lname" name="lname" value="<?= htmlspecialchars($lname) ?>" required>
                            </div>
                        </div>

                        <div class="info-item">
                            <label class="info-label" for="phone_no">Phone Number</label>
                            <div class="info-value">
                                <input type="text" id="phone_no" name="phone_no" value="<?= htmlspecialchars($phone ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="info-item">
                            <label class="info-label" for="bio">Bio</label>
                            <div class="info-value">
                                <textarea id="bio" name="bio" maxlength="300" rows="4"><?= htmlspecialchars($bio ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Password field with toggle button -->
                        <div class="password-wrapper">
                            <label class="info-label" for="password">New Password</label>
                            <div class="info-value">
                                <input type="password" name="password" id="password" placeholder="New Password">
                                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_name" class="btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Modal for cropping the profile photo before upload -->
    <div id="cropModal" class="modal">
        <div class="modal-content">
            <h2>Crop Your Image</h2>
            <div class="img-container">
                <img id="imageToCrop" src="" alt="Crop Preview">
            </div>
            <button id="cropButton" class="btn-primary" style="width: auto;">Crop & Upload</button>
        </div>
    </div>
    
    <footer class="footer">
        <!-- Your footer HTML here -->
    </footer>

    <!-- Cropper.js Library JS from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <!-- Inline script for custom interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ----- Cropper logic for profile photo -----
            const fileInput = document.getElementById('profile_image_input');
            const modal = document.getElementById('cropModal');
            const image = document.getElementById('imageToCrop');
            const cropButton = document.getElementById('cropButton');
            let cropper;

            // When a file is selected, open Cropper.js in a modal
            fileInput.addEventListener('change', function (e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        image.src = event.target.result;
                        modal.style.display = 'flex';
                        cropper = new Cropper(image, {
                            aspectRatio: 1,
                            viewMode: 1,
                            background: false
                        });
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            // Crop and upload the selected image
            cropButton.addEventListener('click', function () {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                });
                const croppedImageData = canvas.toDataURL('image/png');
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'profile.php';
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'cropped_image';
                hiddenInput.value = croppedImageData;
                form.appendChild(hiddenInput);
                document.body.appendChild(form);
                form.submit();
            });

            // Close crop modal on outside click and destroy cropper instance
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    if (cropper) {
                        cropper.destroy();
                    }
                }
            });

            // ----- Password visibility toggle logic -----
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    // Show "eye-off" icon
                    this.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
                } else {
                    // Show normal "eye" icon
                    this.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
                }
            });
        });
    </script>
</body>
</html>
