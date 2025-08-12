<?php
session_start();
include 'db.php';
include 'nav.php';

if (!isset($_GET['id'])) {
    die("Restaurant ID not provided.");
}
$restaurant_id = intval($_GET['id']);

// Fetch restaurant details
$sql = "SELECT * FROM restaurants WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();

if (!$restaurant) {
    die("Restaurant not found.");
}

// Handle wishlist (favorites) add
if (isset($_GET['action']) && $_GET['action'] == 'add_favorite' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if already in wishlist
    $check_sql = "SELECT * FROM wishlists WHERE user_id = ? AND item_type = 'restaurant' AND item_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $restaurant_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Already in favorites!'); window.location.href='my_wishlist.php';</script>";
        exit;
    } else {
        $insert_sql = "INSERT INTO wishlists (user_id, item_type, item_id) VALUES (?, 'restaurant', ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $restaurant_id);
        $insert_stmt->execute();

        echo "<script>alert('Added to favorites successfully!'); window.location.href='my_wishlist.php';</script>";
        exit;
    }
}

// Handle review submission
if (isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($rating > 0 && !empty($comment)) {
        $insert_review = "INSERT INTO reviews (user_id, item_type, item_id, rating, comment, created_at) 
                          VALUES (?, 'restaurant', ?, ?, ?, NOW())";
        $stmt_review = $conn->prepare($insert_review);
        $stmt_review->bind_param("iiis", $user_id, $restaurant_id, $rating, $comment);
        $stmt_review->execute();
    }
    header("Location: restaurant_details.php?id=" . $restaurant_id);
    exit;
}

// Fetch reviews
$reviews_sql = "SELECT r.*, u.fullname 
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.item_type = 'restaurant' AND r.item_id = ?
                ORDER BY r.created_at DESC";
