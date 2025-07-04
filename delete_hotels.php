<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM hotels WHERE id=$id");

header("Location: admin_hotels.php");
exit();
