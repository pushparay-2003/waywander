<?php
include 'db.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

function renderCard($row, $type, $conn) {
    echo "<div style='background:#fff; padding:20px; margin-bottom:20px; border-radius:8px;'>";
    echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
    echo "<p><strong>Category:</strong> " . ($row['category'] ?? '-') . "</p>";
    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
    echo "<p><strong>Rating:</strong> " . str_repeat("★", $row['rating'] ?? 0) . "</p>";

    // Fetch and display reviews
    $item_id = $row['id'];
    $review_sql = "SELECT * FROM reviews WHERE item_type = '$type' AND item_id = $item_id AND status = 'approved'";
    $review_result = $conn->query($review_sql);

    echo "<div style='background:#f9f9f9; padding:10px; margin-top:10px;'>";
    echo "<h4>Reviews:</h4>";
    if ($review_result->num_rows > 0) {
        while ($rev = $review_result->fetch_assoc()) {
            echo "<p><strong>" . htmlspecialchars($rev['name']) . "</strong> (" . $rev['rating'] . "★): " . htmlspecialchars($rev['comment']) . "</p>";
        }
    } else {
        echo "<p><i>No reviews yet.</i></p>";
    }
    echo "</div></div>";
}

echo "<h1>Search Results</h1>";

if ($query === '') {
    echo "<p>Please enter a search term.</p>";
    exit();
}

if ($type === 'hotel' || $type === '') {
    $sql = "SELECT * FROM hotels WHERE status = 'approved' AND name LIKE '%$query%'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        echo "<h2>Hotels</h2>";
        while ($row = $res->fetch_assoc()) renderCard($row, 'hotel', $conn);
    }
}

if ($type === 'restaurant' || $type === '') {
    $sql = "SELECT * FROM restaurants WHERE status = 'approved' AND name LIKE '%$query%'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        echo "<h2>Restaurants</h2>";
        while ($row = $res->fetch_assoc()) renderCard($row, 'restaurant', $conn);
    }
}

if ($type === 'attraction' || $type === '') {
    $sql = "SELECT * FROM attractions WHERE status = 'approved' AND name LIKE '%$query%'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        echo "<h2>Attractions</h2>";
        while ($row = $res->fetch_assoc()) renderCard($row, 'attraction', $conn);
    }
}

$conn->close();
?>
