<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM attractions WHERE id=$id");

header("Location: admin_attractions.php");
exit();
