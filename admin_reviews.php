<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}
include 'db.php';

$result = $conn->query("SELECT r.id, r.rating, r.comment, r.created_at, u.email, 
    CASE
      WHEN r.item_type = 'hotel' THEN h.name
      WHEN r.item_type = 'restaurant' THEN res.name
      WHEN r.item_type = 'attraction' THEN a.name
      ELSE 'Unknown'
    END AS item_name,
    r.item_type
FROM reviews r
LEFT JOIN users u ON r.user_id = u.id
LEFT JOIN hotels h ON r.item_id = h.id AND r.item_type = 'hotel'
LEFT JOIN restaurants res ON r.item_id = res.id AND r.item_type = 'restaurant'
LEFT JOIN attractions a ON r.item_id = a.id AND r.item_type = 'attraction'
ORDER BY r.created_at DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Reviews</title>
    <style>
        table { width: 95%; margin: 20px auto; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: middle; }
        th { background-color: #333; color: white; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All Reviews</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Type</th>
            <th>User Email</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Submitted At</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= ucfirst(htmlspecialchars($row['item_type'])) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['rating']) ?></td>
            <td><?= htmlspecialchars($row['comment']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
