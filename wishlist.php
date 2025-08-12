<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $item_type = $_POST['item_type'];
    $action = $_POST['action'];

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO wishlists (user_id, item_id, item_type, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $user_id, $item_id, $item_type);
        $stmt->execute();
        $stmt->close();

        header("Location: my_wishlist.php?message=Item added to wishlist");
        exit();

    } elseif ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND item_id = ? AND item_type = ?");
        $stmt->bind_param("iis", $user_id, $item_id, $item_type);
        $stmt->execute();
        $stmt->close();

        header("Location: my_wishlist.php");
        exit();
    }
}

// If accessed directly
header("Location: my_wishlist.php");
exit();
