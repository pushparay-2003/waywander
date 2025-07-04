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

    $stmt = $conn->prepare("INSERT IGNORE INTO wishlists (user_id, item_id, item_type) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    $stmt->execute();
    $stmt->close();

   header("Location: restaurants.php?wishlist_added=1");
    exit;
}

// Get filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$rating = isset($_GET['rating']) ? $_GET['rating'] : '';

// Build SQL query
$sql = "SELECT * FROM restaurants WHERE status = 'approved'";
if ($search !== '') $sql .= " AND name LIKE '%" . $conn->real_escape_string($search) . "%'";
if ($category !== '') $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
if ($rating !== '') $sql .= " AND rating = " . intval($rating);
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dhulikhel Restaurants | WayWander</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
        .restaurant-card { background: #fff; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 40px; display: flex; flex-wrap: wrap; gap: 20px; }
        .restaurant-card img { width: 100%; max-width: 400px; height: auto; border-radius: 6px; }
        .restaurant-info { flex: 1; min-width: 300px; }
        .rating { color: orange; font-size: 18px; }
        .review-section { background: #f9f9f9; padding: 15px; margin-top: 15px; border-left: 4px solid orange; border-radius: 4px; width: 100%; }
        .review { padding: 8px 0; border-bottom: 1px solid #ddd; }
        .review:last-child { border-bottom: none; }
        .review-form input, .review-form select, .review-form textarea { width: 100%; margin-top: 8px; padding: 8px; }
        .review-form button { margin-top: 10px; padding: 10px; background-color: orange; border: none; color: white; cursor: pointer; }
        .search-box { margin-bottom: 30px; background: #fff; padding: 15px; border-radius: 6px; display: flex; gap: 10px; flex-wrap: wrap; }
        .search-box input, .search-box select { padding: 8px; }
        .wishlist-btn {
            padding: 8px 14px;
            background-color: #ff5a5f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            align-self: flex-start;
            height: 40px;
            margin-left: auto;
        }
        .wishlist-btn:disabled {
            background-color: #ccc;
            cursor: default;
        }
        .login-msg {
            margin-left: auto;
            color: #999;
            font-style: italic;
            align-self: center;
            height: 40px;
        }
    </style>
</head>
<body>

<h1>Restaurants in Dhulikhel</h1>

<!-- Search/Filter Form -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>">
    <select name="category">
        <option value="">All Categories</option>
        <option value="Budget" <?= $category === 'Budget' ? 'selected' : '' ?>>Budget</option>
        <option value="Rooftop" <?= $category === 'Rooftop' ? 'selected' : '' ?>>Rooftop</option>
        <option value="Local" <?= $category === 'Local' ? 'selected' : '' ?>>Local</option>
        <option value="Family" <?= $category === 'Family' ? 'selected' : '' ?>>Family</option>
        <option value="Romantic" <?= $category === 'Romantic' ? 'selected' : '' ?>>Romantic</option>
    </select>
    <select name="rating">
        <option value="">All Ratings</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>" <?= $rating == $i ? 'selected' : '' ?>><?= $i ?> ★</option>
        <?php endfor; ?>
    </select>
    <button type="submit">Search</button>
</form>

<?php
$user_id = $_SESSION['user_id'] ?? null;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $restaurant_id = $row['id'];
        echo "<div class='restaurant-card'>";
        echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='Restaurant Image'>";
        echo "<div class='restaurant-info'>";
        echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
        echo "<p class='rating'>Rating: " . str_repeat("★", $row['rating']) . "</p>";
        echo "<p>Category: " . htmlspecialchars($row['category']) . "</p>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "</div>";

        // ✅ Corrected Add to Wishlist form
        if ($user_id) {
            echo "<form method='POST' action='wishlist.php' style='margin-left: auto;'>";
            echo "<input type='hidden' name='item_id' value='" . (int)$restaurant_id . "'>";
            echo "<input type='hidden' name='item_type' value='restaurant'>";
            echo "<input type='hidden' name='action' value='add'>";
            echo "<button type='submit' class='wishlist-btn'>Add to Wishlist</button>";
            echo "</form>";
        } else {
            echo "<div class='login-msg'>Log in to add to wishlist</div>";
        }

        $review_sql = "SELECT * FROM reviews WHERE item_type = 'restaurant' AND item_id = $restaurant_id AND status = 'approved' ORDER BY created_at DESC";
        $reviews_result = $conn->query($review_sql);

        echo "<div class='review-section'>";
        echo "<h4>Reviews:</h4>";
        if ($reviews_result && $reviews_result->num_rows > 0) {
            while ($review = $reviews_result->fetch_assoc()) {
                echo "<div class='review'>";
                echo "<strong>" . htmlspecialchars($review['name']) . "</strong> ";
                echo "<small>(" . date("M d, Y", strtotime($review['created_at'])) . ")</small><br>";
                echo "<span class='rating'>" . str_repeat("★", $review['rating']) . "</span><br>";
                echo "<p>" . htmlspecialchars($review['comment']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-review'>No reviews yet.</p>";
        }

        echo "<div class='review-form'>";
        echo "<form method='POST' action='submit_review.php'>";
        echo "<input type='hidden' name='item_type' value='restaurant'>";
        echo "<input type='hidden' name='item_id' value='" . $restaurant_id . "'>";
        echo "<label>Your Name:</label><input type='text' name='name' required>";
        echo "<label>Rating:</label><select name='rating' required>
                <option value=''>Select</option>
                <option value='1'>★☆☆☆☆</option>
                <option value='2'>★★☆☆☆</option>
                <option value='3'>★★★☆☆</option>
                <option value='4'>★★★★☆</option>
                <option value='5'>★★★★★</option>
              </select>";
        echo "<label>Comment:</label><textarea name='comment' required></textarea>";
        echo "<button type='submit'>Submit Review</button>";
        echo "</form></div>";

        echo "</div></div>"; // End review-section and restaurant-card
    }
} else {
    echo "<p>No approved restaurants found.</p>";
}
$conn->close();
?>

<!--  Review or Wishlist Popup -->
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
<div id="review-popup">✅ Review submitted successfully! It is pending approval.</div>
<script>
setTimeout(() => {
    const popup = document.getElementById('review-popup');
    if (popup) popup.style.display = 'none';
}, 4000);
</script>
<?php elseif (isset($_GET['wishlist_added'])): ?>
<style>
#review-popup {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #0077cc;
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    font-weight: 600;
    z-index: 10000;
    animation: fadeInOut 4s forwards;
    font-family: Arial, sans-serif;
}
</style>
<div id="review-popup">❤️ Added to wishlist!</div>
<script>
setTimeout(() => {
    const popup = document.getElementById('review-popup');
    if (popup) popup.style.display = 'none';
}, 4000);
</script>
<?php endif; ?>

</body>
</html>