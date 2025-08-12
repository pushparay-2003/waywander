<?php
session_start();
include 'db.php';
include 'nav.php';

// Fetch restaurants
$sql = "SELECT * FROM restaurants ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
    <style>
        /* Parallax Hero */
        .hero {
            position: relative;
            background-image: url('images/hotel.jpg'); /* Replace with your hotel image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        .hero-overlay {
            background: rgba(0,0,0,0.4);
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .scroll-down {
            position: absolute;
            bottom: 20px;
            font-size: 2rem;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .card-img-top {
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Sections */
        section {
            padding: 60px 0;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 40px;
            text-align: center;
        }

        /* Footer */
        footer {
            background: #111;
            color: #ccc;
            padding-top: 50px;
        }
        footer a { color: #ccc; }
        footer a:hover { color: white; }
        /* Smooth scroll between sections */
html {
    scroll-behavior: smooth;
}

/* Optional: Subtle fade when scrolling */
[data-aos] {
    transition: all 0.8s ease;
}

/* Parallax overlay effect */
.parallax-food, .parallax-food2 {
    position: relative;
}

.parallax-food::before, .parallax-food2::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.3);
}

    </style>
</head>
<body>

<!-- Hero -->
<div class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="zoom-in" data-aos-duration="1200">
        <h1 class="display-4 fw-bold">Discover Dhulikhel's Flavors</h1>
        <p class="lead">From street-side delicacies to fine dining under the Himalayas</p>
    </div>
    <a href="#restaurants" class="scroll-down text-white"><i class="bi bi-chevron-double-down"></i></a>
</div>

<!-- Restaurants Section -->
<section id="restaurants" class="bg-light">
    <h2 class="section-title" data-aos="fade-up">🍽 Featured Restaurants</h2>
    <div class="container">
        <div class="row g-4">
            <?php while ($restaurant = $result->fetch_assoc()): ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card shadow-sm">
                        <?php if (!empty($restaurant['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" class="card-img-top" alt="Restaurant Image">
                        <?php else: ?>
                            <img src="images/default-restaurant.jpg" class="card-img-top" alt="Default Restaurant Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo nl2br(htmlspecialchars(substr($restaurant['description'], 0, 100))); ?>...
                            </p>
                            <p><strong>⭐ Rating:</strong> 
                                <?php echo isset($restaurant['rating']) ? htmlspecialchars($restaurant['rating']) : 'No rating'; ?>/5
                            </p>
                            <a href="restaurant_details.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Blog Section with Parallax Background -->
<section 
    class="parallax-food" 
    style="background-image: url('images/newari food.jpg'); background-attachment: fixed; background-size: cover; background-position: center; color: white; padding: 60px 0;">
    <div class="container" style="background: rgba(0,0,0,0.4); border-radius: 10px; padding: 30px;">
        <h2 class="section-title text-center" data-aos="fade-up">📝 From Our Travelers</h2>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title">Best Thakali Experience</h5>
                        <p class="text-muted">"The Thakali set here was unforgettable! Perfectly cooked lentils and crispy gundruk."</p>
                        <small>- Anisha K.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title">Hidden Tea House</h5>
                        <p class="text-muted">"Found a small tea house with the best masala chai and stunning mountain view."</p>
                        <small>- John D.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title">Nepali Momos Love</h5>
                        <p class="text-muted">"Soft and juicy momos with tangy achar - absolutely the best in town!"</p>
                        <small>- Priya S.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Traditions & Experiences with Parallax Background -->
<section 
    class="parallax-food2" 
    style="background-image: url('images/traditions.jpg'); background-attachment: fixed; background-size: cover; background-position: center; padding: 60px 0; color: white;">
    <div class="container text-center" style="background: rgba(0,0,0,0.4); padding: 30px; border-radius: 10px;" data-aos="fade-up">
        <h2 class="section-title">🌸 Culture & People</h2>
        <p class="lead">Dhulikhel isn't just about food, it's about the stories, the smiles, and the shared meals. From traditional Newari feasts to festival gatherings, every bite is a part of our heritage.</p>
    </div>
</section>


<!-- Footer -->
<footer>
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold text-white">WayWander</h5>
                <p>Your guide to exploring the best hotels, restaurants, and attractions in Dhulikhel.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold text-white">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="hotels.php">Hotels</a></li>
                    <li><a href="restaurants.php">Restaurants</a></li>
                    <li><a href="attractions.php">Attractions</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold text-white">Follow Us</h5>
                <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="me-3"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center small">&copy; <?php echo date('Y'); ?> WayWander. All rights reserved.</div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 1000
    });
</script>
</body>
</html>
