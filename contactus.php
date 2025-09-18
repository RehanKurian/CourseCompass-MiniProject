<?php
session_start();
$home_link = (isset($_SESSION['email']) && isset($_SESSION['role'])) ? 'home.php' : 'index.php';
// THIS ENTIRE BLOCK HANDLES THE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include 'db.php';

    // Retrieve and sanitize form data
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    // Server-side validation
    if (!empty($fname) && !empty($lname) && filter_var($email, FILTER_VALIDATE_EMAIL) && $rating > 0) {
        
        $stmt = $conn->prepare("INSERT INTO feedback (fname, lname, email, subject, rating, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $fname, $lname, $email, $subject, $rating, $message);

        if ($stmt->execute()) {
            $status = 'success';
        } else {
            $status = 'error';
        }

        // CORRECTED: Close the statement and connection HERE, before the redirect.
        $stmt->close();
        $conn->close();
        
        // Redirect AFTER closing resources
        header("Location: contactus.php?status=" . $status);
        exit(); // Stop the script.

    } else {
        // If validation fails, close the connection and redirect.
        $conn->close();
        header("Location: contactus.php?status=error");
        exit();
    }
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
                    <!-- Contact Information -->
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

                    <!-- Feedback Form -->
                    <div class="feedback-form">
                        <?php
                        // This block displays the status message after a redirect
                        if (isset($_GET['status'])) {
                            if ($_GET['status'] == 'success') {
                                echo '<div class="feedback-message success">Thank you! Your feedback has been submitted.</div>';
                            } elseif ($_GET['status'] == 'error') {
                                echo '<div class="feedback-message error">Sorry, something went wrong. Please check your input and try again.</div>';
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

    
        <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('feedbackForm');
        const emailField = document.getElementById('email');
        const ratingInput = document.getElementById('ratingInput');
        const ratingGroup = document.querySelector('.rating-container.form-group');
        const stars = ratingGroup.querySelectorAll('.star');
        const ratingText = document.getElementById('ratingText');

        // --- REUSABLE ERROR HANDLING FUNCTION (UNCHANGED) ---
        const manageError = (fieldGroup, message) => {
            const errorDiv = fieldGroup.querySelector('.error-message');
            const inputElement = fieldGroup.querySelector('.form-input, .form-textarea, .star-rating');
            
            if (message) {
                errorDiv.textContent = message;
                if (inputElement) inputElement.classList.add('invalid');
                else fieldGroup.classList.add('invalid'); // Fallback for rating container
            } else {
                errorDiv.textContent = '';
                if (inputElement) inputElement.classList.remove('invalid');
                else fieldGroup.classList.remove('invalid');
            }
        };

        // --- NEW: VALIDATION LOGIC REFACTORED INTO FUNCTIONS ---
        const validateField = (field) => {
            const fieldGroup = field.closest('.form-group');
            if (field.value.trim() === '') {
                manageError(fieldGroup, 'This field is required.');
                return false;
            }
            manageError(fieldGroup, ''); // Clear error if valid
            return true;
        };

        const validateEmail = () => {
            const fieldGroup = emailField.closest('.form-group');
            if (!validateField(emailField)) return false; // Check if empty first

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                manageError(fieldGroup, 'Please enter a valid email address.');
                return false;
            }
            manageError(fieldGroup, ''); // Clear error if valid
            return true;
        };

        const validateRating = () => {
            if (ratingInput.value === '0') {
                manageError(ratingGroup, 'Please select a star rating.');
                return false;
            }
            manageError(ratingGroup, '');
            return true;
        };

        // --- STAR RATING LOGIC (UNCHANGED, BUT NOW INCLUDES VALIDATION) ---
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const ratingValue = star.dataset.rating;
                ratingInput.value = ratingValue;
                stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.rating) <= ratingValue));
                ratingText.textContent = `You rated ${ratingValue} star(s)`;
                validateRating(); // Validate immediately on click
            });
        });

        // --- NEW: ADD LIVE VALIDATION LISTENERS ---
        form.querySelectorAll('.form-input[required], .form-textarea[required]').forEach(field => {
            field.addEventListener('input', () => {
                if (field.id === 'email') {
                    validateEmail();
                } else {
                    validateField(field);
                }
            });
        });

        // --- MODIFIED: SUBMIT HANDLER NOW USES VALIDATION FUNCTIONS FOR A FINAL CHECK ---
        form.addEventListener('submit', function(e) {
            const isNameValid = validateField(document.getElementById('fname'));
            const isLNameValid = validateField(document.getElementById('lname'));
            const isEmailValid = validateEmail();
            const isSubjectValid = validateField(document.getElementById('subject'));
            const isMessageValid = validateField(document.getElementById('message'));
            const isRatingValid = validateRating();

            // Check if the entire form is valid
            if (!isNameValid || !isLNameValid || !isEmailValid || !isSubjectValid || !isMessageValid || !isRatingValid) {
                e.preventDefault(); // Stop form submission if any validation fails
            }
        });
    });
    </script>
</body>
</html>
