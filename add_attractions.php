<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $image_url = $_POST["image_url"];

    $stmt = $conn->prepare("INSERT INTO attractions (name, location, description, image_url, status) VALUES (?, ?, ?, ?, 'approved')");
    $stmt->bind_param("ssss", $name, $location, $description, $image_url);

    if ($stmt->execute()) {
        $message = "Attraction added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Attraction</title>
    <style>
        <?php include '../style.css'; ?>
        form { max-width: 500px; margin: auto; padding: 20px; background: #eee; border-radius: 10px; }
        input, textarea { width: 100%; margin-top: 10px; padding: 10px; }
        button { margin-top: 15px; padding: 10px 20px; }
    </style>
</head>
<body>
<h2 style="text-align:center;">Admin: Add Attraction</h2>

<?php if (isset($message)) echo "<p style='text-align:center; color: green;'>$message</p>"; ?>

<form method="POST">
    <label>Name:</label><input type="text" name="name" required>
    <label>Location:</label><input type="text" name="location" required>
    <label>Description:</label><textarea name="description" rows="4" required></textarea>
    <label>Image URL:</label><input type="text" name="image_url">
    <button type="submit">Add Attraction</button>
</form>
</body>
</html>
