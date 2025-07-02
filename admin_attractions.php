<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

// Handle Approve / Reject / Delete actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE attractions SET status='approved' WHERE id=$id");
    } elseif ($action === 'reject') {
        $conn->query("UPDATE attractions SET status='rejected' WHERE id=$id");
    } elseif ($action === 'delete') {
        $conn->query("DELETE FROM attractions WHERE id=$id");
    }

    header("Location: admin_attractions.php");
    exit();
}

$result = $conn->query("SELECT * FROM attractions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Manage Attractions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
        }

        td img {
            width: 80px;
            height: 60px;
            object-fit: cover;
        }

        .actions a {
            margin-right: 8px;
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 13px;
        }

        .approve { background-color: green; }
        .reject { background-color: orange; }
        .edit { background-color: #007bff; }
        .delete { background-color: red; }

        .success {
            color: green;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Manage Attractions</h2>

<?php if (isset($_GET['updated'])) echo "<p class='success'>✅ Attraction updated successfully.</p>"; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Category</th>
        <th>Status</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if (!empty($row['image_url'])): ?>
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Image">
                <?php else: ?>
                    <span>No image</span>
                <?php endif; ?>
            </td>
            <td class="actions">
                <?php if ($row['status'] === 'pending'): ?>
                    <a class="approve" href="?action=approve&id=<?= $row['id'] ?>">Approve</a>
                    <a class="reject" href="?action=reject&id=<?= $row['id'] ?>">Reject</a>
                <?php endif; ?>
                <a class="edit" href="edit_attractions.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="delete" href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this attraction?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
