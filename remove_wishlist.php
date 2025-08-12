<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'], $_POST['item_id'], $_POST['item_type'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $item_type = $_POST['item_type'];

    $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND item_id = ? AND item_type = ?");
    $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    $stmt->execute();
}

header("Location: my_wishlist.php"); // or "my_wishlist.php" if that's where the request came from
exit();
