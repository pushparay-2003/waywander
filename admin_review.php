<?php
session_start();
include 'db.php';

// Fetch all reviews
$sql = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Reviews</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        .pending { color: orange; }
        .approved { color: green; }
        form.inline {
            display: inline;
        }
    </style>
</head>
<body>

<h2>Manage Reviews</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Rating</th>
        <th>Comment</th>
        <th>Item Type</th>
        <th>Item ID</th>
        <th>Status</th>
        <th>Submitted</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['rating'] ?> ★</td>
        <td><?= htmlspecialchars($row['comment']) ?></td>
        <td><?= $row['item_type'] ?></td>
        <td><?= $row['item_id'] ?></td>
        <td class="<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
            <?php if ($row['status'] == 'pending') : ?>
                <form method="POST" action="approve_review.php" class="inline">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Approve</button>
                </form>
            <?php endif; ?>
            <form method="POST" action="delete_review.php" class="inline" onsubmit="return confirm('Delete this review?')">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" style="color:red;">Delete</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
