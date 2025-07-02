<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $conn->query("DELETE FROM reviews WHERE id = $id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Reviews</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>

<h1>Admin Panel - Reviews</h1>

<?php
$result = $conn->query("SELECT * FROM reviews");

if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Name</th><th>Rating</th><th>Comment</th><th>Item</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['rating']}</td>
            <td>{$row['comment']}</td>
            <td>{$row['item_type']} (ID: {$row['item_id']})</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <button name='delete'>Delete</button>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No reviews found.</p>";
}

$conn->close();
?>

</body>
</html>
