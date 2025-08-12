<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';
include 'admin_nav.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $location = $_POST["location"];
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
            $new_filename = uniqid("img_", true) . "." . $file_ext;
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
        $stmt = $conn->prepare("INSERT INTO attractions (name, location, description, image_url, status) VALUES (?, ?, ?, ?, 'approved')");
        $stmt->bind_param("ssss", $name, $location, $description, $image_path);

        if ($stmt->execute()) {
            $message = "✅ Attraction added successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Attraction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 50px;
        }
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 25px;
            font-size: 1.5rem;
            color: #0d6efd;
            text-decoration: none;
        }
        .back-arrow:hover {
            color: #084298;
        }
    </style>
</head>
<body>

<a href="javascript:history.back()" class="back-arrow" title="Go Back">&#8592;</a>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Add New Attraction</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-info text-center"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Attraction Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter attraction name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Enter location" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Enter description" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success px-4">Add Attraction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
