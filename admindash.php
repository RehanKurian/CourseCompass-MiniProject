<?php
session_start();
include 'db.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");//this line prevents the browser from caching the page
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");// this line sets the expiration date of the page to a date in the past
header("Pragma: no-cache");//this line is used for HTTP/1.0 compatibility

// Check if user is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not authorized
    exit();
}

// --- DATA FETCHING for this page ---
$user_count = $conn->query("SELECT COUNT(*) as count FROM Users")->fetch_assoc()['count'];
$course_count = $conn->query("SELECT COUNT(*) as count FROM Courses")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        .card-container {
            display: flex;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }

        .card h3 {
            margin-top: 0;
        }

        .card p {
            font-size: 2em;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }
    </style>
</head>

<body>

    <?php include 'adminsidebar.php'; // Include the new sidebar here ?>

    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</h1>
        </div>

        <!-- Dashboard Overview Content -->
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
        </div>
        <!-- End Dashboard Overview Content -->

    </div>

</body>

</html>