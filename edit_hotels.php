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
    $website_url = $_POST['website_url'];
    $image_url = $row['image_url']; // default to existing

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $stmt = $conn->prepare("UPDATE hotels SET name=?, location=?, category=?, description=?, image_url=?, website_url=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $location, $category, $description, $image_url, $website_url, $id);
    $stmt->execute();

    header("Location: admin_hotels.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Hotel</title>
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
        <a href="admin_hotels.php" class="back-arrow">&#8592; Back</a>

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
