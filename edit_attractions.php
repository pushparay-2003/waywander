<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

if (!isset($_GET['id'])) {
    echo "No attraction selected.";
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM attractions WHERE id = $id");

if (!$result || $result->num_rows == 0) {
    echo "Attraction not found.";
    exit();
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $category = $_POST["category"];
    $image_url = $_POST["image_url"];

    $stmt = $conn->prepare("UPDATE attractions SET name=?, location=?, description=?, category=?, image_url=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $location, $description, $category, $image_url, $id);
    $stmt->execute();

    header("Location: admin_attractions.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Attraction</title>
    <style>
        form {
            max-width: 500px;
            margin: auto;
            background: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            margin-top: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Edit Attraction</h2>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($row['location']) ?>" required>

        <label>Description:</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($row['description']) ?></textarea>

        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>">

        <label>Image URL:</label>
        <input type="text" name="image_url" value="<?= htmlspecialchars($row['image_url']) ?>">

        <button type="submit">Update</button>
    </form>
</body>
</html>
