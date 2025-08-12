<?php
include 'db.php';
include 'nav.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

function renderCard($row, $type, $conn) {
    echo "<div style='background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; max-width:900px; margin-left:auto; margin-right:auto;'>";

    // Image display
    if (!empty($row['image'])) {
        echo "<img src='images/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' style='width:100%; height:250px; object-fit:cover;'>";
    } else {
        echo "<img src='images/placeholder.jpg' alt='No image' style='width:100%; height:250px; object-fit:cover;'>";
    }

    echo "<div style='padding:20px;'>";
    echo "<h2 style='margin-top:0; font-size:1.8em; color:#333;'>" . htmlspecialchars($row['name']) . "</h2>";
    echo "<p style='color:#777; margin:5px 0;'><strong>Category:</strong> " . ($row['category'] ?? '-') . "</p>";
    echo "<p style='line-height:1.6;'>" . htmlspecialchars($row['description']) . "</p>";
    echo "<p style='color:#ff9800; font-size:1.2em;'><strong>Rating:</strong> " . str_repeat("★", $row['rating'] ?? 0) . "</p>";

    // Reviews
    $item_id = $row['id'];
    $review_sql = "SELECT * FROM reviews WHERE item_type = '$type' AND item_id = $item_id AND status = 'approved'";
    $review_result = $conn->query($review_sql);

    echo "<div style='background:#f9f9f9; padding:10px; margin-top:15px; border-radius:5px;'>";
    echo "<h4 style='margin:0 0 10px;'>Reviews:</h4>";
    if ($review_result->num_rows > 0) {
        while ($rev = $review_result->fetch_assoc()) {
            echo "<p style='margin:5px 0;'><strong>" . htmlspecialchars($rev['name']) . "</strong> (" . $rev['rating'] . "★): " . htmlspecialchars($rev['comment']) . "</p>";
        }
    } else {
        echo "<p style='margin:0;'><i>No reviews yet.</i></p>";
    }
    echo "</div>";

    echo "</div></div>";
}

echo "<div style='max-width:1000px; margin:auto; padding:20px;'>";
echo "<h1 style='text-align:center; margin-bottom:30px;'>Search Results</h1>";

if ($query === '') {
    echo "<p>Please enter a search term.</p>";
    echo "</div>";
    exit();
}

$foundResults = false;

if ($type === 'hotel' || $type === '') {
    $sql = "SELECT * FROM hotels WHERE status = 'approved' AND name LIKE '%$query%'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        echo "<h2 style='margin-top:40px;'>Hotels</h2>";
        while ($row = $res->fetch_assoc()) {
            renderCard($row, 'hotel', $conn);
            $foundResults = true;
        }
    }
}

if ($type === 'restaurant' || $type === '') {
    $sql = "SELECT * FROM restaurants WHERE status = 'approved' AND name LIKE '%$query%'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        echo "<h2 style='margin-top:40px;'>Restaurants</h2>";
        while ($row = $res->fetch_assoc()) {
            renderCard($row, 'restaurant', $conn);
            $foundResults = true;
        }
    }
}

if ($type === 'attraction' || $type === '') {
    $sql = "SELECT * FROM attractions WHERE status = 'approved' AND name LIKE '%$query%'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        echo "<h2 style='margin-top:40px;'>Attractions</h2>";
        while ($row = $res->fetch_assoc()) {
            renderCard($row, 'attraction', $conn);
            $foundResults = true;
        }
    }
}

if (!$foundResults) {
    echo "<p style='text-align:center; font-size:1.2em; color:#888;'>No results found for '<strong>" . htmlspecialchars($query) . "</strong>'.</p>";
}

echo "</div>";
$conn->close();
?>
