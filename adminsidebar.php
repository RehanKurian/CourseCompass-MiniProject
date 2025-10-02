<?php
// This line gets the name of the current file (e.g., "admindash.php")
// to automatically set the "active" class on the correct sidebar link.
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>

<style>
    .sidebar {
        margin: 0;
        padding: 0;
        width: 200px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        position: fixed;
        height: 100%;
        overflow: auto;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px 16px;
        gap: 3px;
    }

    .sidebar-header .logo {
        font-size: 20px;
    }

    .sidebar-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        color: #ffff;
    }

    .sidebar a {
        display: block;
        color: #ffff;
        padding: 23px;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;

    }

    .sidebar a.active {
        background-color: #32495eff;
        border-radius: 8px;
        color: white;
    }

    .sidebar a:hover:not(.active) {
        background-color: #32495eff;
        border-radius: 8px;
        color: white;
    }

    div.content {
        margin-left: 200px;
        padding: 15px 40px;
        height: 1000px;
    }

    /* On screens that are less than 700px wide, make the sidebar into a topbar */
    @media screen and (max-width: 700px) {
        .sidebar {
            width: 130%;
            height: auto;
            position: relative;
            display: flex;
            flex-wrap: wrap;
        }

        .sidebar-header {
            /* Make header part of the horizontal flow */
            padding: 10px 16px;
            border-bottom: none;
        }

        .sidebar a {
            float: left;
        }

        div.content {
            margin-left: 0;
        }
    }

    /* On screens that are less than 400px, display the bar vertically */
    @media screen and (max-width: 400px) {
        .sidebar a {
            text-align: center;
            float: none;
        }
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">🧭</div>
        <h3>CourseCompass</h3>
    </div>

    <a href="admindash.php" class="<?= $currentPage == 'admindash.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="usermanagement.php" class="<?= $currentPage == 'usermanagement.php' ? 'active' : '' ?>">User Management</a>
    <a href="quizmanagement.php" class="<?= $currentPage == 'quizmanagement.php' ? 'active' : '' ?>">Quiz Management</a>
    <a href="userrecommendations.php"
        class="<?= $currentPage == 'userrecommendations.php' ? 'active' : '' ?>">Recommendations</a>
    <a href="coursemanagement.php" class="<?= $currentPage == 'coursemanagement.php' ? 'active' : '' ?>">Course
        Management</a>
    <a href="adminprofile.php" class="<?= $currentPage == 'adminprofile.php' ? 'active' : '' ?>">Profile</a>
</div>