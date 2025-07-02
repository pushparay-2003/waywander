<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | WayWander</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional: Your existing styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background: #f4f4f4;
        }
        .dashboard-header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .dashboard-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 40px 20px;
            gap: 20px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(210, 148, 148, 0.1);
            transition: 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        .card h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            text-decoration: none;
            background-color: #ff5a5f;
            color: white;
            border-radius: 4px;
        }
        .logout {
            margin-top: 30px;
            text-align: center;
        }
        .logout a {
            color: #ff5a5f;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="dashboard-header">
        <h1>Welcome, Admin</h1>
    </div>

    <div class="dashboard-container">
        <div class="card">
            <h3>Manage Restaurants</h3>
            <a href="admin_restaurants.php">View/Edit</a><br>
            <a href="add_resturant.php">Add New</a>
        </div>
        <div class="card">
            <h3>Manage Hotels</h3>
            <a href="admin_hotels.php">View/Edit</a><br>
            <a href="add_hotels.php">Add New</a>
        </div>
        <div class="card">
            <h3>Manage Attractions</h3>
            <a href="admin_attractions.php">View/Edit</a><br>
            <a href="add_attractions.php">Add New</a>
        </div>
        <div class="card">
            <h3>Pending Submissions</h3>
            <a href="admin_pending.php">Approve/Reject</a>
        </div>
        <div class="card">
            <h3>Manage Reviews</h3>
            <a href="admin_reviews.php">View/Delete</a>
        </div>
    </div>

    <div class="logout">
        <a href="admin-logout.php">Logout</a>
    </div>

</body>
</html>
