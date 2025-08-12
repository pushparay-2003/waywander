<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM restaurants WHERE id=$id");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $current_image = $row['image_url'];

    // Check if a new image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $upload_dir = 'uploads/';
        $image_path = $upload_dir . $image_name;
        move_uploaded_file($image_tmp, $image_path);
    } else {
        $image_path = $current_image; // use previous image if no new one is uploaded
    }

    $stmt = $conn->prepare("UPDATE restaurants SET name=?, rating=?, category=?, description=?, image_url=? WHERE id=?");
    $stmt->bind_param("sisssi", $name, $rating, $category, $description, $image_path, $id);
    $stmt->execute();

    header("Location: admin_restaurants.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Restaurant</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 40px;
        }

        .form-container {
            background-color: white;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-container label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container textarea,
        .form-container input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #ff5a5f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #e0484c;
        }

        .image-preview {
            margin-top: 15px;
            text-align: center;
        }

        .image-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Go Back Arrow -->
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="javascript:history.back()" style="text-decoration: none; font-size: 24px; color: #333;">
            &#8592;
        </a>
    </div>

    <div class="form-container">
        <h2>Edit Restaurant</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

            <label for="category">Category:</label>
            <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($row['description']) ?></textarea>

            <label for="image">Change Image (optional):</label>
            <input type="file" name="image" accept="image/*">

            <?php if (!empty($row['image_url'])): ?>
                <div class="image-preview">
                    <p>Current Image:</p>
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Restaurant Image">
                </div>
            <?php endif; ?>

            <button type="submit">Update Restaurant</button>
        </form>
    </div>


    <div class="form-container">
        <h2>Edit Restaurant</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>


            <label for="category">Category:</label>
            <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($row['description']) ?></textarea>

            <label for="image">Change Image (optional):</label>
            <input type="file" name="image" accept="image/*">

            <?php if (!empty($row['image_url'])): ?>
                <div class="image-preview">
                    <p>Current Image:</p>
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Restaurant Image">
                </div>
            <?php endif; ?>

            <button type="submit">Update Restaurant</button>
        </form>
    </div>
</body>
</html>
