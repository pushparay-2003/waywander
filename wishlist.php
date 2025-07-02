<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "❌ Please log in first.";
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'] ?? null;
$item_type = $_POST['item_type'] ?? null;
$action = $_POST['action'] ?? null;

if (!$item_id || !$item_type || !$action) {
    echo "❌ Missing data.";
    exit();
}

if ($action === 'add') {
    // Avoid duplicate
    $check = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND item_id = ? AND item_type = ?");
    $check->bind_param("iis", $user_id, $item_id, $item_type);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO wishlists (user_id, item_id, item_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $item_id, $item_type);
        $stmt->execute();
    }
} elseif ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND item_id = ? AND item_type = ?");
    $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    $stmt->execute();
}

// Redirect back
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $redirect");
exit();
?>
