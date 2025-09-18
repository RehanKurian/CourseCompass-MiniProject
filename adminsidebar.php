<?php
// This line gets the name of the current file (e.g., "admindash.php")
// to automatically set the "active" class on the correct sidebar link.
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        margin: 0;
        background-color: #f4f7f6;
        color: #333;
        display: flex;
    }
    .sidebar {
        width: 250px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ecf0f1;
        height: 100vh;
        padding: 20px;
        box-sizing: border-box;
        position: fixed;
        flex-shrink: 0;
    }
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px; /* Adds space between the logo and the text */
        padding-bottom: 20px; /* Adds space below the site name */
        color: #fff;
    }
    .sidebar-header .logo {
        font-size: 22px; /* Makes the compass emoji larger */
    }
    .sidebar-header h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 500;
    }
    
    .sidebar ul {
        list-style: none;
        padding: 0;
    }
    .sidebar ul li a {
        display: block;
        color: #ecf0f1;
        text-decoration: none;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 10px;
        transition: background-color 0.3s;
    }
    .sidebar ul li a:hover, .sidebar ul li a.active {
        background-color: #34495e;
    }
    .main-content {
        margin-left: 250px;
        padding: 30px;
        width: calc(100% - 250px);
        box-sizing: border-box;
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
    .message {
        padding: 15px;
        border-radius: 5px;
        color: #fff;
        background-color: #2ecc71;
        margin-bottom: 20px;
    }
    .message.error {
        background-color: #e74c3c;
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">🧭</div>
        <h3>CourseCompass</h3>
    </div>
    

    <ul>
        <li><a href="admindash.php" class="<?= $currentPage == 'admindash.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="usermanagement.php" class="<?= $currentPage == 'usermanagement.php' ? 'active' : '' ?>">User Management</a></li>
        <li><a href="quizmanagement.php" class="<?= $currentPage == 'quizmanagement.php' ? 'active' : '' ?>">Quiz Management</a></li>
        <li><a href="userrecommendations.php" class="<?= $currentPage == 'userrecommendations.php' ? 'active' : '' ?>">Recommendations</a></li>
        <li><a href="coursemanagement.php" class="<?= $currentPage == 'coursemanagement.php' ? 'active' : '' ?>">Course Management</a></li>
        <li><a href="adminprofile.php" class="<?= $currentPage == 'adminprofile.php' ? 'active' : '' ?>">Profile</a></li>
        <li><a href="signout.php">Logout</a></li>
    </ul>
</div>
