<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$message = "";
$doctors = [];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $doctor_name = $_POST["doctor_name"] ?? "";
    $date = $_POST["date"] ?? "";
    $location = $_POST["location"] ?? "";

    if (empty($doctor_name) || empty($date) || empty($location)) {
        $message = "Please fill in all fields.";
    } else {
        $message = "Appointment booked with $doctor_name on $date in $location.";
    }
}

// Fetch doctors from database
$result = $conn->query("SELECT name, specialty, latitude, longitude FROM doctors");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Doctor Appointment - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
    }
    .navbar {
      background-color: #2575fc;
    }
    .navbar a {
      color: white !important;
      font-weight: 600;
    }
    .navbar-toggler {
      border-color: rgba(255, 255, 255, 0.1);
    }
    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255, 255, 255, 0.7%29' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }
    .hero {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      color: white;
      padding: 4rem 2rem;
      text-align: center;
    }
    .search-widget {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: -3rem auto 3rem auto;
      position: relative;
      z-index: 10;
    }
    .featured-doctors {
      padding: 2rem;
      max-width: 900px;
      margin: 0 auto 3rem auto;
    }
    .doctor-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 1rem;
      text-align: center;
      margin-bottom: 1.5rem;
    }
    .doctor-card img {
      border-radius: 50%;
      width: 120px;
      height: 120px;
      object-fit: cover;
      margin-bottom: 1rem;
    }
    .contact-info {
      background: white;
      padding: 2rem;
      max-width: 900px;
      margin: 0 auto 3rem auto;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }
    footer {
      background-color: #2575fc;
      color: white;
      padding: 1.5rem 2rem;
      text-align: center;
    }
    footer a {
      color: white;
      margin: 0 0.5rem;
      text-decoration: none;
      font-weight: 600;
    }
    footer a:hover {
      text-decoration: underline;
    }
  }
  @media (max-width: 768px) {
    .hero {
      padding: 2rem 1rem;
    }
    .search-widget {
      margin: -1.5rem 1rem 1.5rem 1rem;
      padding: 1rem;
      max-width: 100%;
    }
    .featured-doctors {
      padding: 1rem;
      max-width: 100%;
    }
    .doctor-card {
      margin-bottom: 1rem;
    }
    .doctor-card img {
      width: 80px;
      height: 80px;
    }
    .contact-info {
      padding: 1rem;
      max-width: 100%;
      margin: 0 1rem 1.5rem 1rem;
    }
  }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg px-4">
    <a class="navbar-brand" href="#">Doctor Appointment</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="doctor_register.php">Register Doctor</a></li>
        <li class="nav-item"><a class="nav-link" href="signup.html">Sign Up</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php" onclick="return confirmLogout()">Logout</a></li>
      </ul>
      <script>
        function confirmLogout() {
          return confirm('Are you sure you want to logout?');
        }
      </script>
    </div>
  </nav>

  <section class="hero">
    <h1>Welcome to Doctor Appointment</h1>
    <p>Your health is our priority. Find the best medical specialists and book your appointment easily.</p>
  </section>

    <section class="search-widget">
      <h2>Search Medical Specialists & Book Appointment</h2>
      <?php if (!empty($message)): ?>
        <div class="alert alert-info" role="alert">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="home.php">
        <div class="mb-3">
          <label for="doctor_name" class="form-label">Select Doctor</label>
          <select id="doctor_name" name="doctor_name" class="form-select" required>
            <option value="">Select a doctor</option>
            <?php foreach ($doctors as $doctor): ?>
              <option value="<?php echo htmlspecialchars($doctor['name']); ?>">
                <?php echo htmlspecialchars($doctor['name'] . " - " . $doctor['specialty'] . " (" . $doctor['latitude'] . ", " . $doctor['longitude'] . ")"); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="date" class="form-label">Preferred Date</label>
          <input type="date" id="date" name="date" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="location" class="form-label">Location</label>
          <select id="location" name="location" class="form-select" required>
            <option value="">Select your location</option>
            <option value="Kathmandu">Kathmandu</option>
            <option value="Banepa">Banepa</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Search & Book</button>
      </form>
    </section>

  <section class="featured-doctors">
    <h2>Featured Doctors</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="doctor-card">
          <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Dr. John Doe" />
          <h5>Dr. John Doe</h5>
          <p>Cardiologist</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="doctor-card">
          <img src="images/alisha.jpg" alt="Alisha Patel" />
          <h5>Dr. Alisha Lungba Tamang</h5>
          <p>Dermatologist</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="doctor-card">
          <img src="https://randomuser.me/api/portraits/men/56.jpg" alt="Dr. Alan Brown" />
          <h5>Dr. Alan Brown</h5>
          <p>Neurologist</p>
        </div>
      </div>
    </div>
  </section>

  <section id="blood-widgets" class="container my-5">
    <h2 class="text-center mb-4">Blood Services</h2>
    <div class="row text-center">
      <div class="col-md-6 mb-4">
        <div class="service-card p-4 bg-white rounded shadow-sm h-100">
          <h5>Receiving</h5>
          <p>Information and support for patients receiving blood transfusions.</p>
          <a href="#" class="btn btn-primary mt-3">Learn More</a>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="service-card p-4 bg-white rounded shadow-sm h-100">
          <h5>Blood Donor</h5>
          <p>Join our blood donor program and help save lives.</p>
          <a href="#" class="btn btn-primary mt-3">Become a Donor</a>
        </div>
      </div>
    </div>
  </section>

  <section class="services-section">
    <h2>Our Services</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="service-card p-3 bg-white rounded shadow-sm">
          <h5>Hamro Sewa</h5>
          <p>Providing quality healthcare services with compassion and care.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="service-card p-3 bg-white rounded shadow-sm">
          <h5>24/7 Availability</h5>
          <p>Our services are available round the clock to assist you anytime.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="service-card p-3 bg-white rounded shadow-sm">
          <h5>Expert Medical Staff</h5>
          <p>Experienced and dedicated medical professionals at your service.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="workout-goals-section py-5" style="background-color: #f8f9fa;">
    <div class="container bg-white rounded p-4 d-flex flex-wrap align-items-center" style="box-shadow: 0 8px 24px rgba(0,0,0,0.1);">
      <div class="text-content col-md-6 p-3">
        <h2 class="fw-bold">SET GOALS. LOG WORKOUTS. STAY ON TRACK.</h2>
        <p>Easily track your Workouts, set Training Plans, and discover new Workout Routes to crush your goals.</p>
      </div>
      <div class="image-content col-md-6 p-3" style="background-image: url('images/pho.jpg'); background-size: cover; background-position: center; height: 300px; border-radius: 0.5rem;">
      </div>
    </div>
  </section>

  <footer>
    <p>Follow us on:</p>
    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">Facebook</a> |
    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">Twitter</a> |
    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a> |
    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer">LinkedIn</a>
    <p>About Us | Privacy Policy | Terms of Service</p>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
