<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM restaurants WHERE id=$id");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $rating = $_POST['rating'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("UPDATE restaurants SET name=?, rating=?, category=?, description=?, image_url=? WHERE id=?");
    $stmt->bind_param("sisssi", $name, $rating, $category, $description, $image_url, $id);
    $stmt->execute();

    header("Location: admin_restaurants.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Restaurant</title></head>
<body>
<h2>Edit Restaurant</h2>
<form method="POST">
    <input type="text" name="name" value="<?= $row['name'] ?>" required><br>
    <input type="number" name="rating" value="<?= $row['rating'] ?>" required><br>
    <input type="text" name="category" value="<?= $row['category'] ?>" required><br>
    <textarea name="description"><?= $row['description'] ?></textarea><br>
    <input type="text" name="image_url" value="<?= $row['image_url'] ?>"><br>
    <button type="submit">Update</button>
</form>
</body>
</html>
