<?php
// Start session 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>WayWander | Travel and Reviews</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS (Animate on Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --accent: #ff5a5f; /* WayWander accent */
            --muted: #6b7280;
            --card-bg: #ffffff;
            --page-bg: #f6f7fb;
        }
        body {
            font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: var(--page-bg);
            color: #222;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ---- keep your original header style but modernize it slightly ---- */
        header {
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 28px;
            border-bottom: 1px solid rgba(16,24,40,0.06);
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: 0 1px 0 rgba(0,0,0,0.02);
        }
        header img { height: 44px; }
        nav a {
            margin: 0 12px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 15px;
        }
        .search-bar input, .search-bar select {
            padding: 8px 10px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #e6e9ee;
        }
        .search-bar button {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 700;
        }

        /* Hero */
        .hero {
            background-image: linear-gradient(180deg, rgba(0,0,0,0.15), rgba(0,0,0,0.15)), url('images/hero-dhulikhel.jpg');
            background-size: cover;
            background-position: center;
            padding: 84px 0 60px;
            color: white;
            position: relative;
        }
        .hero .overlay-card {
            background: rgba(255,255,255,0.95);
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(2,6,23,0.12);
        }
        .hero h1 {
            font-weight: 800;
            font-size: 40px;
            margin: 0;
            color: #fff;
            text-shadow: 0 6px 30px rgba(10,10,10,0.35);
        }
        .hero p.lead {
            color: #fff;
            opacity: 0.95;
            font-size: 18px;
            margin-top: 12px;
            margin-bottom: 18px;
        }

        /* Featured grid */
        .section-title {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
        }
        .card-spot {
            border-radius: 12px;
            overflow: hidden;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .card-spot:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 40px rgba(2,6,23,0.12);
        }
        .card-spot .img {
            height: 190px;
            object-fit: cover;
        }
        .chip {
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:6px 10px;
            background: rgba(255,255,255,0.9);
            border-radius: 100px;
            font-weight:600;
            color:#111;
        }

        /* promo strip */
        .promo {
            background: linear-gradient(90deg,#fff1f0 0%, #fffaf0 100%);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid rgba(0,0,0,0.03);
        }

        /* testimonials */
        .testimonial {
            background: linear-gradient(180deg, #fff, #fbfbff);
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 8px 30px rgba(2,6,23,0.05);
        }

        footer.site-footer {
            background: linear-gradient(180deg,#222831,#1b1f2a);
            color: #ddd;
            padding: 36px 0;
        }
        footer a { color: #ffd369; text-decoration:none; }
        footer small { color: #bbb; }

        /* small responsive fixes */
        @media (max-width: 767px) {
            .hero h1 { font-size: 28px; }
        }

    </style>
</head>
<body>

<header>
    <img src="images/logo.png" alt="waywander" />
    <nav>
        <a href="index.php">Home</a>
        <a href="hotels.php">Hotels</a>
        <a href="attractions.php">Attractions</a>
        <a href="restaurants.php">Restaurants</a>
        <a href="my_wishlist.php">Wishlist</a>
        <a href="account.html">Account</a>
    </nav>

    <form class="search-bar d-none d-md-flex" action="search_results.php" method="GET">
        <input type="text" name="query" placeholder="Search Dhulikhel, hotels, attractions..." required>
        <select name="type">
            <option value="">All</option>
            <option value="hotel">Hotels</option>
            <option value="restaurant">Restaurants</option>
            <option value="attraction">Attractions</option>
        </select>
        <button type="submit">Search</button>
    </form>
</header>

<!-- HERO -->
<section class="hero d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7 text-white" data-aos="fade-right" data-aos-duration="900">
                <h1 class="animate__animated animate__fadeInDown">Explore Dhulikhel — Hills, Heritage & Himalayan Views</h1>
                <p class="lead">Discover handpicked hotels, family-run guesthouses, scenic attractions and authentic dining. Plan your perfect day trip or weekend getaway in and around Dhulikhel.</p>

                <div class="d-flex gap-2 mt-3">
                    <a href="hotels.php" class="btn btn-lg" style="background:var(--accent);color:white;font-weight:700;border-radius:10px;">Explore Hotels</a>
                    <a href="attractions.php" class="btn btn-lg btn-outline-light" style="border-radius:10px;color:white;">See Attractions</a>
                </div>

                <div class="mt-4 overlay-card w-100" data-aos="zoom-in" data-aos-duration="700">
                    <form action="search_results.php" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <input class="form-control" name="query" placeholder="Search hotels, restaurants, attractions..." required>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All</option>
                                <option value="hotel">Hotels</option>
                                <option value="restaurant">Restaurants</option>
                                <option value="attraction">Attractions</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select">
                                <option value="">Sort: Recommended</option>
                                <option value="rating">Top Rated</option>
                                <option value="price_low">Price: Low → High</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn" style="background:var(--accent);color:white;" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5" data-aos="fade-left" data-aos-duration="900">
                <div class="card card-spot p-3">
                    <img src="uploads/1754640196_namobudha-panauti-hiking.jpg" alt="Dhulikhel View" class="img-fluid rounded mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Dhulikhel Panorama</h5>
                            <small class="text-muted">Sunrise viewpoints & easy hikes</small>
                        </div>
                        <div class="chip">
                            <i class="fa fa-mountain"></i> Top Pick
                        </div>
                    </div>
                </div>

                

            </div>
        </div>
    </div>
</section>

<!-- FEATURED HOTELS -->
<section class="py-5">
    <div class="container">
        <div class="section-title mb-3">
            <h3>Featured Hotels in Dhulikhel</h3>
            <a href="hotels.php" class="small text-muted">View all hotels →</a>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-spot">
                    <img src="images/dhulikhel.jpg" class="img-fluid img" alt="Hotel Paradise">
                    <div class="card-body">
                        <h5 class="card-title">Mirabel Hotel resort</h5>
                        <p class="text-muted small mb-2">Mountain view • Complimentary breakfast</p>
                        <div class="d-flex justify-content-between align-items-center">
                            
                            <div>
                                <a href="hotel_details.php?id=6" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-spot">
                    <img src="images/himalayan sunrise.jpg" class="img-fluid img" alt="Resort">
                    <div class="card-body">
                        <h5 class="card-title">Himalayan Horizon Hotel</h5>
                        <p class="text-muted small mb-2">Resort • Family friendly</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="hotel_details.php?id=4" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card card-spot">
                    <img src="images/eco lodge.jpg" class="img-fluid img" alt="Eco Lodge">
                    <div class="card-body">
                        <h5 class="card-title"> Dhulikhel Lodge Resort</h5>
                        <p class="text-muted small mb-2">Eco stay • Boutique vibes</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="hotel_details.php?id=3" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container -->
</section>

<!-- PROMOTIONAL STRIP / ADS -->
<section class="container mb-5">
    <div class="row g-3">
        <div class="col-md-8" data-aos="fade-right">
            <div class="promo d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Featured Experience: Sunrise Hike + Tea with the Locals</h5>
                    <small class="text-muted">Join a guided sunrise hike with panoramic Himalayan views and a local tea experience.</small>
                </div>
                <div>
                    <a href="attractions.php" class="btn btn-outline-dark">See Attractions</a>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
            <div class="promo text-center">
                <small class="text-muted">Sponsored</small>
                <h6 class="mt-1">Dhulikhel Home Stays</h6>
                <p class="small mb-1">Authentic stays run by local families — support community tourism.</p>
            </div>
        </div>
    </div>
</section>

<!-- ATTRACTIONS -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-title mb-3">
            <h3>Must-See Attractions</h3>
            <a href="attractions.php" class="small text-muted">See more attractions →</a>
        </div>

        <div class="row g-4">
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="100">
                <div class="card card-spot">
                    <img src="images/namo buddha.jpg" class="img-fluid img" alt="Namo Buddha">
                    <div class="card-body">
                        <h5 class="card-title">Namo Buddha</h5>
                        <p class="small text-muted mb-2">Peaceful monastery with Himalayan views.</p>
                        <a href="attraction_details.php?id=8" class="btn btn-sm btn-outline-primary">Details</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
                <div class="card card-spot">
                    <img src="images/Popular-Village-Trekking-in-Nepal.webp" class="img-fluid img" alt="Hike">
                    <div class="card-body">
                        <h5 class="card-title">Dhulikhel View Tower</h5>
                        <p class="small text-muted mb-2">Short trails perfect for sunrise walks.</p>
                        <a href="attraction_details.php?id=2" class="btn btn-sm btn-outline-primary">Details</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
                <div class="card card-spot">
                    <img src="uploads/1754640100_kalidevi temple.jpg" class="img-fluid img" alt="Market">
                    <div class="card-body">
                        <h5 class="card-title">Kali Devi Temple</h5>
                        <p class="small text-muted mb-2">Sacred Temple in Dhulikhel. It is one of the most popular attractions of Dhulikhel.</p>
                        <a href="attraction_details.php?id=3" class="btn btn-sm btn-outline-primary">Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="py-5">
    <div class="container">
        <div class="section-title mb-3">
            <h3>Traveler Stories</h3>
            <span class="small text-muted">Real experiences shared by visitors to Dhulikhel</span>
        </div>

        <div id="testimonials" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="testimonial p-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="images/priya.jpg" class="rounded-circle" width="64" height="64" alt="">
                            <div>
                                <strong>Priya & family</strong>
                                <div class="small text-muted">Visited June 2025</div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">"Stunning sunrise views and friendly hosts. Hotel Paradise made our family trip relaxed and memorable."</p>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial p-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="images/person2.jpg" class="rounded-circle" width="64" height="64" alt="">
                            <div>
                                <strong>Ashok</strong>
                                <div class="small text-muted">Visited April 2025</div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">"Loved the local eats and the market — a real taste of community life in Dhulikhel."</p>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial p-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="images/ashok.jpg" class="rounded-circle" width="64" height="64" alt="">
                            <div>
                                <strong>Emma</strong>
                                <div class="small text-muted">Visited Jan 2025</div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">"Perfect little escape from Kathmandu — scenic, safe and very walkable."</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonials" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonials" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</section>

<!-- QUICK TIPS -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-title mb-3">
            <h3>Quick Travel Tips</h3>
            <span class="small text-muted">Handy local tips before you go</span>
        </div>

        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card p-3">
                    <h5>Best time to visit</h5>
                    <p class="small text-muted">September–December for clear Himalayan views, March–May for blooming rhododendrons.</p>
                </div>
            </div>

            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card p-3">
                    <h5>Local transport</h5>
                    <p class="small text-muted">Taxis and local jeeps are available — clarify prices before starting. Short walks are often the most rewarding.</p>
                </div>
            </div>

            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card p-3">
                    <h5>What to pack</h5>
                    <p class="small text-muted">Layered clothing, sturdy shoes, sunscreen and a light rain jacket for mountain winds.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER (keeps your original content) -->
<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-6">
                <h3 style="color: #fff;">Contact Us</h3>
                <p style="color:#ddd;">Email: <a href="mailto:info@waywander.com">info@waywander.com</a></p>
                <p style="color:#ddd;">Phone: <a href="tel:+9771234567890">+977 1234567890</a></p>
                <p style="color:#ddd;">Location: Dhulikhel, Kavrepalanchok, Nepal</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h3 style="color: #fff;">Follow Us</h3>
                <div class="d-flex justify-content-md-end gap-3 align-items-center">
                    <a href="#" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                </div>

                <div class="mt-4">
                    <small>&copy; 2025 WayWander. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 700, easing: 'ease-in-out' });
</script>

</body>
</html>
