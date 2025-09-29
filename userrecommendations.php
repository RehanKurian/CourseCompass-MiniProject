<?php
session_start();
include 'db.php';

// Authentication: Ensure the user is a logged-in admin.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users to display on the page.
$sql_users = "SELECT user_id, fname, lname, email FROM users WHERE role = 'user' ORDER BY fname ASC";
$result_users = $conn->query($sql_users);

// Prepare a statement to fetch recommendations for each user inside the loop.
$sql_recs = "
    SELECT c.title, c.platform, c.level, c.duration, c.url, r.score 
    FROM recommendations r 
    JOIN courses c ON r.course_id = c.course_id 
    WHERE r.user_id = ? 
    ORDER BY r.score DESC 
    LIMIT 50
";
$stmt_recs = $conn->prepare($sql_recs);
include 'adminsidebar.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Recommendations - Admin Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* --- Base and Admin Layout Styles --- */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #333;
            display: flex; /* This is key for the sidebar layout */
        }

        /* --- Main Content Area --- */
        .main-content {
            margin-left: 250px; 
            padding: 30px;
            width: 100%;
            box-sizing: border-box;
        }
        .main-content h1 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: #1a1a1a;
        }

        /* --- User List and Rows --- */
        .users-list {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .user-row-container {
            border-bottom: 1px solid #e5e7eb;
        }
        .user-row-container:last-child {
            border-bottom: none;
        }
        .user-row {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .user-info h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }
        .user-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
        }

        /* --- Dropdown Button and Menu --- */
        .dropdown-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .dropdown-btn:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
        }

        .dropdown-menu {
            display: none;
            width: 100%;
            background-color: #f9fafb;
            padding: 1.5rem;
            box-sizing: border-box;
            animation: fadeIn 0.4s;
        }
        .dropdown-menu.visible {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- Recommendation Item Styling --- */
        .recommendation-item {
            padding: 0.8rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .recommendation-item:last-child {
            border-bottom: none;
        }
        .recommendation-title {
            font-weight: 600;
            font-size: 1rem;
            margin: 0 0 5px 0;
        }
        .recommendation-title a {
            text-decoration: none;
            color: #3b82f6;
        }
        .recommendation-title a:hover {
            text-decoration: underline;
        }
        .recommendation-meta {
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Main content container that sits next to the sidebar -->
    <div class="main-content">
        <h1>All User Recommendations</h1>

        <?php if ($result_users->num_rows > 0): ?>
            <div class="users-list">
                <?php while ($user = $result_users->fetch_assoc()): ?>
                    <div class="user-row-container">
                        <div class="user-row">
                            <div class="user-info">
                                <h3><?= htmlspecialchars($user['fname']) ?> <?= htmlspecialchars($user['lname']) ?></h3>
                                <p><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <button class="dropdown-btn" data-userid="<?= $user['user_id'] ?>">
                                Show Recommendations
                            </button>
                        </div>
                        <div class="dropdown-menu" id="menu-<?= $user['user_id'] ?>">
                            <?php
                            $stmt_recs->bind_param('i', $user['user_id']);
                            $stmt_recs->execute();
                            $result_recs = $stmt_recs->get_result();

                            if ($result_recs->num_rows > 0):
                                while ($rec = $result_recs->fetch_assoc()): ?>
                                    <div class="recommendation-item">
                                        <p class="recommendation-title">
                                            <a href="<?= htmlspecialchars($rec['url']) ?>" target="_blank">
                                                <?= htmlspecialchars($rec['title']) ?>
                                            </a>
                                        </p>
                                        <p class="recommendation-meta">
                                            <strong>Platform:</strong> <?= htmlspecialchars($rec['platform']) ?> | 
                                            <strong>Level:</strong> <?= htmlspecialchars($rec['level']) ?> | 
                                            <strong>Score:</strong> <?= htmlspecialchars(round($rec['score'], 2)) ?>
                                        </p>
                                    </div>
                                <?php endwhile;
                            else: ?>
                                <p>No recommendations found for this user.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No users found in the database.</p>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dropdown-btn').forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.dataset.userid;
                const targetMenu = document.getElementById(`menu-${userId}`);
                const isVisible = targetMenu.classList.contains('visible');

                // Close all other open menus
                document.querySelectorAll('.dropdown-menu.visible').forEach(menu => {
                    if (menu.id !== `menu-${userId}`) {
                        menu.classList.remove('visible');
                        const otherButton = document.querySelector(`.dropdown-btn[data-userid="${menu.id.split('-')[1]}"]`);
                        if (otherButton) {
                            otherButton.textContent = 'Show Recommendations';
                        }
                    }
                });

                // Toggle the clicked menu
                if (isVisible) {
                    targetMenu.classList.remove('visible');
                    button.textContent = 'Show Recommendations';
                } else {
                    targetMenu.classList.add('visible');
                    button.textContent = 'Hide Recommendations';
                }
            });
        });
     });
    </script>
</body>
</html>
<?php
// Close the prepared statement and database connection
$stmt_recs->close();
$conn->close();
?>
