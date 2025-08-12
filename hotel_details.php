<?php
session_start();
include 'db.php';
include 'nav.php';

if (!isset($_GET['id'])) {
    die("Hotel ID not provided.");
}
$hotel_id = intval($_GET['id']);

// Fetch hotel details
$sql = "SELECT * FROM hotels WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$hotel = $stmt->get_result()->fetch_assoc();

if (!$hotel) {
    die("Hotel not found.");
}

// Handle wishlist add
if (isset($_GET['action']) && $_GET['action'] == 'add_wishlist' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if already in wishlist
    $check_sql = "SELECT * FROM wishlists WHERE user_id = ? AND item_type = 'hotel' AND item_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $hotel_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Already added to wishlist!'); window.location.href='my_wishlist.php';</script>";
        exit;
    } else {
        $insert_sql = "INSERT INTO wishlists (user_id, item_type, item_id) VALUES (?, 'hotel', ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $hotel_id);
        $insert_stmt->execute();

        echo "<script>alert('Added to wishlist successfully!'); window.location.href='my_wishlist.php';</script>";
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
                          VALUES (?, 'hotel', ?, ?, ?, NOW())";
        $stmt_review = $conn->prepare($insert_review);
        $stmt_review->bind_param("iiis", $user_id, $hotel_id, $rating, $comment);
        $stmt_review->execute();
    }
    header("Location: hotel_details.php?id=" . $hotel_id);
    exit;
}

// Fetch reviews
$reviews_sql = "SELECT r.*, u.fullname 
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.item_type = 'hotel' AND r.item_id = ?
                ORDER BY r.created_at DESC";
