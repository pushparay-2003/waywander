<?php
session_start();
include 'db.php';
include 'nav.php';

// Fetch attractions
$sql = "SELECT * FROM attractions ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attractions - Dhulikhel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
/* --- Parallax Sections --- */
.parallax {
  background-attachment: fixed;
  background-size: cover;
  background-position: center;
  position: relative;
  color: white;
  text-align: center;
  padding: 120px 20px;
}
.hero {
  background-image: url('images/himalaya.jpg');
}
.culture {
  background-image: url('images/newaculture.jpg');
}
.overlay {
  background: rgba(0,0,0,0.4);
  padding: 50px;
  border-radius: 12px;
  display: inline-block;
}
h1, h2 {
  font-weight: bold;
}
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
}
.footer {
  background: #005f73;
  color: white;
  padding: 40px 20px;
  text-align: center;
}
.footer a {
  color: #94d2bd;
  text-decoration: none;
}
.footer a:hover {
  text-decoration: underline;
}
</style>
</head>
<body>

<!-- Hero Parallax -->
<section class="parallax hero">
  <div class="overlay" data-aos="fade-up">
    <h1>Welcome to Dhulikhel</h1>
    <p>Where the Himalayas greet you every morning</p>
  </div>
</section>

<!-- Facts Section -->
<section class="container my-5" data-aos="fade-up">
  <h2 class="text-center mb-4">🌏 Fun Facts about Dhulikhel</h2>
  <div class="row g-4">
    <div class="col-md-4"><div class="p-4 shadow-sm bg-light rounded">⛰️ Panoramic Himalayan Views visible from town</div></div>
    <div class="col-md-4"><div class="p-4 shadow-sm bg-light rounded">🏛️ Rich Newari culture & traditional architecture</div></div>
    <div class="col-md-4"><div class="p-4 shadow-sm bg-light rounded">🚶 Perfect hub for treks & short hikes</div></div>
  </div>
</section>

<!-- Culture Parallax -->
<section class="parallax culture">
  <div class="overlay" data-aos="zoom-in">
    <h2>Culture & Heritage</h2>
    <p>Experience centuries-old traditions and craftsmanship</p>
  </div>
</section>

<!-- Attractions Cards -->
<section class="container my-5" data-aos="fade-up">
  <div class="row g-4">
    <?php while ($attraction = $result->fetch_assoc()): ?>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <?php if (!empty($attraction['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($attraction['image_url']); ?>" class="card-img-top" alt="Attraction Image">
          <?php else: ?>
            <img src="images/default-attraction.jpg" class="card-img-top" alt="Default Attraction Image">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($attraction['name']); ?></h5>
            <p class="card-text text-muted"><?php echo nl2br(htmlspecialchars(substr($attraction['description'], 0, 100))); ?>...</p>
            <a href="attraction_details.php?id=<?php echo $attraction['id']; ?>" class="btn btn-primary">View Details</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- User Blog Cards -->
<section class="container my-5" data-aos="fade-up">
  <h2 class="text-center mb-4">📝 Stories from Travelers</h2>
  <div class="row g-4">
    <div class="col-md-4"><div class="p-4 bg-light rounded shadow-sm">"Sunrise over the Himalayas is worth waking up at 4am!" - Maya</div></div>
    <div class="col-md-4"><div class="p-4 bg-light rounded shadow-sm">"Loved the peaceful vibe and warm hospitality." - Alex</div></div>
    <div class="col-md-4"><div class="p-4 bg-light rounded shadow-sm">"A perfect weekend getaway from Kathmandu." - Sam</div></div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <p>&copy; <?php echo date("Y"); ?> WayWander | Explore Dhulikhel</p>
  <p><a href="#">Privacy Policy</a> | <a href="#">Contact Us</a></p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true });
</script>
</body>
</html>
