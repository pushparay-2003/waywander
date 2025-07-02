<?php
session_start();
include 'db.php';
$id = $_GET['id'];
$type = $_GET['type'];

$table = $type === 'restaurant' ? 'restaurants' : 'hotels';
$conn->query("UPDATE $table SET status='approved' WHERE id=$id");
header("Location: admin_pending.php");
exit();
