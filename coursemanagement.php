<?php
session_start();
include 'db.php'; 

// --- Initialize variables for the edit mode ---
$edit_mode = false;
$course_to_edit = null;
$duration_number = '';
$duration_unit = 'Weeks'; 

// --- Check if the page is in edit mode from a GET request ---
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_mode = true;
    $course_id_to_edit = $_GET['id'];

    // Fetch the course details
    $stmt = $conn->prepare("SELECT * FROM Courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_to_edit = $result->fetch_assoc();

    if ($course_to_edit && !empty($course_to_edit['duration'])) {
        // Use explode to split the string by the first space. Limit to 2 parts.
        $parts = explode(' ', $course_to_edit['duration'], 2);
        if (count($parts) === 2) {
            $duration_number = $parts[0]; // The numeric part
            $duration_unit = $parts[1];   // The unit part (e.g., 'Weeks')
        } else {
            // If the format is unexpected, just use the whole string as the number
            $duration_number = $course_to_edit['duration'];
        }
    }
    $stmt->close();
}

// --- PHP LOGIC TO HANDLE FORM SUBMISSIONS ---
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // Combine duration number and unit into a single string 
    $combined_duration = '';
    if (isset($_POST['duration_number']) && isset($_POST['duration_unit']) && !empty($_POST['duration_number'])) {
        $combined_duration = trim($_POST['duration_number']) . ' ' . trim($_POST['duration_unit']);
    }

    // --- ADD course logic ---
    if ($_POST['action'] == 'add_course') {
        $stmt = $conn->prepare("INSERT INTO Courses (title, platform, duration, level, category, url, description, rating, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssid", $_POST['title'], $_POST['platform'], $combined_duration, $_POST['level'], $_POST['category'], $_POST['url'], $_POST['description'], $_POST['rating'], $_POST['price']);

        if ($stmt->execute()) {
            $message = "Course added successfully!";
        } else {
            $message = "Error adding course: " . $stmt->error;
        }
        $stmt->close();
    }

    // --- UPDATE course logic ---
    if ($_POST['action'] == 'update_course') {
        $stmt = $conn->prepare("UPDATE Courses SET title=?, platform=?, duration=?, level=?, category=?, url=?, description=?, rating=?, price=? WHERE course_id=?");
        $stmt->bind_param("sssssssidi", $_POST['title'], $_POST['platform'], $combined_duration, $_POST['level'], $_POST['category'], $_POST['url'], $_POST['description'], $_POST['rating'], $_POST['price'], $_POST['course_id']);

        if ($stmt->execute()) {
            $message = "Course updated successfully!";
        } else {
            $message = "Error updating course: " . $stmt->error;
        }
        $stmt->close();
        $edit_mode = false;
        header("Location: coursemanagement.php"); 
        exit();
    }

    // --- DELETE course logic ---
    if ($_POST['action'] == 'delete_course') {
        $course_id = $_POST['course_id'];
        $stmt = $conn->prepare("DELETE FROM Courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        if ($stmt->execute()) {
            $message = "Course deleted successfully!";
        } else {
            $message = "Error deleting course: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <style>
        body {
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #333;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .header h1 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #e9ecef;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
            display: inline-block;
        }

        .btn-primary,
        .btn-danger,
        .btn-secondary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .form-grid div {
            display: flex;
            flex-direction: column;
        }

        .form-grid label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-grid input,
        .form-grid select,
        .form-grid textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            color: #fff;
            background-color: #2ecc71;
            margin-bottom: 20px;
        }

        .action-buttons,
        .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .validation-error {
            color: #e74c3c;
            font-size: 0.85em;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <?php include 'adminsidebar.php'; ?>

    <div class="content">
        <h1>Course Management</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div><?php endif; ?>

        <div class="form-container">
            <h3><?php echo $edit_mode ? 'Edit Course' : 'Add New Course'; ?></h3>
            <form id="courseForm" action="coursemanagement.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update_course' : 'add_course'; ?>">
                <?php if ($edit_mode): ?><input type="hidden" name="course_id"
                        value="<?php echo htmlspecialchars($course_to_edit['course_id']); ?>"><?php endif; ?>

                <div class="form-grid">
                    <div><label>Title</label><input type="text" name="title"
                            value="<?php echo htmlspecialchars($course_to_edit['title'] ?? ''); ?>" required></div>
                    <div>
                        <label>Platform</label>
                        <input type="text" name="platform" id="platform" value="<?php echo htmlspecialchars($course_to_edit['platform'] ?? ''); ?>">
                        <small id="platform_error" class="validation-error"></small>
                    </div>

                    <!-- UPDATED: Duration fields with correct names and pre-filled values -->
                    <div>
                        <label>Duration</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="duration_number" id="duration_number"
                                value="<?php echo htmlspecialchars($duration_number); ?>" style="flex: 2;">
                            <small id="duration_error" class="validation-error" style="margin-top:6px;"></small>
                            <select name="duration_unit" style="flex: 1;">
                                <option value="Days" <?php echo ($duration_unit == 'Days') ? 'selected' : ''; ?>>Days
                                </option>
                                <option value="Weeks" <?php echo ($duration_unit == 'Weeks') ? 'selected' : ''; ?>>Weeks
                                </option>
                                <option value="Months" <?php echo ($duration_unit == 'Months') ? 'selected' : ''; ?>>
                                    Months</option>
                                <option value="Year" <?php echo ($duration_unit == 'Year') ? 'selected' : ''; ?>>Year
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label>Level</label>
                        <select name="level" required>
                            <option value="Beginner" <?php echo ($course_to_edit['level'] ?? '') == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="Intermediate" <?php echo ($course_to_edit['level'] ?? '') == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="Advanced" <?php echo ($course_to_edit['level'] ?? '') == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>

                    <div>
                        <label>Category</label>
                        <input type="text" name="category" id="category" value="<?php echo htmlspecialchars($course_to_edit['category'] ?? ''); ?>">
                        <small id="category_error" class="validation-error"></small>
                    </div>
                    <div><label>URL</label><input type="text" name="url"
                            value="<?php echo htmlspecialchars($course_to_edit['url'] ?? ''); ?>" id="url"></div>
                        <small id="url_error" class="validation-error"></small>
                    <div>
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" id="price"
                            value="<?php echo htmlspecialchars($course_to_edit['price'] ?? ''); ?>">
                        <small id="price_error" class="validation-error"></small>
                    </div>
                    <div>
                        <label>Rating (0.0-5.0)</label>
                        <input type="number" step="0.1" name="rating" id="rating"
                            value="<?php echo htmlspecialchars($course_to_edit['rating'] ?? ''); ?>">
                        <small id="rating_error" class="validation-error"></small>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label>Description</label>
                        <textarea name="description" id="description"
                            rows="3"><?php echo htmlspecialchars($course_to_edit['description'] ?? ''); ?></textarea>
                        <small id="description_error" class="validation-error"></small>
                    </div>
                </div>
                <br>
                <div class="form-actions">
                    <button type="submit"
                        class="btn btn-primary"><?php echo $edit_mode ? 'Update Course' : 'Add Course'; ?></button>
                    <?php if ($edit_mode): ?><a href="coursemanagement.php" class="btn btn-secondary">Cancel
                             Edit</a><?php endif; ?>
                </div>
            </form>
        </div>

        <h3 style="margin-top: 30px;">Existing Courses</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT course_id, title, duration, category, price FROM Courses ORDER BY course_id");
                while ($course = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $course['course_id']; ?></td>
                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                        <td><?php echo htmlspecialchars($course['duration']); ?></td>
                        <td><?php echo htmlspecialchars($course['category']); ?></td>
                        <td>$<?php echo number_format($course['price'], 2); ?></td>
                        <td class="action-buttons">
                            <a href="coursemanagement.php?action=edit&id=<?php echo $course['course_id']; ?>"
                                class="btn btn-secondary">Edit</a>
                            <form action="coursemanagement.php" method="POST" onsubmit="return confirm('Are you sure?');"
                                style="margin:0;">
                                <input type="hidden" name="action" value="delete_course">
                                <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Purpose: Provide immediate feedback to the user for specific fields.
        const form = document.getElementById('courseForm');
        const platformInput = document.getElementById('platform');
        const durationInput = document.getElementById('duration_number');
        const categoryInput = document.getElementById('category');
        const urlInput = document.getElementById('url');
        const priceInput = document.getElementById('price');
        const ratingInput = document.getElementById('rating');
        const descriptionInput = document.getElementById('description');

        const platformError = document.getElementById('platform_error');
        const durationError = document.getElementById('duration_error');
        const categoryError = document.getElementById('category_error');
        const urlError = document.getElementById('url_error');
        const priceError = document.getElementById('price_error');
        const ratingError = document.getElementById('rating_error');
        const descriptionError = document.getElementById('description_error');

        const lettersOnly = /^[A-Za-z\s]+$/;
        const urlRegex = /^(https?:\/\/)?([A-Za-z0-9-]+\.)+[A-Za-z]{2,}(\/\S*)?$/i;

        // Live validators
        platformInput && platformInput.addEventListener('input', function () {
            if (!this.value.trim()) {
                platformError.textContent = 'Platform is required.';
            } else if (!lettersOnly.test(this.value.trim())) {
                platformError.textContent = 'Platform should contain letters only.';
            } else {
                platformError.textContent = '';
            }
        });

        durationInput && durationInput.addEventListener('input', function () {
            if (this.value === '' || this.value === null) {
                durationError.textContent = 'Duration is required.';
            } else if (!/^\d+$/.test(String(this.value))) {
                durationError.textContent = 'Duration must be a whole number.';
            } else {
                durationError.textContent = '';
            }
        });

        categoryInput && categoryInput.addEventListener('input', function () {
            if (!this.value.trim()) {
                categoryError.textContent = 'Category is required.';
            } else if (!lettersOnly.test(this.value.trim())) {
                categoryError.textContent = 'Category should contain letters only.';
            } else {
                categoryError.textContent = '';
            }
        });

        urlInput && urlInput.addEventListener('input', function () {
            if (!this.value.trim()) {
                urlError.textContent = '';
                return;
            }
            if (!urlRegex.test(this.value.trim())) {
                urlError.textContent = 'Please enter a valid URL (e.g. https://example.com).';
            } else {
                urlError.textContent = '';
            }
        });

        priceInput && priceInput.addEventListener('input', function () {
            if (this.value === '' || this.value === null) {
                priceError.textContent = '';
                return;
            }
            if (isNaN(parseFloat(this.value))) {
                priceError.textContent = 'Price must be a number.';
            } else if (parseFloat(this.value) < 0) {
                priceError.textContent = 'Price cannot be negative.';
            } else {
                priceError.textContent = '';
            }
        });

        ratingInput && ratingInput.addEventListener('input', function () {
            if (this.value === '' || this.value === null) {
                ratingError.textContent = '';
                return;
            }
            const val = parseFloat(this.value);
            if (isNaN(val)) {
                ratingError.textContent = 'Rating must be numeric.';
            } else if (val < 0.0 || val > 5.0) {
                ratingError.textContent = 'Rating must be between 0.0 and 5.0.';
            } else {
                ratingError.textContent = '';
            }
        });

        descriptionInput && descriptionInput.addEventListener('input', function () {
            const maxLength = 500;
            if (this.value.length > maxLength) {
                descriptionError.textContent = `Description cannot exceed ${maxLength} characters.`;
            } else {
                descriptionError.textContent = '';
            }
        });

        // Final validation before submit
        form && form.addEventListener('submit', function (e) {
            let valid = true;

            // Trigger each validator to populate errors
            platformInput && platformInput.dispatchEvent(new Event('input'));
            durationInput && durationInput.dispatchEvent(new Event('input'));
            categoryInput && categoryInput.dispatchEvent(new Event('input'));
            urlInput && urlInput.dispatchEvent(new Event('input'));
            priceInput && priceInput.dispatchEvent(new Event('input'));
            ratingInput && ratingInput.dispatchEvent(new Event('input'));
            descriptionInput && descriptionInput.dispatchEvent(new Event('input'));

            const errorElements = [platformError, durationError, categoryError, urlError, priceError, ratingError, descriptionError];
            errorElements.forEach(el => { if (el && el.textContent) valid = false; });

            if (!valid) {
                e.preventDefault();
                // Optionally focus first error field
                const firstErrorField = [platformInput, durationInput, categoryInput, urlInput, priceInput, ratingInput, descriptionInput].find((fld, idx) => {
                    const err = errorElements[idx];
                    return fld && err && err.textContent;
                });
                if (firstErrorField) firstErrorField.focus();
            }
        });
    </script>

</body>

</html>
<?php
$conn->close();
?>