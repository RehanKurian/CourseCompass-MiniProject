<?php
// Start output buffering as a safety net.
ob_start();

session_start();
include "db.php";
include "recommendation_logic.php";

// Check if user is logged in.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
$current_user_id = $_SESSION['user_id'];

// --- FORM PROCESSING LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Clear old responses for this user to ensure fresh data.
    $stmt_delete = $conn->prepare("DELETE FROM responses WHERE user_id = ?");
    $stmt_delete->bind_param("i", $current_user_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Insert the new responses.
    $sql_insert = "INSERT INTO responses (user_id, question_id, option_id) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    $answer_count = 0;
    // Loop through the submitted POST data to find answers.
    foreach ($_POST as $post_key => $post_value) {
        if (strpos($post_key, 'question_') === 0) {
            $question_id = substr($post_key, strlen('question_'));
            // Ensure the value is an array (from checkboxes).
            if (is_array($post_value)) {
                foreach ($post_value as $option_id) {
                    $stmt_insert->bind_param("iii", $current_user_id, $question_id, $option_id);
                    $stmt_insert->execute();
                    $answer_count++;
                }
            }
        }
    }
    $stmt_insert->close();

    // --- CALL THE RECOMMENDATION ENGINE ---
    // After saving answers, call the function to calculate recommendations.
    if ($answer_count > 0) {
        // Pass the mysqli connection object '$conn' to the function.
        calculateAndStoreRecommendations($conn, $current_user_id);
    }

    // Set a message for the user.
    $_SESSION['flash_message'] = "Your recommendations are ready!";

    // --- REDIRECT TO THE RECOMMENDATIONS PAGE ---
    header("Location: recommendations.php");
    exit();
}

// Check for a flash message from a previous action.
$flashMessage = null;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire</title>
    <link rel="stylesheet" href="quiz.css">
    <style>
        /* Your flash-message CSS here */
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container">

        <?php if ($flashMessage): ?>
            <div class="flash-message">
                <?php echo htmlspecialchars($flashMessage); ?>
            </div>
        <?php endif; ?>

        <h1>Course Questionnaire</h1>
        <p>Please select all options that apply to you.</p>

        <form action="quiz.php" method="post">
            <?php
            // --- FORM DISPLAY LOGIC (using MySQLi) ---
            // The $conn object is already available from 'db.php'.
            
            $sql_questions = "SELECT question_id, question_text FROM questions ORDER BY question_id ASC";
            $result_questions = $conn->query($sql_questions);

            if ($result_questions->num_rows > 0) {
                while ($row_question = $result_questions->fetch_assoc()) {
                    $question_id = $row_question['question_id'];
                    $question_text = $row_question['question_text'];

                    echo '<div class="question-block">';
                    echo '<p class="question-text">' . htmlspecialchars($question_text) . '</p>';

                    $sql_options = "SELECT option_id, option_text FROM options WHERE question_id = ? ORDER BY option_id ASC";
                    $stmt_options = $conn->prepare($sql_options);
                    $stmt_options->bind_param("i", $question_id);
                    $stmt_options->execute();
                    $result_options = $stmt_options->get_result();

                    if ($result_options->num_rows > 0) {
                        echo '<ul class="options">';
                        while ($row_option = $result_options->fetch_assoc()) {
                            $option_id = $row_option['option_id'];
                            $option_text = $row_option['option_text'];
                            echo '<li>';
                            echo '<label>';
                            echo '<input type="checkbox" name="question_' . $question_id . '[]" value="' . $option_id . '">';
                            echo htmlspecialchars($option_text);
                            echo '</label>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No options available for this question.</p>';
                    }
                    $stmt_options->close(); 
                    echo '</div>';
                }
            } else {
                echo '<p>No questions found in the database.</p>';
            }

            $conn->close(); 
            ?>

            <button type="submit" class="submit-btn">Get Recommendations</button>
        </form>
    </div>
</body>

</html>
<?php
// Flush the output buffer.
ob_end_flush();
?>