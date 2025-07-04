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
        // Redirect back to referring page with success param
        $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        $sep = (parse_url($redirect_url, PHP_URL_QUERY) == NULL) ? '?' : '&';
        header("Location: {$redirect_url}{$sep}review_submitted=1");
        exit();
    } else {
        // Optional: Redirect back with error param or show error message
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
