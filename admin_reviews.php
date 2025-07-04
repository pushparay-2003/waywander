<?php
session_start();
include 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Handle Approve Action
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE reviews SET status = 'approved' WHERE id = $id");
    header("Location: admin_reviews.php");
    exit();
}

// Handle Delete Action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id = $id");
    header("Location: admin_reviews.php");
    exit();
}

// Fetch all reviews
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reviews | WayWander</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f7f7f7;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #ff5a5f;
            color: white;
        }
        .action-buttons a {
            padding: 6px 12px;
            text-decoration: none;
            margin-right: 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .approve-btn {
            background-color: #4BB543;
            color: white;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }
        .pending {
            color: #e67e22;
            font-weight: bold;
        }
        .approved {
            color: #2ecc71;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Manage Reviews</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Item Type</th>
            <th>Item ID</th>
            <th>Name</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['item_type']) ?></td>
                <td><?= $row['item_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= str_repeat("★", (int)$row['rating']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['comment'])) ?></td>
                <td class="<?= $row['status'] === 'approved' ? 'approved' : 'pending' ?>">
                    <?= ucfirst(htmlspecialchars($row['status'])) ?>
                </td>
                <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                <td class="action-buttons">
                    <?php if ($row['status'] !== 'approved'): ?>
                        <a href="?approve=<?= $row['id'] ?>" class="approve-btn">Approve</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this review?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No reviews found.</p>
<?php endif; ?>

</body>
</html>
