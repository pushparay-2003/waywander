<?php
session_start();
include 'db.php';
include 'nav.php';
// Handle wishlist add request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['item_type'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<p>Please log in to add items to your wishlist.</p>";
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $item_id = (int)$_POST['item_id'];
    $item_type = $conn->real_escape_string($_POST['item_type']);

    // Insert if not already in wishlist (ignore duplicates)
    $stmt = $conn->prepare("INSERT IGNORE INTO wishlists (user_id, item_id, item_type) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    $stmt->execute();
    $stmt->close();

    // Redirect back to avoid form resubmission on reload
    header("Location: attractions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Dhulikhel Attractions | WayWander</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 20px;
        }
        form {
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        input, button, select {
            padding: 10px;
            font-size: 16px;
        }
        .attraction-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .attraction-card img {
            width: 180px;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
        }
        .attraction-info {
            flex: 1;
        }
        .attraction-info h2 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        .attraction-info p {
            margin: 5px 0;
            color: #666;
        }
        /* Add to wishlist button styling */
        .wishlist-btn {
            padding: 8px 14px;
            background-color: #ff5a5f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .wishlist-btn:disabled {
            background-color: #ccc;
            cursor: default;
        }
    </style>
</head>
<body>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WayWander</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ✅ Correct CSS link -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Attractions in Dhulikhel</h1>

<form method="GET">
    <input type="text" name="search" placeholder="Search name or description" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    <button type="submit">Search</button>
</form>

<?php
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM attractions WHERE status = 'approved'";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

$result = $conn->query($sql);

$user_id = $_SESSION['user_id'] ?? null;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='attraction-card'>";
        echo "<img src='" . htmlspecialchars($row['image_url'] ?: 'images/default.jpg') . "' alt='Image'>";
        echo "<div class='attraction-info'>";
        echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "</div>";

        // Show add to wishlist form only if user logged in
        if ($user_id) {
            echo "<form method='POST' style='margin-left: auto;'>";
            echo "<input type='hidden' name='item_id' value='" . (int)$row['id'] . "'>";
            echo "<input type='hidden' name='item_type' value='attraction'>";
            echo "<button type='submit' class='wishlist-btn'>Add to Wishlist</button>";
            echo "</form>";
        } else {
            echo "<p style='margin-left:auto; color:#999; font-style: italic;'>Log in to add to wishlist</p>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No attractions found.</p>";
}

$conn->close();
?>
<?php if (isset($_GET['review_submitted'])): ?>
<style>
    #review-popup {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #4BB543;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        font-weight: 600;
        z-index: 10000;
        animation: fadeInOut 4s forwards;
        font-family: Arial, sans-serif;
    }
    @keyframes fadeInOut {
        0% {opacity: 0; transform: translateY(20px);}
        10% {opacity: 1; transform: translateY(0);}
        90% {opacity: 1; transform: translateY(0);}
        100% {opacity: 0; transform: translateY(20px);}
    }
</style>
<div id="review-popup">
    Review submitted successfully! It is pending approval.
</div>
<script>
    setTimeout(() => {
        const popup = document.getElementById('review-popup');
        if (popup) popup.style.display = 'none';
    }, 4000);
</script>
<?php endif; ?>


</body>
</html>