$stmt_reviews = $conn->prepare($reviews_sql);
$stmt_reviews->bind_param("i", $hotel_id);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo htmlspecialchars($hotel['name']); ?></title>

    <!-- Bootstrap & AOS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        :root{
            --teal: #00a8a8;
            --teal-dark: #007f7f;
            --aqua: #dff8f8;
            --muted: #6c757d;
            --card-radius: 14px;
        }

        body {
            background: linear-gradient(180deg, #f3fbfb 0%, #ffffff 100%);
            color: #08343a;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        /* Hero area */
        .hotel-hero {
            position: relative;
            background: linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.25)), url('<?php echo htmlspecialchars($hotel['image_url'] ?? "images/default-hotel.jpg"); ?>') center/cover no-repeat;
            min-height: 44vh;
            display: flex;
            align-items: center;
            color: #fff;
            border-radius: 12px;
            margin-top: 1rem;
            overflow: hidden;
        }
        .hotel-hero .hero-overlay {
            padding: 40px;
            max-width: 900px;
        }
        .hotel-hero h1 { font-size: 2.2rem; text-shadow: 0 6px 22px rgba(0,0,0,0.45); }
        .hotel-hero p.lead { color: #e8f7f7; font-size: 1.05rem; }

        /* image modal thumb */
        .thumb-img { cursor: zoom-in; border-radius: 8px; box-shadow: 0 6px 20px rgba(0,0,0,0.12); }

        /* hotel info box */
        .info-box {
            background: white;
            padding: 18px;
            border-radius: var(--card-radius);
            box-shadow: 0 8px 30px rgba(2, 48, 48, 0.06);
        }

        /* stars */
        .stars { color: #f4b400; font-size: 1rem; }

        /* wishlist button */
        .wishlist-btn { background: var(--teal); border: none; color: white; border-radius: 999px; padding: 8px 14px; }
        .wishlist-btn:hover { background: var(--teal-dark); }

        /* reviews */
        .reviews-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .review-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 6px 18px rgba(2, 48, 48, 0.04);
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .review-card:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(2,48,48,0.08); }
        .review-meta { font-size: .9rem; color: var(--muted); margin-bottom: 8px; }

        /* "View more reviews" link */
        .view-more { cursor: pointer; color: var(--teal-dark); font-weight: 600; margin-top: 10px; }

        /* form */
        .review-form .form-control { border-radius: 10px; }
        .review-form .btn { border-radius: 10px; }

        /* footer */
        .site-footer {
            background: linear-gradient(90deg, #007f7f, #00a8a8);
            color: rgba(255,255,255,0.95);
            padding: 36px 0;
            margin-top: 40px;
        }
        .site-footer a { color: rgba(255,255,255,0.92); text-decoration: none; }
        .site-footer a:hover { text-decoration: underline; }

        /* responsive tweaks */
        @media (max-width: 767px) {
            .hotel-hero { min-height: 34vh; padding: 18px; }
            .hotel-hero h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HERO -->
    <div class="hotel-hero mt-4 rounded" data-aos="fade-up">
        <div class="hero-overlay">
            <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
            <p class="lead"><?php echo nl2br(htmlspecialchars($hotel['description'] ?? 'No description available.')); ?></p>

            <div class="d-flex gap-2 align-items-center mt-3">
                <?php if (!empty($hotel['website_url'])): ?>
                    <a href="<?php echo htmlspecialchars($hotel['website_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light me-2">
                        <i class="bi bi-box-arrow-up-right"></i> Visit Website
                    </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="hotel_details.php?id=<?php echo $hotel_id; ?>&action=add_wishlist" class="wishlist-btn"> <i class="bi bi-heart-fill me-1"></i> Add to Wishlist</a>
                <?php else: ?>
                    <a href="login.php" class="wishlist-btn"> <i class="bi bi-heart me-1"></i> Login to Add</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Details row -->
    <div class="row mt-4 g-4">
        <div class="col-lg-8" data-aos="fade-right">
            <div class="info-box">
                <div class="d-flex align-items-start gap-3">
                    <div style="min-width:140px;">
                        <?php if (!empty($hotel['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="Hotel Image" class="img-fluid thumb-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                        <?php else: ?>
                            <img src="images/default-hotel.jpg" alt="No image" class="img-fluid thumb-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                        <?php endif; ?>
                    </div>
                    <div class="flex-fill">
                        <h4 class="mb-1"><?php echo htmlspecialchars($hotel['name']); ?></h4>
                        <div class="mb-2 stars" aria-hidden="true">
                            <?php
                                $rt = isset($hotel['rating']) ? intval($hotel['rating']) : 0;
                                echo str_repeat('★', $rt) . str_repeat('☆', max(0, 5-$rt));
                            ?>
                            <span class="ms-2 text-muted" style="font-size:.95rem;"><?php echo htmlspecialchars($hotel['rating'] ?? 'No rating'); ?>/5</span>
                        </div>
                        <p class="text-muted mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location'] ?? '—'); ?></p>
                        <p class="text-muted mb-0"><strong>Category:</strong> <?php echo htmlspecialchars($hotel['category'] ?? '—'); ?></p>
                    </div>
                </div>

                <!-- small features or quick actions -->
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <a class="btn btn-outline-secondary btn-sm" href="#reviews">Jump to Reviews</a>
                    <a class="btn btn-outline-secondary btn-sm" href="hotels.php">Back to Listings</a>
                </div>
            </div>

            <!-- Reviews area -->
            <div id="reviews" class="mt-4">
                <h3 class="mb-3" data-aos="fade-up">Reviews</h3>

                <div class="reviews-grid" data-aos="fade-up" data-aos-delay="120">
                    <?php
                    $review_count = 0;
                    while ($review = $reviews_result->fetch_assoc()) {
                        $review_count++;
                        $hidden_class = $review_count > 3 ? 'd-none extra-review' : '';
                        // render review card
                        ?>
                        <div class="review-card <?php echo $hidden_class; ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong><?php echo htmlspecialchars($review['fullname']); ?></strong>
                                    <div class="review-meta"><?php echo htmlspecialchars(date("M d, Y", strtotime($review['created_at']))); ?></div>
                                </div>
                                <div class="text-end stars"><?php
                                    $r = intval($review['rating'] ?? 0);
                                    echo str_repeat('★', $r) . str_repeat('☆', max(0, 5-$r));
                                ?></div>
                            </div>
                            <div><?php echo nl2br(htmlspecialchars($review['comment'])); ?></div>
                        </div>
                    <?php } // endwhile ?>

                </div>

                <?php if ($review_count > 3): ?>
                    <p class="view-more" onclick="showMoreReviews()" data-aos="fade-up" data-aos-delay="220">View More Reviews</p>
                <?php endif; ?>
            </div>

        </div>

        <div class="col-lg-4" data-aos="fade-left">
            <!-- Sticky aside -->
            <div class="position-sticky" style="top:20px;">
                <!-- Quick facts card -->
                <div class="info-box mb-4">
                    <h5>Quick Facts</h5>
                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($hotel['phone'] ?? 'N/A'); ?></p>
                    <p class="mb-1"><strong>Website:</strong>
                        <?php if (!empty($hotel['website_url'])): ?>
                            <a href="<?php echo htmlspecialchars($hotel['website_url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo parse_url($hotel['website_url'], PHP_URL_HOST) ?? 'Visit' ?></a>
                        <?php else: echo '—'; endif; ?>
                    </p>
                    <p class="mb-0"><strong>Price Range:</strong> <?php echo htmlspecialchars($hotel['price_range'] ?? '—'); ?></p>
                </div>

                <!-- Tips card -->
                <div class="info-box mb-4">
                    <h6>Traveler Tips</h6>
                    <ul class="mb-0" style="padding-left: 1rem;">
                        <li>Book early for sunrise view rooms.</li>
                        <li>Carry a light jacket for evenings.</li>
                        <li>Check transport options to viewpoints.</li>
                    </ul>
                </div>

                <!-- Mini weather card (placeholder) -->
                <div class="info-box">
                    <h6>Weather</h6>
                    <div class="d-flex align-items-center">
                        <div style="font-size:2rem; margin-right:10px;">🌤</div>
                        <div>
                            <div><strong>Sunny</strong></div>
                            <div class="text-muted">22°C • Light breeze</div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">(Connect a live API later for real-time data)</small>
                </div>

            </div>
        </div>
    </div>

    <!-- Write a Review -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mt-5 review-form" data-aos="fade-up">
            <h4>Write a Review</h4>
            <form method="POST" action="hotel_details.php?id=<?php echo $hotel_id; ?>">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" required>
                            <option value="">Select</option>
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" name="submit_review" class="btn btn-success">Submit Review</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="mt-5 text-center" data-aos="fade-up">
            <p><a href="login.php" class="btn btn-outline-primary">Log in</a> to write a review and add to wishlist.</p>
        </div>
    <?php endif; ?>

</div> <!-- container end -->

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0">
        <?php if (!empty($hotel['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" class="img-fluid w-100 rounded" alt="Hotel Image">
        <?php else: ?>
            <img src="images/default-hotel.jpg" class="img-fluid w-100 rounded" alt="Default image">
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="site-footer mt-5">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold"><?php echo htmlspecialchars($hotel['name']); ?></h5>
                <p class="mb-0">A WayWander listing — Dhulikhel, Nepal</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Quick Links</h6>
                <p class="mb-0"><a href="index.php">Home</a> &nbsp; | &nbsp; <a href="hotels.php">Hotels</a></p>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Follow Us</h6>
                <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,0.12);" />
        <small>&copy; <?php echo date("Y"); ?> WayWander. All rights reserved.</small>
    </div>
</footer>

<!-- JS: Bootstrap, AOS, small helpers -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 900, once: true });

  function showMoreReviews() {
      document.querySelectorAll('.extra-review').forEach(el => el.classList.remove('d-none'));
      const vm = document.querySelector('.view-more');
      if (vm) vm.style.display = 'none';
      // Smooth scroll to first newly revealed review:
      const firstExtra = document.querySelector('.extra-review');
      if (firstExtra) firstExtra.scrollIntoView({behavior: 'smooth', block: 'center'});
  }
</script>

</body>
</html>
