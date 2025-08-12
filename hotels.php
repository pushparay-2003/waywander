<?php
session_start();
include 'db.php';
include 'nav.php';

// Fetch hotels
$sql = "SELECT * FROM hotels ORDER BY name ASC";
$result = $conn->query($sql);
?>

<html>
<head>
    <title>Hotels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        body {
            background: linear-gradient(180deg, #e6f9f9 0%, #ffffff 100%);
            font-family: 'Poppins', sans-serif;
        }
        /* Hero Parallax */
        .hero {
            background: url('images/stay.jpg') center/cover fixed;
            height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }
        .scroll-down {
            margin-top: 20px;
            font-size: 2rem;
            animation: bounce 2s infinite;
            cursor: pointer;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0);}
            40% { transform: translateY(-10px);}
            60% { transform: translateY(-5px);}
        }

        /* Hotel Card Styling */
        .card {
            height: 100%;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .card-img-top {
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .card:hover .card-img-top {
            transform: scale(1.05);
        }
        .btn-primary {
            border-radius: 25px;
            padding: 6px 18px;
            background-color: #00a8a8;
            border: none;
        }
        .btn-primary:hover {
            background-color: #007f7f;
            transform: scale(1.05);
        }

        /* Fact & Tips Cards */
        .fact-card {
            background: #d4f4f4;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Footer */
        footer {
            background-color: #007f7f;
            color: white;
            padding: 30px 0;
        }
        footer a {
            color: #d4f4f4;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
        .parallax-section {
    position: relative;
    background-attachment: fixed;
    background-size: cover;
    background-position: center;
    min-height: 100vh;
    display: flex;
    align-items: center;
}

.overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
}

.glass {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    padding: 20px;
    border-radius: 12px;
    color: white;
}

html {
    scroll-behavior: smooth;
}

    </style>
</head>
<body>

<!-- Hero -->
<section class="hero" data-aos="fade-in">
    <h1 class="display-4 fw-bold">Discover Stays in Dhulikhel</h1>
    <p class="lead">Experience comfort, culture, and breathtaking views</p>
    <div class="scroll-down" onclick="document.getElementById('hotels-list').scrollIntoView({behavior: 'smooth'})">
        <i class="bi bi-chevron-down"></i>
    </div>
</section>

<!-- Culture Intro -->
<div class="container my-5" data-aos="fade-up">
    <div class="fact-card">
        <h2>Why Dhulikhel?</h2>
        <p>From Himalayan sunrise views to traditional Newari hospitality, Dhulikhel’s hotels are more than a stay — they’re a story.</p>
    </div>
</div>

<!-- Hotels Section -->
<div class="container" id="hotels-list">
    <div class="row g-4">
        <?php while ($hotel = $result->fetch_assoc()): ?>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="150">
                <div class="card shadow-sm">
                    <?php if (!empty($hotel['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" class="card-img-top" alt="Hotel Image">
                    <?php else: ?>
                        <img src="images/default-hotel.jpg" class="card-img-top" alt="Default Hotel Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($hotel['name']); ?></h5>
                        <p class="card-text text-muted">
                            <?php echo nl2br(htmlspecialchars(substr($hotel['description'], 0, 100))); ?>...
                        </p>
                        <p><strong>⭐ Rating:</strong> 
                            <?php echo isset($hotel['rating']) ? htmlspecialchars($hotel['rating']) : 'No rating'; ?>/5
                        </p>
                        <a href="hotel_details.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- PARALLAX BACKGROUND 1: Weather & Facts -->
<section class="parallax-section" style="background-image: url('images/weather.jpg');">
    <div class="overlay"></div>
    <div class="container my-5 text-white">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-right">
                <div class="fact-card glass">
                    <h4>🌤 Current Weather</h4>
                    <p>Sunny, 22°C</p>
                    <small>(Live API can be added)</small>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up">
                <div class="fact-card glass">
                    <h4>💡 Traveler Tip</h4>
                    <p>Book sunrise-view rooms in advance — they sell out fast!</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-left">
                <div class="fact-card glass">
                    <h4>📜 Did You Know?</h4>
                    <p>Dhulikhel has been a key trade route between Nepal and Tibet for centuries.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PARALLAX BACKGROUND 2: Traveler Blogs -->
<section class="parallax-section" style="background-image: url('images/food.jpg');">
    <div class="overlay"></div>
    <div class="container my-5 text-white">
        <h3 class="text-center mb-4 fw-bold" data-aos="fade-up">Traveler Stories</h3>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-right">
                <div class="fact-card glass">
                    <h5>🌄 Sunrise Bliss</h5>
                    <p>"Watching the mountains light up at dawn was pure magic."</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up">
                <div class="fact-card glass">
                    <h5>🍲 Culinary Journey</h5>
                    <p>"The momos here are to die for — especially with spicy achar."</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-left">
                <div class="fact-card glass">
                    <h5>🚶‍♂️ Nature Escape</h5>
                    <p>"A quick trek from the hotel led us to stunning valley views."</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Footer -->
<footer>
    <div class="container text-center">
        <p>&copy; <?php echo date("Y"); ?> WayWander. All Rights Reserved.</p>
        <p>
            <a href="index.php">Home</a> | 
            <a href="hotels.php">Hotels</a> | 
            <a href="restaurants.php">Restaurants</a> | 
            <a href="contact.php">Contact</a>
        </p>
        <p>
            Follow us:
            <i class="bi bi-facebook mx-1"></i>
            <i class="bi bi-instagram mx-1"></i>
            <i class="bi bi-twitter mx-1"></i>
        </p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1200,
        once: true,
        offset: 100
    });
</script>
</body>
</html>
