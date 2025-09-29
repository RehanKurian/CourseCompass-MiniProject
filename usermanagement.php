<?php
session_start();
include 'db.php';

// --- PHP LOGIC TO HANDLE FORM SUBMISSIONS (User Actions) ---
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] == 'update_role') {
        $user_id = $_POST['user_id'];
        $new_role = $_POST['role'];
        $stmt = $conn->prepare("UPDATE Users SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        if ($stmt->execute()) {
            $message = "User role updated successfully!";
        } else {
            $message = "Error updating role: " . $stmt->error;
        }
        $stmt->close();
    }

    if ($_POST['action'] == 'remove_user') {
        $user_id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "User removed successfully!";
        } else {
            $message = "Error removing user: " . $stmt->error;
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
    <title>User Management</title>
    <style>
        /* --- General Layout and Theme (Repeated) --- */
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
            width: 100%;
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

        /* --- Page-Specific CSS for User Management --- */
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
        }

        .btn-danger {
           background: linear-gradient(135deg, #667eea, #764ba2);
        }

        select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            color: #fff;
            background-color: #2ecc71;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
      <?php include 'adminsidebar.php'; ?>
    <div class="main-content">
        <div class="header">
            <h1>User Management</h1>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT user_id, fname, lname, email, role FROM Users");
                while ($user = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <form action="usermanagement.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User
                                    </option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="usermanagement.php" method="POST"
                                onsubmit="return confirm('Are you sure you want to remove this user?');"
                                style="display:inline;">
                                <input type="hidden" name="action" value="remove_user">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" class="btn btn-danger"
                                    <?php if ($user['role'] == 'admin') echo 'disabled title="Cannot remove admin users"'; ?>>
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile;
                $conn->close(); ?>
            </tbody>
        </table>

    </div>

</body>

</html>