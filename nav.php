<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login at the very top for wishlist usage
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>WayWander | Travel and Reviews</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        header {
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            border-bottom: 1px solid #ccc;
        }
        header img {
            height: 40px;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        form.search-bar {
            display: flex;
            gap: 10px;
        }
        form.search-bar input[type="text"],
        form.search-bar select {
            padding: 8px;
            font-size: 14px;
        }
        form.search-bar button {
            background-color: #ff5a5f;
            color: white;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
        }

        .hero {
            background-color: #f3f3f3;
            text-align: center;
            padding: 100px 20px;
        }
        .hero h1 {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .hero .btn {
            padding: 10px 25px;
            background-color: #ff5a5f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
        }
        .section {
            padding: 60px 40px;
            text-align: center;
        }
        .section h2 {
            font-size: 26px;
            margin-bottom: 30px;
        }
        .review {
            max-width: 600px;
            margin: 0 auto 40px;
        }
        .review img {
            height: 80px;
            border-radius: 5px;
        }
        .review h3 {
            margin-top: 10px;
        }
        .review p {
            color: #555;
        }

        /* Wishlist styling */
        .wishlist {
            padding: 40px;
            text-align: center;
        }
        .wishlist-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            margin: 10px auto;
            max-width: 400px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<header>
    <img src="img/logo.png" alt="waywander" />
    <nav>
        <a href="index.php">Home</a> <!-- Updated to .php if needed -->
        <a href="hotels.php">Hotels</a>
        <a href="attractions.php">Attractions</a>
        <a href="resturants.php">Restaurants</a>
        <a href="my_wishlist.php">Wishlist</a> <!-- Wishlist link -->
        <a href="account.html">Account</a>
    </nav>

    <form class="search-bar" action="search_results.php" method="GET">
        <input type="text" name="query" placeholder="Search..." required>
        <select name="type">
            <option value="">All</option>
            <option value="hotel">Hotels</option>
            <option value="restaurant">Restaurants</option>
            <option value="attraction">Attractions</option>
        </select>
        <button type="submit">Search</button>
    </form>
</header>
</body>