<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f7f7f7, #eef1f5);
        }
        .wishlist-container {
            max-width: 1100px;
            margin: 50px auto;
        }
        .wishlist-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .wishlist-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .wishlist-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .wishlist-info h3 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .wishlist-info p {
            color: #6c757d;
        }
        .remove-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 6px;
            transition: background-color 0.25s ease, transform 0.2s ease;
        }
        .remove-btn:hover {
            background-color: #e63939;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<?php if (isset($_GET['message']) && !empty($_GET['message'])): ?>
    <div class="alert alert-info alert-dismissible fade show text-center" role="alert" style="margin: 20px;">
        <?php echo htmlspecialchars($_GET['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php include 'nav.php'; ?>

<div class="wishlist-container">
    <h1 class="text-center mb-5" data-aos="fade-up" style="font-weight: 700; color: #333;">Your Wishlist</h1>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $name = $row['hotel_name'] ?? $row['restaurant_name'] ?? $row['attraction_name'];
                $image = $row['hotel_img'] ?? $row['restaurant_img'] ?? $row['attraction_img'] ?? 'images/default.jpg';
                $type = ucfirst($row['item_type']);

                if ($row['item_type'] === 'hotel') {
                    $details_link = "hotel_details.php?id=" . $row['item_id'];
                } elseif ($row['item_type'] === 'restaurant') {
                    $details_link = "restaurant_details.php?id=" . $row['item_id'];
                } elseif ($row['item_type'] === 'attraction') {
                    $details_link = "attraction_details.php?id=" . $row['item_id'];
                } else {
                    $details_link = "#";
                }
            ?>
                <div class="col-md-6 col-lg-4" data-aos="zoom-in">
                    <div class="wishlist-card">
                        <a href="<?= htmlspecialchars($details_link) ?>">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                        </a>
                        <div class="p-3 wishlist-info">
                            <a href="<?= htmlspecialchars($details_link) ?>" style="text-decoration:none; color:inherit;">
                                <h3><?= htmlspecialchars($name) ?></h3>
                            </a>
                            <p>Type: <?= $type ?></p>
                        </div>
                        <div class="px-3 pb-3">
                            <form method="POST" action="wishlist.php" class="d-inline">
                                <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                                <input type="hidden" name="item_type" value="<?= $row['item_type'] ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="remove-btn w-100">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted" data-aos="fade-up">
                <p>You haven't added anything to your wishlist yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>
</body>
</html>
