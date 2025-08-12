<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $category = $_POST["category"];
    $description = $_POST["description"];

    // Image upload handling
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_path = "";
    if (isset($_FILES["image_file"]) && $_FILES["image_file"]["error"] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES["image_file"]["tmp_name"];
        $file_name = basename($_FILES["image_file"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array($file_ext, $allowed_types)) {
            $new_filename = uniqid("restaurant_", true) . "." . $file_ext;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $target_file)) {
                $image_path = $target_file;
            } else {
                $message = "Error uploading the image.";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG & GIF allowed.";
        }
    }

    if (!empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO restaurants (name, category, description, image_url, status) VALUES (?, ?, ?, ?, 'approved')");
        $stmt->bind_param("ssss", $name, $category, $description, $image_path);

        if ($stmt->execute()) {
            $message = "Restaurant added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            margin-top: 50px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            position: relative;
        }
        .btn-custom {
            background: linear-gradient(135deg, #43cea2, #185a9d);
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #185a9d, #43cea2);
        }
        .back-arrow {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
        }
        .back-arrow:hover {
            color: #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header">
            <a href="javascript:history.back()" class="back-arrow">&#8592;</a>
            Admin: Add Restaurant
        </div>
        <div class="card-body">
            <?php if (isset($message)) echo "<div class='alert alert-info text-center'>$message</div>"; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter restaurant name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option>Budget</option>
                        <option>Rooftop</option>
                        <option>Local</option>
                        <option>Family</option>
                        <option>Romantic</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Write a short description" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Image:</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Add Restaurant</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
