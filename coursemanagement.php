<?php
session_start();
include 'db.php'; // Your database connection file

// --- Initialize variables for the edit mode ---
$edit_mode = false;
$course_to_edit = null;
$duration_number = '';
$duration_unit = 'Weeks'; // Default unit

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

    // NEW: Parse the 'duration' string to pre-fill the form
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

    // --- Combine duration number and unit into a single string ---
    // This happens for both 'add' and 'update' actions.
    $combined_duration = '';
    if (isset($_POST['duration_number']) && isset($_POST['duration_unit']) && !empty($_POST['duration_number'])) {
        $combined_duration = trim($_POST['duration_number']) . ' ' . trim($_POST['duration_unit']);
    }

    // --- ADD course logic ---
    if ($_POST['action'] == 'add_course') {
        // The SQL query uses the single 'duration' column
        $stmt = $conn->prepare("INSERT INTO Courses (title, platform, duration, level, category, url, description, rating, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // The bind_param string is "sssssssid", reflecting the VARCHAR duration.
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
        // The bind_param string is updated for the single duration string and the course_id.
        $stmt->bind_param("sssssssidi", $_POST['title'], $_POST['platform'], $combined_duration, $_POST['level'], $_POST['category'], $_POST['url'], $_POST['description'], $_POST['rating'], $_POST['price'], $_POST['course_id']);

        if ($stmt->execute()) {
            $message = "Course updated successfully!";
        } else {
            $message = "Error updating course: " . $stmt->error;
        }
        $stmt->close();
        $edit_mode = false;
        header("Location: coursemanagement.php"); // Redirect to clear the form and URL
        exit();
    }

    // --- DELETE course logic ---
    if ($_POST['action'] == 'delete_course') {
        // This part remains unchanged
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
        /* CSS styles remain the same as the previous version */
        body {
            font-family: "Segoe UI", sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #667eea;
            display: flex;
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

    <div class="main-content">
        <h1>Course Management</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div><?php endif; ?>

        <div class="form-container">
            <h3><?php echo $edit_mode ? 'Edit Course' : 'Add New Course'; ?></h3>
            <form action="coursemanagement.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update_course' : 'add_course'; ?>">
                <?php if ($edit_mode): ?><input type="hidden" name="course_id"
                        value="<?php echo htmlspecialchars($course_to_edit['course_id']); ?>"><?php endif; ?>

                <div class="form-grid">
                    <div><label>Title</label><input type="text" name="title"
                            value="<?php echo htmlspecialchars($course_to_edit['title'] ?? ''); ?>" required></div>
                    <div><label>Platform</label><input type="text" name="platform"
                            value="<?php echo htmlspecialchars($course_to_edit['platform'] ?? ''); ?>"></div>

                    <!-- UPDATED: Duration fields with correct names and pre-filled values -->
                    <div>
                        <label>Duration</label>
                        <div style="display: flex; gap: 10px;">
                            <!-- Name is 'duration_number' -->
                            <input type="number" name="duration_number" placeholder="e.g., 6"
                                value="<?php echo htmlspecialchars($duration_number); ?>" style="flex: 2;">
                            <!-- Name is 'duration_unit' -->
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

                    <div><label>Category</label><input type="text" name="category"
                            value="<?php echo htmlspecialchars($course_to_edit['category'] ?? ''); ?>"></div>
                    <div><label>URL</label><input type="text" name="url"
                            value="<?php echo htmlspecialchars($course_to_edit['url'] ?? ''); ?>"></div>
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
            <!-- Table displays the combined duration string directly from the DB -->
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
                // UPDATED: Fetched 'duration' to display in the table
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
        const priceInput = document.getElementById('price');
        const ratingInput = document.getElementById('rating');
        const descriptionInput = document.getElementById('description');
        const priceError = document.getElementById('price_error');
        const ratingError = document.getElementById('rating_error');
        const descriptionError = document.getElementById('description_error');

        // --- Live validation for the Price field ---
        priceInput.addEventListener('input', function () {
            if (this.value < 0) {
                priceError.textContent = 'Price cannot be negative.';
            } else {
                priceError.textContent = '';
            }
        });

        // --- Live validation for the Rating field ---
        ratingInput.addEventListener('input', function () {
            const val = parseFloat(this.value);
            if (val < 0.0 || val > 5.0) {
                ratingError.textContent = 'Rating must be between 0.0 and 5.0.';
            } else {
                ratingError.textContent = '';
            }
        });

        // --- Live validation for the Description field ---
        descriptionInput.addEventListener('input', function () {
            const maxLength = 500;
            if (this.value.length > maxLength) {
                descriptionError.textContent = `Description cannot exceed ${maxLength} characters.`;
            } else {
                descriptionError.textContent = '';
            }
        });
    </script>

</body>

</html>
<?php
$conn->close();
?>