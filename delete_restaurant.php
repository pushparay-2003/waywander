<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM restaurants WHERE id=$id");

header("Location: admin_restaurants.php");
exit();
