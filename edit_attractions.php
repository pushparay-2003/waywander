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
    <title>Edit Attractions</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input[type="text"], textarea, select, input[type="url"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="file"] {
            margin-top: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
        }
        img.preview {
            max-width: 100%;
            height: auto;
            margin-top: 15px;
            border-radius: 8px;
        }
        /* Go back arrow styling */
        .back-arrow {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .back-arrow:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Go Back Arrow -->
        <a href="admin_attractions.php" class="back-arrow">&#8592; Back</a>

        <h2>Edit Hotel</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

            <label>Location:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($row['location']) ?>" required>

            <label>Category:</label>
            <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>" required>

            <label>Description:</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($row['description']) ?></textarea>

            <label>Website URL:</label>
            <input type="url" name="website_url" value="<?= htmlspecialchars($row['website_url']) ?>" placeholder="https://example.com">

            <label>Change Image (optional):</label>
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($row['image_url'])): ?>
                <img class="preview" src="<?= htmlspecialchars($row['image_url']) ?>" alt="Hotel Image">
            <?php endif; ?>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>