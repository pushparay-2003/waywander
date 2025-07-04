<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}
include 'db.php';

$result = $conn->query("SELECT * FROM hotels ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Hotels</title>
    <style>
        table { width: 95%; margin: 20px auto; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #333; color: white; }
        img { width: 80px; height: 60px; object-fit: cover; }
        a.btn {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 5px;
        }
        a.btn-red { background-color: red; }
    </style>
</head>
<body>
<h2 style="text-align:center;">All Hotels</h2>

<table>
    <tr>
        <th>ID</th><th>Name</th><th>Location</th><th>Category</th><th>Image</th><th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td>
            <?php if (!empty($row['image_url'])): ?>
                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Hotel Image">
            <?php else: ?>
                <span>No image</span>
            <?php endif; ?>
        </td>
        <td>
            <a class="btn" href="edit_hotels.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn btn-red" href="delete_hotels.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this hotel?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
