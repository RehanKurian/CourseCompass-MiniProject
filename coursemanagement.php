<?php
session_start();
include 'db.php'; 

//  Initialize variables for the edit mode ---
$edit_mode = false;
$course_to_edit = null;

//  Check if the page is in edit mode from a GET request ---
// This happens when a user clicks the "Edit" link in the table.
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_mode = true;
    $course_id_to_edit = $_GET['id'];
    
    // Fetch the details of the specific course to be edited.
    $stmt = $conn->prepare("SELECT * FROM Courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_to_edit = $result->fetch_assoc();
    $stmt->close();
}


// --- PHP LOGIC TO HANDLE FORM SUBMISSIONS (Course Actions) ---
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // ADD course logic 
    if ($_POST['action'] == 'add_course') {
        $stmt = $conn->prepare("INSERT INTO Courses (title, platform, duration, level, category, url, description, rating, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssid", $_POST['title'], $_POST['platform'], $_POST['duration'], $_POST['level'], $_POST['category'], $_POST['url'], $_POST['description'], $_POST['rating'], $_POST['price']);
        if ($stmt->execute()) {
            $message = "Course added successfully!";
        } else {
            $message = "Error adding course: " . $stmt->error;
        }
        $stmt->close();
    }

    //  Logic to handle the 'update_course' action ---
    if ($_POST['action'] == 'update_course') {
        $stmt = $conn->prepare("UPDATE Courses SET title=?, platform=?, duration=?, level=?, category=?, url=?, description=?, rating=?, price=? WHERE course_id=?");
        // Note the new "i" at the end for the integer course_id.
        $stmt->bind_param("sssssssidi", $_POST['title'], $_POST['platform'], $_POST['duration'], $_POST['level'], $_POST['category'], $_POST['url'], $_POST['description'], $_POST['rating'], $_POST['price'], $_POST['course_id']);
        if ($stmt->execute()) {
            $message = "Course updated successfully!";
        } else {
            $message = "Error updating course: " . $stmt->error;
        }
        $stmt->close();
        // After an update, it's good practice to clear the edit mode
        $edit_mode = false; 
    }

    // DELETE course logic (from your code)
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #667eea;
            display: flex;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
        th, td {
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
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
            display: inline-block; /* Ensures link buttons behave like buttons */
        }
        .btn-primary, .btn-danger, .btn-secondary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .form-container h3 {
            margin-top: 0;
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
        .form-grid input, .form-grid select, .form-grid textarea {
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
        .action-buttons, .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include 'adminsidebar.php';  ?>

    <div class="main-content">
        <div class="header">
            <h1>Course Management</h1>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <!-- UPDATED: The heading is now dynamic -->
            <h3><?php echo $edit_mode ? 'Edit Course' : 'Add New Course'; ?></h3>
            
            <form action="coursemanagement.php" method="POST">
                <!-- UPDATED: The hidden action field is now dynamic -->
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update_course' : 'add_course'; ?>">
                
                <!-- NEW: If in edit mode, include the course_id as a hidden field -->
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_to_edit['course_id']); ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <!-- UPDATED: All form fields now have a 'value' attribute to pre-fill data -->
                    <div><label>Title</label><input type="text" name="title" value="<?php echo htmlspecialchars($course_to_edit['title'] ?? ''); ?>" required></div>
                    <div><label>Platform</label><input type="text" name="platform" value="<?php echo htmlspecialchars($course_to_edit['platform'] ?? ''); ?>"></div>
                    <div><label>Duration</label><input type="text" name="duration" value="<?php echo htmlspecialchars($course_to_edit['duration'] ?? ''); ?>"></div>
                    <div><label>Level</label><input type="text" name="level" value="<?php echo htmlspecialchars($course_to_edit['level'] ?? ''); ?>"></div>
                    <div><label>Category</label><input type="text" name="category" value="<?php echo htmlspecialchars($course_to_edit['category'] ?? ''); ?>"></div>
                    <div><label>URL</label><input type="text" name="url" value="<?php echo htmlspecialchars($course_to_edit['url'] ?? ''); ?>"></div>
                    <div><label>Rating (0.0-5.0)</label><input type="number" step="0.1" name="rating" value="<?php echo htmlspecialchars($course_to_edit['rating'] ?? ''); ?>"></div>
                    <div><label>Price</label><input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($course_to_edit['price'] ?? ''); ?>"></div>
                    <div style="grid-column: 1 / -1;"><label>Description</label><textarea name="description" rows="3"><?php echo htmlspecialchars($course_to_edit['description'] ?? ''); ?></textarea></div>
                </div>
                <br>
                <div class="form-actions">
                    <!-- UPDATED: The button text and an optional 'Cancel' link are now dynamic -->
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update Course' : 'Add Course'; ?></button>
                    <?php if ($edit_mode): ?>
                        <a href="coursemanagement.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h3 style="margin-top: 30px;">Existing Courses</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch courses for the table
                $result = $conn->query("SELECT course_id, title, category, price FROM Courses ORDER BY course_id");
                while ($course = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $course['course_id']; ?></td>
                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                    <td><?php echo htmlspecialchars($course['category']); ?></td>
                    <td>$<?php echo number_format($course['price'], 2); ?></td>
                    <td class="action-buttons">
                        <!-- UPDATED: The Edit button is now a link that triggers edit mode -->
                        <a href="coursemanagement.php?action=edit&id=<?php echo $course['course_id']; ?>" class="btn btn-secondary">Edit</a>
                        
                        <form action="coursemanagement.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this course?');" style="margin:0;">
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

</body>
</html>
<?php
// Close the database connection once everything is done
$conn->close();
?>
