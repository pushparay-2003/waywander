<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}
include 'db.php';

$result = $conn->query("SELECT * FROM restaurants ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Restaurants</title>
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
        a.btn-green { background-color: green; }
    </style>
</head>
<body>
<h2 style="text-align:center;">All Restaurants</h2>
    <!-- Go Back Arrow -->
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="javascript:history.back()" style="text-decoration: none; font-size: 24px; color: #333;">
            &#8592;
        </a>
    </div>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Category</th>
        <th>Image</th>
        <th>Website</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td>
            <?php if (!empty($row['image_url'])): ?>
                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Restaurant Image">
            <?php else: ?>
                <span>No image</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if (!empty($row['website_url'])): ?>
                <a class="btn btn-green" href="<?= htmlspecialchars($row['website_url']) ?>" target="_blank">Visit Website</a>
            <?php else: ?>
                <span>No website</span>
            <?php endif; ?>
        </td>
        <td>
            <a class="btn" href="edit_restaurant.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn btn-red" href="delete_restaurants.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this restaurant?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
