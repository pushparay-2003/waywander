<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM hotels WHERE id=$id");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("UPDATE hotels SET name=?, location=?, category=?, description=?, image_url=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $location, $category, $description, $image_url, $id);
    $stmt->execute();

    header("Location: admin_hotels.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Hotel</title>
</head>
<body>
<h2>Edit Hotel</h2>
<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required><br>
    <input type="text" name="location" value="<?= htmlspecialchars($row['location']) ?>" required><br>
    <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>" required><br>
    <textarea name="description"><?= htmlspecialchars($row['description']) ?></textarea><br>
    <input type="text" name="image_url" value="<?= htmlspecialchars($row['image_url']) ?>"><br>
    <button type="submit">Update</button>
</form>
</body>
</html>
