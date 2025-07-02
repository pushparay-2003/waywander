<?php
session_start();
include 'db.php';
$id = $_GET['id'];
$type = $_GET['type'];

$table = $type === 'restaurant' ? 'restaurants' : 'hotels';
$conn->query("DELETE FROM $table WHERE id=$id");
header("Location: admin_pending.php");
exit();
