<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to view your wishlist.";
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT w.*, 
               h.name AS hotel_name, 
               r.name AS restaurant_name, 
               a.name AS attraction_name
        FROM wishlists w
        LEFT JOIN hotels h ON w.item_id = h.id AND w.item_type = 'hotel'
        LEFT JOIN restaurants r ON w.item_id = r.id AND w.item_type = 'restaurant'
        LEFT JOIN attractions a ON w.item_id = a.id AND w.item_type = 'attraction'
        WHERE w.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>My Wishlist</h2>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = $row['hotel_name'] ?? $row['restaurant_name'] ?? $row['attraction_name'];
        $type = ucfirst($row['item_type']);
        $item_id = $row['item_id'];
        $item_type = $row['item_type'];

        echo "<div style='margin-bottom: 10px;'>
                <strong>$name</strong> ($type)

                <form method='POST' action='wishlist.php' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='item_id' value='$item_id'>
                    <input type='hidden' name='item_type' value='$item_type'>
                    <input type='hidden' name='action' value='remove'>
                    <button type='submit' style='background:red; color:white; border:none; padding:5px 10px; border-radius:3px;'>🗑 Remove</button>
                </form>
              </div>";
    }
} else {
    echo "<p>Your wishlist is empty.</p>";
}
?>
