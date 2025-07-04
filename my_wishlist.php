<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Or account.html
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items (restaurants, hotels, attractions)
$sql = "SELECT w.*, 
               h.name AS hotel_name, h.image_url AS hotel_img,
               r.name AS restaurant_name, r.image_url AS restaurant_img,
               a.name AS attraction_name, a.image_url AS attraction_img
        FROM wishlists w
        LEFT JOIN hotels h ON w.item_type = 'hotel' AND w.item_id = h.id
        LEFT JOIN restaurants r ON w.item_type = 'restaurant' AND w.item_id = r.id
        LEFT JOIN attractions a ON w.item_type = 'attraction' AND w.item_id = a.id
        WHERE w.user_id = $user_id
        ORDER BY w.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wishlist | WayWander</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .wishlist-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .wishlist-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .wishlist-card {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .wishlist-card img {
            width: 200px;
            height: 130px;
            object-fit: cover;
        }
        .wishlist-info {
            padding: 15px 20px;
            flex: 1;
        }
        .wishlist-info h3 {
            margin: 0;
            font-size: 20px;
        }
        .wishlist-info p {
            color: #777;
            margin: 8px 0 0;
        }
        .wishlist-actions {
            padding-right: 20px;
        }
        .wishlist-actions form {
            display: inline;
        }
        .wishlist-actions button {
            background-color: #ccc;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .wishlist-actions button:hover {
            background-color: #bbb;
        }

        @media screen and (max-width: 600px) {
            .wishlist-card {
                flex-direction: column;
            }
            .wishlist-card img {
                width: 100%;
                height: auto;
            }
            .wishlist-info {
                padding: 15px;
            }
            .wishlist-actions {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="wishlist-container">
    <h1>Your Wishlist</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()):
            $name = $row['hotel_name'] ?? $row['restaurant_name'] ?? $row['attraction_name'];
            $image = $row['hotel_img'] ?? $row['restaurant_img'] ?? $row['attraction_img'] ?? 'images/default.jpg';
            $type = ucfirst($row['item_type']);
        ?>
            <div class="wishlist-card">
                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                <div class="wishlist-info">
                    <h3><?= htmlspecialchars($name) ?></h3>
                    <p>Type: <?= $type ?></p>
                </div>
                <div class="wishlist-actions">
                    <form method="POST" action="wishlist.php">
                        <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                        <input type="hidden" name="item_type" value="<?= $row['item_type'] ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit"> Remove</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color: #777;">You haven't added anything to your wishlist yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
