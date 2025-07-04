<?php
session_start();
include 'db.php';
include 'nav.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build SQL query with filters
$sql = "SELECT * FROM hotels WHERE status = 'approved'";
if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $sql .= " AND (name LIKE '%$search_safe%' OR location LIKE '%$search_safe%')";
}
if (!empty($category)) {
    $category_safe = $conn->real_escape_string($category);
    $sql .= " AND category = '$category_safe'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dhulikhel Hotels | WayWander</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .hotel-card { background: #fff; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 40px; }
        .hotel-card img { width: 100%; max-width: 400px; height: auto; border-radius: 6px; }
        .rating { color: orange; font-size: 18px; }
        .review-section { background: #f9f9f9; padding: 15px; margin-top: 15px; border-left: 4px solid orange; border-radius: 4px; }
        .review { padding: 8px 0; border-bottom: 1px solid #ddd; }
        .review:last-child { border-bottom: none; }
        .review-form { margin-top: 15px; padding-top: 10px; border-top: 1px solid #ccc; }
        .review-form input, .review-form select, .review-form textarea { width: 100%; margin-top: 8px; padding: 8px; }
        .review-form button { margin-top: 10px; padding: 10px; background-color: orange; border: none; color: white; cursor: pointer; }
        .no-review { font-style: italic; color: #888; }
        .filter-box { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 0 6px rgba(0,0,0,0.1); }
        .filter-box input, .filter-box select { padding: 8px; margin-right: 10px; }
        .container { max-width: 1200px; margin: auto; padding: 20px; }
    </style>
</head>
<body>

<div class="container">
<h1>Hotels in Dhulikhel</h1>

<!-- Filter/Search Section -->
<div class="filter-box">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by name or location" value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Budget" <?= $category == "Budget" ? "selected" : "" ?>>Budget</option>
            <option value="Luxury" <?= $category == "Luxury" ? "selected" : "" ?>>Luxury</option>
            <option value="Resort" <?= $category == "Resort" ? "selected" : "" ?>>Resort</option>
            <option value="Family" <?= $category == "Family" ? "selected" : "" ?>>Family</option>
            <option value="Eco" <?= $category == "Eco" ? "selected" : "" ?>>Eco</option>
        </select>
        <button type="submit">Search</button>
    </form>
</div>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotel_id = $row['id'];

        echo "<div class='hotel-card'>";
        echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='Hotel Image'>";
        echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
        echo "<p class='rating'>Rating: " . str_repeat("★", $row['rating']) . "</p>";
        echo "<p>Category: " . htmlspecialchars($row['category']) . "</p>";
        echo "<p>Location: " . htmlspecialchars($row['location']) . "</p>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";

        // ✅ Add to Wishlist Button
        if (isset($_SESSION['user_id'])) {
            echo "<form method='POST' action='wishlist.php' style='margin-top: 10px;'>";
            echo "<input type='hidden' name='item_id' value='" . $hotel_id . "'>";
            echo "<input type='hidden' name='item_type' value='hotel'>";
            echo "<input type='hidden' name='action' value='add'>";
            echo "<button type='submit' style='background-color:#ff5a5f; color:white; border:none; padding:8px 12px; border-radius:4px;'>❤️ Add to Wishlist</button>";
            echo "</form>";
        } else {
            echo "<p><a href='account.html'>Log in</a> to add to wishlist.</p>";
        }

        // ✅ Reviews
        $review_sql = "SELECT * FROM reviews WHERE item_type = 'hotel' AND item_id = $hotel_id AND status = 'approved' ORDER BY created_at DESC";
        $reviews_result = $conn->query($review_sql);

        echo "<div class='review-section'><h4>Reviews:</h4>";
        if ($reviews_result->num_rows > 0) {
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

        // ✅ Review Form
        echo "<div class='review-form'>";
        echo "<form method='POST' action='submit_review.php'>";
        echo "<input type='hidden' name='item_type' value='hotel'>";
        echo "<input type='hidden' name='item_id' value='" . $hotel_id . "'>";
        echo "<label>Your Name:</label><input type='text' name='name' required>";
        echo "<label>Rating:</label>
              <select name='rating' required>
                <option value=''>Select</option>
                <option value='1'>★☆☆☆☆</option>
                <option value='2'>★★☆☆☆</option>
                <option value='3'>★★★☆☆</option>
                <option value='4'>★★★★☆</option>
                <option value='5'>★★★★★</option>
              </select>";
        echo "<label>Comment:</label><textarea name='comment' required></textarea>";
        echo "<button type='submit'>Submit Review</button>";
        echo "</form></div>"; // review-form
        echo "</div></div>"; // review-section and hotel-card
    }
} else {
    echo "<p>No approved hotels available.</p>";
}
$conn->close();
?>

<!-- ✅ Pop-up Confirmation -->
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
<div id="review-popup">✅ Review submitted successfully!</div>
<script>
    setTimeout(() => {
        const popup = document.getElementById('review-popup');
        if (popup) popup.style.display = 'none';
    }, 4000);
</script>
<?php endif; ?>

</div> <!-- end container -->
</body>
</html>
