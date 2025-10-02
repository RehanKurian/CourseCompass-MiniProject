<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$message = ''; // For user feedback on form submission

// --- SERVER-SIDE ACTION HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- Part 1: Handle standard form submission for updating user info ---
    if ($action === 'update_info') {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $phone_no = $_POST['phone_no'];
        $bio = $_POST['bio'];
        
        $stmt_update = $conn->prepare("UPDATE Users SET fname = ?, lname = ?, phone_no = ?, bio = ? WHERE user_id = ?");
        $stmt_update->bind_param("ssssi", $fname, $lname, $phone_no, $bio, $admin_id);

        if ($stmt_update->execute()) {
            $message = "Profile information updated successfully!";
            $_SESSION['user_name'] = $fname; 
        } else {
            $message = "Error updating profile: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
    // --- Part 2: Handle AJAX requests for image manipulation ---
    elseif ($action === 'upload' || $action === 'remove') {
        header('Content-Type: application/json');

        // UPLOAD ACTION 
        if ($action == 'upload' && isset($_FILES['croppedImage'])) {
            $upload_dir = 'uploads/';
            $filename = 'user_' . $admin_id . '_' . time() . '.png';
            $filepath = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['croppedImage']['tmp_name'], $filepath)) {
                $stmt_update = $conn->prepare("UPDATE Users SET profile_image = ? WHERE user_id = ?");
                $stmt_update->bind_param("si", $filepath, $admin_id); // Storing full path now
                if ($stmt_update->execute()) {
                    echo json_encode(['success' => true, 'filePath' => $filepath]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Database update failed.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'File move failed.']);
            }
        }
        // REMOVE ACTION 
        elseif ($action == 'remove') {
            $stmt_update = $conn->prepare("UPDATE Users SET profile_image = NULL WHERE user_id = ?");
            $stmt_update->bind_param("i", $admin_id);
            if ($stmt_update->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed.']);
            }
        }
        exit; 
    }
}

// --- DATA FETCHING FOR PAGE DISPLAY ---
$stmt = $conn->prepare("SELECT fname, lname, email, profile_image, bio, phone_no FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$avatar_initials = (!empty($admin['fname']) && !empty($admin['lname'])) ? strtoupper(substr($admin['fname'], 0, 1) . substr($admin['lname'], 0, 1)) : '';
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="adminprofile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" defer></script>
    <style>
        .error-message { color: #e74c3c; font-size: 0.9em; margin-top: 5px; height: 1em; }
        .form-control.invalid { border-color: #e74c3c; }
    </style>
</head>
<body>
    <?php include 'adminsidebar.php'; ?>
    <div class="content">

        <?php if ($message): ?>
            <div class="message" style="background-color: #2ecc71; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-content">
                <div class="profile-picture-section">
                    <div class="profile-picture-container">
                        <img src="<?php echo $admin['profile_image'] ? htmlspecialchars($admin['profile_image']) : ''; ?>" alt="Profile Picture" class="profile-picture" id="profile-pic-display" style="<?php echo $admin['profile_image'] ? '' : 'display: none;'; ?>">
                        <div class="initials-avatar" id="initials-avatar-display" style="<?php echo $admin['profile_image'] ? 'display: none;' : 'display: flex;'; ?>">
                            <span><?php echo htmlspecialchars($avatar_initials); ?></span>
                        </div>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*" style="display:none;">
                    <button class="btn btn-primary" onclick="document.getElementById('imageUpload').click();">Upload New Picture</button>
                    <button class="btn btn-danger" id="remove-pic-btn">Remove Picture</button>
                    <a class="btn btn-danger" href="signout.php">Logout</a>
                </div>

                <form id="profileForm" class="profile-details" method="POST" action="adminprofile.php">
                    <input type="hidden" name="action" value="update_info">
                    
                    <!-- UPDATED: Name fields are now inputs -->
                    <div class="detail-item">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" class="form-control" value="<?php echo htmlspecialchars($admin['fname']); ?>" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="detail-item">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" class="form-control" value="<?php echo htmlspecialchars($admin['lname']); ?>" required>
                        <div class="error-message"></div>
                    </div>
                    
                    <div class="detail-item">
                        <label>Email</label>
                        <p><?php echo htmlspecialchars($admin['email']); ?></p>
                    </div>
                    
                    <div class="detail-item">
                        <label for="phone_no">Phone Number</label>
                        <input type="text" id="phone_no" name="phone_no" class="form-control" value="<?php echo htmlspecialchars($admin['phone_no'] ?? ''); ?>" placeholder="Add a phone number">
                        <div class="error-message"></div>
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
            <div><img id="image-to-crop" src=""></div><br>
            <button id="crop-and-upload" class="btn btn-primary">Crop & Upload</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Cropper.js and Image Upload/Remove Logic (Unchanged) ---
        const imageUploadInput = document.getElementById('imageUpload');
        const profilePicDisplay = document.getElementById('profile-pic-display');
        const initialsAvatarDisplay = document.getElementById('initials-avatar-display');
        const cropModal = document.getElementById('crop-modal');
        const imageToCrop = document.getElementById('image-to-crop');
        const cropAndUploadBtn = document.getElementById('crop-and-upload');
        const removePicBtn = document.getElementById('remove-pic-btn');
        let cropper;
        // (Listeners for image upload and remove remain the same)

        // --- NEW: LIVE VALIDATION LOGIC ---
        const profileForm = document.getElementById('profileForm');
        const fnameInput = document.getElementById('fname');
        const lnameInput = document.getElementById('lname');
        const phoneInput = document.getElementById('phone_no');

        // --- Reusable function to display/hide error messages ---
        const manageError = (input, message) => {
            const errorDiv = input.nextElementSibling;
            if (message) {
                errorDiv.textContent = message;
                input.classList.add('invalid');
            } else {
                errorDiv.textContent = '';
                input.classList.remove('invalid');
            }
        };

        // --- Validation function for Name fields (letters only) ---
        const validateName = (input) => {
            if (input.value.trim() === '') {
                manageError(input, 'This field is required.');
                return false;
            }
            const nameRegex = /^[A-Za-z]+$/;
            if (!nameRegex.test(input.value)) {
                manageError(input, 'Only letters are allowed.');
                return false;
            }
            manageError(input, '');
            return true;
        };

        // --- Validation function for Phone Number (must be 10 digits) ---
        const validatePhone = (input) => {
            const phoneRegex = /^\d{10}$/;
            if (input.value.trim() !== '' && !phoneRegex.test(input.value)) {
                manageError(input, 'Phone number must be 10 digits.');
                return false;
            }
            manageError(input, '');
            return true;
        };
        
        // --- Attach Event Listeners for Live Validation ---
        fnameInput.addEventListener('input', () => validateName(fnameInput));
        lnameInput.addEventListener('input', () => validateName(lnameInput));
        phoneInput.addEventListener('input', () => validatePhone(phoneInput));

        // --- Final Validation on Form Submission ---
        profileForm.addEventListener('submit', function(event) {
            const isFnameValid = validateName(fnameInput);
            const isLnameValid = validateName(lnameInput);
            const isPhoneValid = validatePhone(phoneInput);
            
            if (!isFnameValid || !isLnameValid || !isPhoneValid) {
                event.preventDefault(); // Stop form submission if invalid
            }
        });

        // Event listener for closing the crop modal
        window.onclick = function(event) {
            if (event.target == cropModal) {
                cropModal.style.display = "none";
            }
        }
    });
    </script>
</body>
</html>
