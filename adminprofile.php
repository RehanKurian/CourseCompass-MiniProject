<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("ACCESS DENIED: You must be logged in as an admin to view this page.");
}

$admin_id = $_SESSION['user_id'];
$message = ''; // Initialize a message variable for user feedback

// --- SERVER-SIDE ACTION HANDLING  ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- Part 1: Handle standard form submission for updating user info ---
    // This action reloads the page and shows a message.
    if ($action === 'update_info') {
        $phone_no = $_POST['phone_no'];
        $bio = $_POST['bio'];
        
        $stmt_update = $conn->prepare("UPDATE Users SET phone_no = ?, bio = ? WHERE user_id = ?");
        $stmt_update->bind_param("ssi", $phone_no, $bio, $admin_id);

        if ($stmt_update->execute()) {
            $message = "Profile information updated successfully!";
        } else {
            $message = "Error updating profile: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
    // --- Part 2: Handle AJAX requests for image manipulation ---
    // These actions return a JSON response and stop the script.
    elseif ($action === 'upload' || $action === 'remove') {
        // Set the header to indicate a JSON response
        header('Content-Type: application/json');

        // UPLOAD ACTION
        if ($action == 'upload' && isset($_FILES['croppedImage'])) {
            $upload_dir = 'uploads/';
            $filename = 'user_' . $admin_id . '_' . time() . '.png';
            $filepath = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['croppedImage']['tmp_name'], $filepath)) {
                $stmt = $conn->prepare("SELECT profile_image FROM Users WHERE user_id = ?");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $old_image = $result['profile_image'] ?? null;
                $stmt->close();

                $stmt_update = $conn->prepare("UPDATE Users SET profile_image = ? WHERE user_id = ?");
                $stmt_update->bind_param("si", $filename, $admin_id);

                if ($stmt_update->execute()) {
                    if ($old_image && file_exists($upload_dir . $old_image)) {
                        unlink($upload_dir . $old_image);
                    }
                    echo json_encode(['success' => true, 'filePath' => $filepath]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Database update failed.']);
                }
                $stmt_update->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to save the file.']);
            }
        }
        // REMOVE ACTION
        elseif ($action == 'remove') {
            $stmt = $conn->prepare("SELECT profile_image FROM Users WHERE user_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $current_image = $result['profile_image'] ?? null;
            $stmt->close();

            $stmt_update = $conn->prepare("UPDATE Users SET profile_image = NULL WHERE user_id = ?");
            $stmt_update->bind_param("i", $admin_id);

            if ($stmt_update->execute()) {
                if ($current_image && file_exists('uploads/' . $current_image)) {
                    unlink('uploads/' . $current_image);
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed.']);
            }
            $stmt_update->close();
        }
        
        // --- CRITICAL FIX: Exit here ONLY for AJAX actions ---
        // This prevents the HTML from being sent with the JSON response.
        exit;
    }
}

// --- DATA FETCHING FOR PAGE DISPLAY ---
// This part runs on a normal page load OR after the 'update_info' action.
// It fetches the most current data from the database.
$stmt = $conn->prepare("SELECT fname, lname, email, profile_image, bio, phone_no FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$avatar_initials = '';
if (!empty($admin['fname']) && !empty($admin['lname'])) {
    $avatar_initials = strtoupper(substr($admin['fname'], 0, 1) . substr($admin['lname'], 0, 1));
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    
    <!-- Your existing CSS link -->
    <link rel="stylesheet" href="adminprofile.css">
    
    <!-- Your existing CDN links for Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" defer></script>
</head>
<body>

    <?php include 'adminsidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Admin Profile</h1>
        </div>

        <!-- NEW: This block will display the success/error message after an update -->
        <?php if ($message): ?>
            <div class="message" style="background-color: #2ecc71; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-content">
                <!-- The Profile Picture section remains unchanged -->
                <div class="profile-picture-section">
                    <div class="profile-picture-container">
                        <img src="<?php echo $admin['profile_image'] ? 'uploads/' . htmlspecialchars($admin['profile_image']) : ''; ?>" 
                             alt="Profile Picture" class="profile-picture" id="profile-pic-display" 
                             style="<?php echo $admin['profile_image'] ? '' : 'display: none;'; ?>">
                        <div class="initials-avatar" id="initials-avatar-display" 
                             style="<?php echo $admin['profile_image'] ? 'display: none;' : 'display: flex;'; ?>">
                            <span><?php echo htmlspecialchars($avatar_initials); ?></span>
                        </div>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*" style="display:none;">
                    <button class="btn btn-primary" onclick="document.getElementById('imageUpload').click();">Upload New Picture</button>
                    <button class="btn btn-danger" id="remove-pic-btn">Remove Picture</button>
                </div>

                <!-- UPDATED: This section is now a form to allow editing -->
                <form class="profile-details" method="POST" action="adminprofile.php">
                    <!-- This hidden field tells the server what action to perform -->
                    <input type="hidden" name="action" value="update_info">
                
                    <h2><?php echo htmlspecialchars($admin['fname'] . ' ' . $admin['lname']); ?></h2>
                    
                    <div class="detail-item">
                        <label>Email</label>
                        <p><?php echo htmlspecialchars($admin['email']); ?></p>
                    </div>
                    
                    <div class="detail-item">
                        <label for="phone_no">Phone Number</label>
                        <input type="text" id="phone_no" name="phone_no" class="form-control" value="<?php echo htmlspecialchars($admin['phone_no'] ?? ''); ?>" placeholder="Add a phone number">
                    </div>
                    
                    <div class="detail-item">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($admin['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="detail-item">
                         <button type="submit" class="btn btn-primary">Update Info</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div id="crop-modal" class="modal">
        <div class="modal-content">
            <h2>Crop Your Image</h2>
            <div> <img id="image-to-crop" src=""> </div> <br>
            <button id="crop-and-upload" class="btn btn-primary">Crop & Upload</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageUploadInput = document.getElementById('imageUpload');
        const profilePicDisplay = document.getElementById('profile-pic-display');
        const initialsAvatarDisplay = document.getElementById('initials-avatar-display');
        const cropModal = document.getElementById('crop-modal');
        const imageToCrop = document.getElementById('image-to-crop');
        const cropAndUploadBtn = document.getElementById('crop-and-upload');
        const removePicBtn = document.getElementById('remove-pic-btn');
        let cropper;

        imageUploadInput.addEventListener('change', (e) => {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = () => {
                    imageToCrop.src = reader.result;
                    cropModal.style.display = 'block';
                    if(cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, { aspectRatio: 1, viewMode: 1, background: false });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        cropAndUploadBtn.addEventListener('click', () => {
            cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                const formData = new FormData();
                formData.append('action', 'upload');
                formData.append('croppedImage', blob);

                fetch('adminprofile.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        profilePicDisplay.src = data.filePath + '?t=' + new Date().getTime();
                        profilePicDisplay.style.display = 'block';
                        initialsAvatarDisplay.style.display = 'none';
                        cropModal.style.display = 'none';
                    } else {
                        alert('Upload failed: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        removePicBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                const formData = new FormData();
                formData.append('action', 'remove');
                
                fetch('adminprofile.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        profilePicDisplay.src = '';
                        profilePicDisplay.style.display = 'none';
                        initialsAvatarDisplay.style.display = 'flex';
                    } else {
                        alert('Failed to remove picture: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
        
        window.onclick = function(event) {
            if (event.target == cropModal) {
                cropModal.style.display = "none";
            }
        }
    });
    </script>
</body>
</html>