$stmt_reviews = $conn->prepare($reviews_sql);
$stmt_reviews->bind_param("i", $restaurant_id);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($restaurant['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <style>
        /* Body & general */
        body {
            background-color: #e6f7f7; /* softer aqua background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #004d4d;
        }

        /* Hero Section with Parallax & Overlay */
        .hero-section {
            position: relative;
            height: 60vh;
            background: url('<?php echo htmlspecialchars($restaurant['image_url']); ?>') center/cover fixed no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.7);
        }
        .hero-overlay {
            background: rgba(0, 77, 77, 0.65);
            padding: 3rem 2rem;
            border-radius: 15px;
            max-width: 700px;
            box-shadow: 0 8px 25px rgba(0, 77, 77, 0.5);
            backdrop-filter: saturate(180%) blur(8px);
        }
        .hero-overlay h1 {
            font-weight: 900;
            font-size: 3.2rem;
            margin-bottom: 0.4rem;
            letter-spacing: 1.5px;
        }
        .hero-overlay p {
            font-size: 1.5rem;
            font-weight: 600;
            color: #b2dfdb;
        }

        /* Content container */
        .container {
            max-width: 900px;
        }

        /* Description styling */
        .description {
            font-size: 1.125rem;
            line-height: 1.7;
            margin-bottom: 2rem;
            color: #004d4ddd;
        }

        /* Quick facts, weather, travel tips cards container */
        .info-cards {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        .info-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 77, 77, 0.1);
            flex: 1 1 280px;
            padding: 1.5rem 1.75rem;
            color: #004d4d;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 77, 77, 0.25);
        }
        .info-card h4 {
            color: #00796b;
            font-weight: 700;
            margin-bottom: 0.75rem;
            border-bottom: 2px solid #004d4d;
            padding-bottom: 0.3rem;
        }
        .info-card p, .info-card ul {
            font-size: 1rem;
            line-height: 1.5;
            margin: 0;
            padding-left: 1rem;
        }

        /* Buttons styling */
        .btn-info {
            background-color: #009688;
            border-color: #00796b;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-info:hover {
            background-color: #00796b;
            border-color: #004d40;
        }
        .btn-warning {
            background-color: #ffb300;
            border-color: #ffa000;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #ffa000;
            border-color: #ff6f00;
        }
        .btn-success {
            background-color: #009688;
            border-color: #00796b;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #00796b;
            border-color: #004d40;
        }

        /* Reviews Section */
        h3 {
            font-weight: 700;
            color: #00695c;
            margin-bottom: 1.5rem;
        }
        .card-review {
            background: #ffffffcc;
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 77, 77, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 1.5rem;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-review:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 77, 77, 0.3);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .review-header h5 {
            font-weight: 700;
            color: #004d4d;
            margin: 0;
        }
        .badge-rating {
            background-color: #009688;
            font-size: 1rem;
            font-weight: 700;
            padding: 0.45em 0.85em;
            border-radius: 12px;
            color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .card-review p {
            flex-grow: 1;
            font-size: 1rem;
            color: #004d4ddd;
            white-space: pre-wrap;
        }

        /* View More Reviews button */
        .view-more {
            margin-top: 1rem;
            font-weight: 700;
            color: #00796b;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
            text-align: center;
        }
        .view-more:hover {
            color: #004d40;
            text-decoration: underline;
        }

        /* Review Form */
        h4 {
            margin-top: 3rem;
            font-weight: 700;
            color: #004d4d;
        }
        form label {
            font-weight: 600;
            margin-top: 1rem;
            color: #00695c;
        }
        form select, form textarea {
            border-radius: 10px;
            border: 1.5px solid #009688;
            font-size: 1rem;
            padding: 0.5rem;
            transition: border-color 0.3s ease;
        }
        form select:focus, form textarea:focus {
            outline: none;
            border-color: #004d40;
            box-shadow: 0 0 8px #004d40aa;
        }
        form button[type="submit"] {
            background-color: #009688;
            border: none;
            font-weight: 700;
            margin-top: 1.5rem;
            padding: 0.7rem 2rem;
            border-radius: 30px;
            color: white;
            transition: background-color 0.3s ease;
        }
        form button[type="submit"]:hover {
            background-color: #00796b;
        }

        /* Full width footer */
        footer {
            background: #004d4d;
            color: white;
            padding: 25px 0;
            text-align: center;
            font-weight: 600;
            letter-spacing: 1.2px;
            margin-top: 4rem;
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            box-shadow: 0 -5px 15px rgba(0, 77, 77, 0.4);
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .hero-overlay h1 {
                font-size: 2.2rem;
            }
            .hero-overlay p {
                font-size: 1.1rem;
            }
            .card-review {
                min-height: auto;
            }
            .info-cards {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<!-- Parallax Hero -->
<section class="hero-section" data-aos="fade-down" data-aos-duration="1500">
    <div class="hero-overlay">
        <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
        <p><?php echo htmlspecialchars($restaurant['rating']); ?>/5 Rating</p>
    </div>
</section>

<div class="container py-5" data-aos="fade-up" data-aos-duration="1200">

    <div class="description mb-4">
        <?php echo nl2br(htmlspecialchars($restaurant['description'])); ?>
    </div>

    <!-- New Info Cards: Quick Facts, Weather, Travel Tips -->
    <div class="info-cards" data-aos="fade-up" data-aos-delay="300">
        <div class="info-card">
            <h4>Quick Facts</h4>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['location'] ?? 'N/A'); ?></p>
            <p><strong>Price Range:</strong> <?php echo htmlspecialchars($restaurant['price_range'] ?? 'N/A'); ?></p>
            <p><strong>Opening Hours:</strong> <?php echo htmlspecialchars($restaurant['opening_hours'] ?? '9:00 AM - 10:00 PM'); ?></p>
        </div>
        <div class="info-card">
            <h4>Weather</h4>
            <p>Average Temperature: 22°C</p>
            <p>Best Time to Visit: October - March</p>
            <p>Forecast: Sunny with occasional showers</p>
            <!-- You can integrate real API data here later -->
        </div>
        <div class="info-card">
            <h4>Travel Tips</h4>
            <ul>
                <li>Reserve tables in advance</li>
                <li>Try the local specialties</li>
                <li>Check for dress code</li>
                <li>Visit during off-peak hours</li>
            </ul>
        </div>
    </div>

    <?php if (!empty($restaurant['website_url'])): ?>
        <a href="<?php echo htmlspecialchars($restaurant['website_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-info me-3 mb-3" data-aos="zoom-in" data-aos-delay="100">
            Visit Website
        </a>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="restaurant_details.php?id=<?php echo $restaurant_id; ?>&action=add_favorite" class="btn btn-warning favorite-btn mb-3" data-aos="zoom-in" data-aos-delay="200">Add to Favorites</a>
    <?php else: ?>
        <a href="login.php" class="btn btn-warning favorite-btn mb-3" data-aos="zoom-in" data-aos-delay="200">Login to Add to Favorites</a>
    <?php endif; ?>

    <hr>

    <!-- Reviews Section -->
    <h3 data-aos="fade-up">Traveler Reviews</h3>
    <div class="row g-4">
    <?php
    $review_count = 0;
    while ($review = $reviews_result->fetch_assoc()) {
        $review_count++;
        $hidden_class = $review_count > 3 ? 'd-none extra-review' : '';
        ?>
        <div class="col-md-6 <?php echo $hidden_class; ?>" data-aos="zoom-in" data-aos-delay="<?php echo 100 + ($review_count * 100); ?>">
            <div class="card-review">
                <div class="review-header">
                    <h5><?php echo htmlspecialchars($review['fullname']); ?></h5>
                    <span class="badge-rating"><?php echo htmlspecialchars($review['rating']); ?>/5</span>
                </div>
                <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
            </div>
        </div>
    <?php } ?>
    </div>

    <?php if ($review_count > 3): ?>
        <p class="view-more" onclick="showMoreReviews()">View More Reviews</p>
    <?php endif; ?>

    <!-- Review Form -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <hr />
        <h4 data-aos="fade-right" data-aos-duration="1000">Write a Review</h4>
        <form method="POST" data-aos="fade-up" data-aos-duration="1000">
            <label for="rating">Rating:</label>
            <select id="rating" name="rating" class="form-select" required>
                <option value="">Select Rating</option>
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <label for="comment" class="mt-3">Comment:</label>
            <textarea id="comment" name="comment" class="form-control" rows="4" required></textarea>
            <button type="submit" name="submit_review" class="btn btn-success">Submit Review</button>
        </form>
    <?php endif; ?>

</div>

<!-- Footer -->
<footer>
    <p>&copy; <?php echo date('Y'); ?> Travel Planner | Explore the world with ease</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    function showMoreReviews() {
        document.querySelectorAll('.extra-review').forEach(el => el.classList.remove('d-none'));
        document.querySelector('.view-more').style.display = 'none';
    }
</script>

</body>
</html>
