<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $item_type = $_POST['item_type']; // 'restaurant' or 'hotel'
    $item_id = $_POST['item_id'];     // the ID of that restaurant/hotel

    $stmt = $conn->prepare("INSERT INTO reviews (name, rating, comment, item_type, item_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sissi", $name, $rating, $comment, $item_type, $item_id);

    if ($stmt->execute()) {
        echo "Thank you! Your review is pending approval.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
