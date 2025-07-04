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
    <img src="images/logo.png" alt="waywander" />
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

<section class="hero">
    <h1>Welcome to WayWander</h1>
    <p>Your guide to the best travel experiences in Dhulikhel</p>
    <a href="hotels.php" class="btn">Explore Hotels</a>
</section>

<?php
// Show wishlist only if user is logged in
if (isset($_SESSION['user_id'])) {
    include 'db.php';
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT w.*, 
                   h.name AS hotel_name, 
                   r.name AS restaurant_name, 
                   a.name AS attraction_name
            FROM wishlists w
            LEFT JOIN hotels h ON w.item_id = h.id AND w.item_type = 'hotel'
            LEFT JOIN restaurants r ON w.item_id = r.id AND w.item_type = 'restaurant'
            LEFT JOIN attractions a ON w.item_id = a.id AND w.item_type = 'attraction'
            WHERE w.user_id = $user_id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<section class='wishlist'><h2>Your Wishlist</h2>";
        while ($row = $result->fetch_assoc()) {
            $itemName = $row['hotel_name'] ?? $row['restaurant_name'] ?? $row['attraction_name'];
            $itemType = ucfirst($row['item_type']);
            echo "<div class='wishlist-card'>
                    <h3>$itemName</h3>
                    <p>Type: $itemType</p>
                  </div>";
        }
        echo "</section>";
    } else {
        echo "<section class='wishlist'><p>Your wishlist is empty.</p></section>";
    }
}
// If not logged in, no wishlist shown
?>

<section class="section">
    <h2>Featured Reviews</h2>

    <div class="review">
        <img src="imgages/dhulikhel.jpg" alt="Review 1">
        <h3>Hotel Paradise</h3>
        <p>"Amazing stay! The service was exceptional and the views were breathtaking."</p>
    </div>

    <div class="review">
        <img src="imges/dwarika.jpg" alt="Review 2">
        <h3>Mountain Adventure</h3>
        <p>"Perfect for thrill-seekers. An unforgettable hiking experience!"</p>
    </div>
</section>

<footer style="background-color: #333; color: white; padding: 40px 20px; text-align: center; font-family: Arial, sans-serif;">
    <div style="max-width: 1000px; margin: auto;">
        <h2 style="margin-bottom: 20px;">Contact Us</h2>
        <p>Email: <a href="mailto:info@waywander.com" style="color: #f4d03f;">info@waywander.com</a></p>
        <p>Phone: <a href="tel:+9771234567890" style="color: #f4d03f;">+977 1234567890</a></p>
        <p>Location: Dhulikhel, Kavrepalanchok, Nepal</p>

        <div style="margin-top: 20px;">
            <a href="#" style="margin: 0 10px; color: white; text-decoration: none;">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="#" style="margin: 0 10px; color: white; text-decoration: none;">
                <i class="fab fa-instagram"></i> Instagram
            </a>
            <a href="#" style="margin: 0 10px; color: white; text-decoration: none;">
                <i class="fab fa-twitter"></i> Twitter
            </a>
        </div>

        <div style="margin-top: 30px; font-size: 14px; color: #ccc;">
            &copy; 2025 WayWander. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
