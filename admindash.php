<?php
session_start();
include 'db.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_count = $conn->query("SELECT COUNT(*) as count FROM Users")->fetch_assoc()['count'];
$course_count = $conn->query("SELECT COUNT(*) as count FROM Courses")->fetch_assoc()['count'];
$feedback_count = $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'];

$feedbacks = "SELECT fid, fname, lname, email, subject, rating, message FROM feedback ORDER BY fid DESC";
$result_feedback = $conn->query($feedbacks);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            display: block;
            margin: 0;
        }

        .card-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex-basis: 250px;
            flex-grow: 1;
        }

        .card h3 {
            margin-top: 0;
            color: #34495e;
        }

        .card p {
            font-size: 2em;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            font-size: 0.9em;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .card a:hover {
            text-decoration: underline;
        }

        #feedback-list {
            margin-top: 40px;
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .feedback-table th,
        .feedback-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .feedback-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .feedback-table tbody tr:hover {
            background-color: #f3f4f6;
        }

        .feedback-table td {
            color: #6b7280;
        }
    </style>
</head>

<body>

    <?php include 'adminsidebar.php'; ?>

    <div class="content">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</h1>
        </div>

        <h2>Dashboard Overview</h2>
        <div class="card-container">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="card">
                <h3>Total Courses</h3>
                <p><?php echo $course_count; ?></p>
            </div>
            <div class="card">
                <h3>Feedbacks Submitted</h3>
                <p><?php echo $feedback_count; ?></p>
                <a id="toggle-feedback-btn">View Feedbacks</a>
            </div>
        </div>

        <section id="feedback-list" style="display: none;">
            <h2>All Submitted Feedbacks</h2>

            <?php if ($result_feedback && $result_feedback->num_rows > 0): ?>
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Rating</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_feedback->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['fid']); ?></td>
                                <td><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedbacks have been submitted yet.</p>
            <?php endif; ?>
        </section>
    </div>

    <!-- JavaScript for Toggle Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the button and the section to be toggled
            const toggleBtn = document.getElementById('toggle-feedback-btn');
            const feedbackSection = document.getElementById('feedback-list');

            // Check if both elements exist to prevent errors
            if (toggleBtn && feedbackSection) {

                // Add a click event listener to the button
                toggleBtn.addEventListener('click', function (event) {
                    // Prevent the default link behavior
                    event.preventDefault();

                    // Check the current display state of the feedback section
                    const isHidden = feedbackSection.style.display === 'none';

                    if (isHidden) {
                        // If it's hidden, show it and change the button text
                        feedbackSection.style.display = 'block';
                        toggleBtn.textContent = 'Hide Feedbacks';
                    } else {
                        // If it's visible, hide it and change the button text back
                        feedbackSection.style.display = 'none';
                        toggleBtn.textContent = 'View Feedbacks';
                    }
                });
            }
        });
    </script>

</body>

</html>