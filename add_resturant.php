<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $rating = $_POST["rating"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $image_url = $_POST["image_url"];

    $stmt = $conn->prepare("INSERT INTO restaurants (name, rating, category, description, image_url, status) 
                            VALUES (?, ?, ?, ?, ?, 'approved')");
    $stmt->bind_param("sisss", $name, $rating, $category, $description, $image_url);

    if ($stmt->execute()) {
        $inserted_id = $stmt->insert_id;
        $message = "Restaurant added successfully! ID: $inserted_id";
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
    <title>Add Restaurant</title>
    <style>
        <?php include '../style.css'; ?>
        form { max-width: 500px; margin: auto; padding: 20px; background: #eee; border-radius: 10px; }
        input, select, textarea { width: 100%; margin-top: 10px; padding: 10px; }
        button { margin-top: 15px; padding: 10px 20px; }
    </style>
</head>
<body>
<h2 style="text-align:center;">Admin: Add Restaurant</h2>

<?php if (isset($message)) echo "<p style='text-align:center; color: green;'>$message</p>"; ?>

<form method="POST">
    <label>Name:</label><input type="text" name="name" required>
    <label>Rating (1–5):</label>
    <select name="rating" required>
        <option value="">Select</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
    </select>
    <label>Category:</label>
    <select name="category" required>
        <option>Budget</option><option>Rooftop</option><option>Local</option><option>Family</option><option>Romantic</option>
    </select>
    <label>Description:</label><textarea name="description" rows="4" required></textarea>
    <label>Image URL:</label><input type="text" name="image_url">
    <button type="submit">Add Restaurant</button>
</form>
</body>
</html>
