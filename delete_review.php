<?php
session_start();
include 'db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM reviews WHERE id=$id");
header("Location: admin_reviews.php");
exit();
