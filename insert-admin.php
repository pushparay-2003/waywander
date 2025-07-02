<?php
include 'db.php';

$email = "admin@example.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);

$sql = "INSERT INTO admin_users (email, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();

echo "✅ Admin added successfully!";
?>
