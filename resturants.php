<?php
session_start();
include 'db.php';

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
    header("Location: restaurants.php");
    exit;
}

// Get filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$rating = isset($_GET['rating']) ? $_GET['rating'] : '';

// Build SQL query
$sql = "SELECT * FROM restaurants WHERE status = 'approved'";

if ($search !== '') {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
if ($category !== '') {
    $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
}
if ($rating !== '') {
    $sql .= " AND rating = " . intval($rating);
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Dhulikhel Restaurants | WayWander</title>
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
        /* Wishlist button styling */
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

<!-- ✅ Navbar HTML -->
<header>
    <div class="logo">
        <a href="index.php"><img src="img/logo.png" alt="waywander"></a>
    </div>

    <nav>
        <ul class="navmenu">
            <li><a href="index.php">Home</a></li>
            <li><a href="hotels.php">Hotels</a></li>
            <li><a href="attractions.php">Attractions</a></li>
            <li><a href="resturants.php">Restaurants</a></li>
            <li><a href="my_wishlist.php">Wishlist</a></li>
            <li><a href="account.html">Account</a></li>
        </ul>
    </nav>

    <!-- ✅ Search Bar -->
    <form class="search-bar" action="search_results.php" method="GET">
        <input type="text" name="query" placeholder="Search..." required>
        <select name="type">
            <option value="">All</option>
            <option value="hotel">Hotels</option>
            <option value="restaurant">Restaurants</option>
            <option value="attraction">Attractions</option>
        </select>
        <button type="submit">Search</button>
    </form>
</header>


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

        // Add to Wishlist button or login prompt
        if ($user_id) {
            echo "<form method='POST' style='margin-left: auto;'>";
            echo "<input type='hidden' name='item_id' value='" . (int)$restaurant_id . "'>";
            echo "<input type='hidden' name='item_type' value='restaurant'>";
            echo "<button type='submit' class='wishlist-btn'>Add to Wishlist</button>";
            echo "</form>";
        } else {
            echo "<div class='login-msg'>Log in to add to wishlist</div>";
        }

        // Show Reviews
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

        // Review Form
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

</body>
</html>
