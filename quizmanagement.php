<?php
// quizmanagement.php
session_start();

include 'db.php';

$message = '';

// Handle Add Question form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    $question_text = $conn->real_escape_string($_POST['question_text']);
    if (!empty($question_text)) {
        $sql = "INSERT INTO questions (question_text) VALUES ('$question_text')";
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert success'>New question added successfully!</div>";
        } else {
            $message = "<div class='alert error'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }
}

 // Handle Add Option form submission (now supports multiple options)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_option'])) {
    $question_id = (int)$_POST['question_id'];
    $options_text = $_POST['option_text_multiple'];

    // Split the textarea content by new lines, trim whitespace, and filter out empty lines
    $options_lines = array_filter(array_map('trim', explode("\n", $options_text)));

    $success_count = 0;
    $error_count = 0;

    if ($question_id > 0 && count($options_lines) > 0) {
        foreach ($options_lines as $option_text) {
            $safe_text = $conn->real_escape_string($option_text);
            $sql = "INSERT INTO options (question_id, option_text) VALUES ($question_id, '$safe_text')";
            if ($conn->query($sql) === TRUE) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
        $message = "<div class='alert success'>$success_count option(s) added successfully.</div>";
        if ($error_count > 0) {
             $message = "<div class='alert error'>$success_count option(s) added successfully, but $error_count option(s) failed.</div>";
        }
    } else {
        $message = "<div class='alert error'>Please select a question and enter at least one option.</div>";
    }
}

// Handle Add Tag and Assign form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_tag_assignment'])) {
    $option_id = (int)$_POST['option_id'];
    $course_id = (int)$_POST['course_id'];
    $tag_name = $conn->real_escape_string($_POST['tag_name']);
    $weight = (int)$_POST['weight'];

    if ($option_id > 0 && $course_id > 0 && !empty($tag_name) && $weight > 0) {
        // Start transaction
        $conn->begin_transaction();
        try {
            // 1. Insert new tag
            $sql_tag = "INSERT INTO tags (tag_name) VALUES ('$tag_name')";
            $conn->query($sql_tag);
            $new_tag_id = $conn->insert_id;

            // 2. Link tag to course
            $sql_course_tag = "INSERT INTO course_tags (course_id, tag_id) VALUES ($course_id, $new_tag_id)";
            $conn->query($sql_course_tag);

            // 3. Link tag to option with weight
            $sql_option_weight = "INSERT INTO option_tag_weights (option_id, tag_id, weight) VALUES ($option_id, $new_tag_id, $weight)";
            $conn->query($sql_option_weight);

            // If all queries succeed, commit the transaction
            $conn->commit();
            $message = "<div class='alert success'>Tag created and assigned successfully!</div>";
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $message = "<div class='alert error'>Transaction failed: " . $exception->getMessage() . "</div>";
        }
    }
}

// Fetch data for dropdowns
$questions = $conn->query("SELECT question_id, question_text FROM questions ORDER BY question_id DESC");
$options = $conn->query("SELECT o.option_id, o.option_text, q.question_text FROM options o JOIN questions q ON o.question_id = q.question_id ORDER BY o.option_id DESC");
$courses = $conn->query("SELECT course_id, title FROM courses ORDER BY title ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Management - CourseCompass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
        }

        .main-content {
            padding-top: 90px;
            padding-left: 280px; /* Adjust this value to match the width of your sidebar */
            padding-right: 40px;
            width: calc(100% - 320px); /* Adjust based on sidebar width + padding */
        }
        
        h1, h2 {
            color: #3b82f6;
            font-weight: 700;
            border-bottom: 2px solid #e5e5e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .management-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

          .form-control, textarea.form-control {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
            font-family: 'Inter', sans-serif;
        }
        .form-control:focus, textarea.form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .btn {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: inline-block;
            text-align: center;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <div>
        <?php include 'adminsidebar.php'; ?>
    </div>

    <div class="main-content">
        <h1>Quiz Management</h1>

        <?php echo $message; ?>

        <!-- Section 1: Add a Question -->
        <div class="management-section">
            <h2>Step 1: Add a New Question</h2>
            <form action="quizmanagement.php" method="post">
                <div class="form-group">
                    <label for="question_text">Question Text</label>
                    <input type="text" id="question_text" name="question_text" class="form-control" placeholder="e.g., What is your primary learning goal?" required>
                </div>
                <button type="submit" name="add_question" class="btn btn-submit">Add Question</button>
            </form>
        </div>

          <!-- Section 2: Add an Option for a Question -->
        <div class="management-section">
            <h2>Step 2: Add Options for a Question</h2>
            <form action="quizmanagement.php" method="post">
                <div class="form-group">
                    <label for="question_id">Select Question</label>
                    <select id="question_id" name="question_id" class="form-control" required>
                        <option value="">-- Select a Question --</option>
                        <?php 
                        $questions->data_seek(0);
                        while($row = $questions->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $row['question_id']; ?>"><?php echo htmlspecialchars($row['question_text']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="option_text_multiple">Option Texts (one per line)</label>
                    <textarea id="option_text_multiple" name="option_text_multiple" class="form-control" placeholder="Enter each option on a new line, for example:&#10;To start a new career&#10;To get a promotion&#10;To explore a hobby" rows="5" required></textarea>
                </div>
                <button type="submit" name="add_option" class="btn btn-submit">Add Options</button>
            </form>
        </div>

        <!-- Section 3: Add Tag and Assignments -->
        <div class="management-section">
            <h2>Step 3: Create Tag and Assign to Option & Course</h2>
            <form action="quizmanagement.php" method="post">
                <div class="form-group">
                    <label for="option_id">Select Option</label>
                    <select id="option_id" name="option_id" class="form-control" required>
                        <option value="">-- Select an Option to Tag --</option>
                        <?php
                        // Reset pointer and re-fetch for this loop
                        $options->data_seek(0);
                        while($row = $options->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $row['option_id']; ?>"><?php echo htmlspecialchars($row['option_text'] . ' (For Q: ' . substr($row['question_text'], 0, 40) . '...)'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="tag_name">New Tag Name</label>
                    <input type="text" id="tag_name" name="tag_name" class="form-control" placeholder="e.g., Career Change" required>
                </div>
                <div class="form-group">
                    <label for="course_id">Assign to Course</label>
                    <select id="course_id" name="course_id" class="form-control" required>
                        <option value="">-- Select a Course --</option>
                         <?php 
                         // Reset pointer and re-fetch for this loop
                         $courses->data_seek(0);
                         while($row = $courses->fetch_assoc()): 
                         ?>
                            <option value="<?php echo $row['course_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="weight">Assign Weight (1-3)</label>
                    <input type="number" id="weight" name="weight" class="form-control" min="1" max="3" placeholder="e.g., 3" required>
                </div>
                <button type="submit" name="add_tag_assignment" class="btn btn-submit">Create & Assign Tag</button>
            </form>
        </div>

    </div>

</body>
</html>
