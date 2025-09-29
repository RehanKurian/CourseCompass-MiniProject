<?php
session_start();

// THIS PHP BLOCK HANDLES THE FORM SUBMISSION AND RELIES ON CLIENT-SIDE VALIDATION.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Include the database connection file.
    include 'db.php'; 

    // Retrieve form data directly without server-side validation.
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $rating = (int)($_POST['rating'] ?? 0);
    $message = $_POST['message'] ?? '';
    
    
    $stmt = $conn->prepare("INSERT INTO feedback (fname, lname, email, subject, rating, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $fname, $lname, $email, $subject, $rating, $message);

    if ($stmt->execute()) {
        $status = 'success';
    } else {
        $status = 'error';
    }

    $stmt->close();
    $conn->close();

    header("Location: contactus.php?status=" . $status);
    exit(); 
}
include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CourseCompass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="contactus.css">
</head>
<body> 
    <main class="main-content">
        <div class="page-header">
            <div class="container">
                <h1>Get in Touch</h1>
                <p>We'd love to hear from you! Reach out with any questions, feedback, or suggestions.</p>
            </div>
        </div>

        <div class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <!-- Column 1: Contact Information Details -->
                    <div class="contact-info">
                        <div>
                            <h2 class="section-title">Contact Information</h2>
                            <p class="section-subtitle">We're here to guide you. Get in touch through any of these channels.</p>
                        </div>
                        <div class="contact-item"><div class="contact-icon">📧</div><div class="contact-details"><h3>Email Us</h3><p><a href="mailto:support@coursecompass.com">support@coursecompass.com</a></p></div></div>
                        <div class="contact-item"><div class="contact-icon">📞</div><div class="contact-details"><h3>Call Us</h3><p><a href="tel:+1-800-COMPASS">+1 (800) COMPASS</a></p></div></div>
                        <div class="contact-item"><div class="contact-icon">🏢</div><div class="contact-details"><h3>Visit Us</h3><p>123 Learning Street<br>Knowledge City, KC 12345</p></div></div>
                        <div class="contact-item"><div class="contact-icon">⏰</div><div class="contact-details"><h3>Business Hours</h3><p>Mon - Fri: 9 AM - 6 PM<br>Saturday: 10 AM - 4 PM</p></div></div>
                    </div>

                    <!-- Column 2: User Feedback Form -->
                    <div class="feedback-form">
                        <?php
                        if (isset($_GET['status'])) {
                            if ($_GET['status'] == 'success') {
                                echo '<div class="feedback-message success">Thank you! Your feedback has been submitted.</div>';
                            } elseif ($_GET['status'] == 'error') {
                                echo '<div class="feedback-message error">A submission error occurred. Please try again.</div>';
                            }
                        }
                        ?>
                        <h3 class="form-title">Send Us Your Feedback</h3>
                        <form id="feedbackForm" method="POST" action="contactus.php" novalidate>
                            <div class="form-row">
                                <div class="form-group"><label class="form-label" for="fname">First Name *</label><input type="text" id="fname" name="fname" class="form-input" required><div class="error-message"></div></div>
                                <div class="form-group"><label class="form-label" for="lname">Last Name *</label><input type="text" id="lname" name="lname" class="form-input" required><div class="error-message"></div></div>
                            </div>
                            <div class="form-group"><label class="form-label" for="email">Email Address *</label><input type="email" id="email" name="email" class="form-input" required><div class="error-message"></div></div>
                            <div class="form-group"><label class="form-label" for="subject">Subject *</label><input type="text" id="subject" name="subject" class="form-input" required><div class="error-message"></div></div>
                            <input type="hidden" id="ratingInput" name="rating" value="0">
                            <div class="rating-container form-group">
                                <label class="rating-label">Rate Your Experience *</label>
                                <div class="star-rating" id="starRating">
                                    <span class="star" data-rating="1">★</span><span class="star" data-rating="2">★</span><span class="star" data-rating="3">★</span><span class="star" data-rating="4">★</span><span class="star" data-rating="5">★</span>
                                </div>
                                <div class="rating-text" id="ratingText">Click to rate</div>
                                <div class="error-message"></div>
                            </div>
                            <div class="form-group"><label class="form-label" for="message">Your Message *</label><textarea id="message" name="message" class="form-textarea" required></textarea><div class="error-message"></div></div>
                            <button type="submit" class="submit-btn">Send Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php include 'footer.php'; ?>
    <script>
    // Purpose: This script provides client-side form validation before submission.
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('feedbackForm');
        
        // --- Reusable function to display validation error messages ---
        const manageError = (fieldGroup, message) => {
            const errorDiv = fieldGroup.querySelector('.error-message');
            const inputElement = fieldGroup.querySelector('.form-input, .form-textarea');
            if (message) {
                errorDiv.textContent = message;
                if (inputElement) inputElement.classList.add('invalid');
            } else {
                errorDiv.textContent = '';
                if (inputElement) inputElement.classList.remove('invalid');
            }
        };

        // --- Validation function for Name fields (letters only) ---
        const validateNameField = (field) => {
            const fieldGroup = field.closest('.form-group');
            // Check 1: Ensure the field is not empty.
            if (field.value.trim() === '') {
                manageError(fieldGroup, 'This field is required.');
                return false;
            }
            // Check 2: Ensure the field contains only alphabetic characters.
            const nameRegex = /^[A-Za-z]+$/;
            if (!nameRegex.test(field.value)) {
                manageError(fieldGroup, 'Only letters are allowed.');
                return false;
            }
            // If all checks pass, clear the error message.
            manageError(fieldGroup, '');
            return true;
        };
        
        // --- Validation function for standard required text fields ---
        const validateTextField = (field) => {
            const fieldGroup = field.closest('.form-group');
            if (field.value.trim() === '') {
                manageError(fieldGroup, 'This field is required.');
                return false;
            }
            manageError(fieldGroup, '');
            return true;
        };
        
        // --- Validation function for the Email field ---
        const validateEmailField = () => {
            const emailField = document.getElementById('email');
            if (!validateTextField(emailField)) return false; // Check if empty first.
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                manageError(emailField.closest('.form-group'), 'Please enter a valid email address.');
                return false;
            }
            manageError(emailField.closest('.form-group'), '');
            return true;
        };

        // --- Attach "input" event listeners for live validation as the user types ---
        form.querySelectorAll('.form-input[required], .form-textarea[required]').forEach(field => {
            field.addEventListener('input', () => {
                if (field.id === 'fname' || field.id === 'lname') {
                    validateNameField(field); // Use name validation logic.
                } else if (field.id === 'email') {
                    validateEmailField(); // Use email validation logic.
                } else {
                    validateTextField(field); // Use basic required field logic.
                }
            });
        });
        
        // --- Star rating interaction logic ---
        const ratingInput = document.getElementById('ratingInput');
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('click', () => {
                ratingInput.value = star.dataset.rating; // Update hidden input
                stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.rating) <= ratingInput.value));
            });
        });

        // --- Final validation check on form "submit" event ---
        form.addEventListener('submit', function(e) {
            // Run all validation functions one last time to be sure.
            const isFNameValid = validateNameField(document.getElementById('fname'));
            const isLNameValid = validateNameField(document.getElementById('lname'));
            const isEmailValid = validateEmailField();
            const isSubjectValid = validateTextField(document.getElementById('subject'));
            const isMessageValid = validateTextField(document.getElementById('message'));
            
            // If any validation function returns false, prevent the form submission.
            if (!isFNameValid || !isLNameValid || !isEmailValid || !isSubjectValid || !isMessageValid) {
                e.preventDefault(); // Stop the form from being sent to the server.
            }
        });
    });
    </script>
</body>
</html>