<?php
// This line gets the name of the current file (e.g., "admindash.php")
// to automatically set the "active" class on the correct sidebar link.
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<style>
    body {
        font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        margin: 0;
        background-color: #f4f7f6;
        color: #333;
        display: flex;
    }

    .sidebar {
    width: 250px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ecf0f1;
    height: fit-content;
    padding: 20px;
    padding-bottom: 100px;
    box-sizing: border-box;
    position: fixed;
    top: 0;
    left: 0;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding-bottom: 20px;
        color: #fff;
    }

    .sidebar-header .logo {
        font-size: 22px;
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

    .sidebar ul li {
        position: relative;
    }

    .sidebar ul li a {
        display: block;
        color: #ecf0f1;
        text-decoration: none;
        padding: 23px 16px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 1rem;
        position: relative;
        transition: background 0.3s, color 0.3s;
        z-index: 1;
        overflow: visible;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        color: #fff;
    }

    .sidebar ul li a::after {
        content: '';
        position: absolute;
        left: 42%;
        margin-bottom:-10px;
        bottom: 6px;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: white;
        border-radius: 2px;
        transition: width 0.3s ease;
        z-index: 2;
    }

    .sidebar ul li a:hover::after,
    .sidebar ul li a.active::after {
        width: 70%;
    }

    .main-content {
        padding: 30px;
        width: 100%;
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
    @media print {
    .sidebar {
        position: static !important;
        width: 100% !important;
        height: auto !important;
    }

    .main-content {
        margin-left: 0 !important;
    }
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
    </ul>
</div>