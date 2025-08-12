<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = intval($_POST['item_id']);
    $user_id = $_SESSION['user_id']; // Make sure user is logged in
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO reviews (item_id, user_id, rating, comment, status) VALUES (?, ?, ?, ?, 'approved')");
    $stmt->bind_param("iiis", $item_id, $user_id, $rating, $comment);

    if ($stmt->execute()) {
        // Redirect or show success message
        header("Location: some_page.php?msg=Review submitted");
        exit();
    } else {
        echo "Error submitting review.";
    }
}
?>
