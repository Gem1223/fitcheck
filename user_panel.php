<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch user data from database
$userEmailSession = $_SESSION['email'] ?? null;
$userName = 'User';
$userEmail = 'user@example.com';

if ($userEmailSession) {
    $stmt = $conn->prepare("SELECT fullname, email, profile_image FROM users2 WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $userEmailSession);
        $stmt->execute();
        $stmt->bind_result($fullname, $email, $profileImage);
        if ($stmt->fetch()) {
            $userName = $fullname;
            $userEmail = $email;
            $userImage = $profileImage;
        }
        $stmt->close();
    }
}

// Static achievements data for demo
$achievements = [
    ['title' => 'Marathon Completed', 'date' => '2025-05-10'],
    ['title' => '100 Workouts Logged', 'date' => '2025-06-15'],
    ['title' => 'Lost 10kg', 'date' => '2025-07-20'],
];

// Static goals data for demo
$goalsLabels = ['Running', 'Cycling', 'Swimming', 'Weightlifting', 'Yoga'];
$goalsData = [5, 3, 2, 4, 1];

// BMI calculation
$bmi = null;
$category = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['weight']) && isset($_POST['height'])) {
    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);
    if ($weight > 0 && $height > 0) {
        $height_m = $height / 100;
        $bmi = $weight / ($height_m * $height_m);
        $bmi = round($bmi, 2);
        if ($bmi < 18.5) {
            $category = "Underweight";
        } elseif ($bmi < 25) {
            $category = "Normal weight";
        } elseif ($bmi < 30) {
            $category = "Overweight";
        } else {
            $category = "Obesity";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .alert-custom {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 1050;
      min-width: 300px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      animation: slideIn 0.5s ease forwards;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(100%); }
      to { opacity: 1; transform: translateX(0); }
    }
    .profile-image {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #007bff;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="user_panel.php">User Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="#profile">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="#goals">Goals</a></li>
          <li class="nav-item"><a class="nav-link" href="#achievements">Achievements</a></li>
          <li class="nav-item"><a class="nav-link" href="#bmi">BMI Calculator</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <section id="profile" class="mb-5 text-center">
      <h2>My Profile</h2>
      <?php if (!empty($userImage)): ?>
        <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Profile Image" class="profile-image" />
      <?php else: ?>
        <img src="images/default-profile.png" alt="Default Profile Image" class="profile-image" />
      <?php endif; ?>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($userName); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($userEmail); ?></p>
    </section>

    <section id="goals" class="mb-5">
      <h2>My Fitness Goals</h2>
      <canvas id="goalsChart" style="max-width: 600px;"></canvas>
    </section>

    <section id="achievements" class="mb-5">
      <h2>My Achievements</h2>
      <ul class="list-group" style="max-width: 600px;">
        <?php foreach ($achievements as $achievement): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo htmlspecialchars($achievement['title']); ?>
            <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($achievement['date']); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section id="bmi" class="mb-5">
      <h2>BMI Calculator</h2>
      <form method="POST" action="user_panel.php" style="max-width: 400px;">
        <div class="mb-3">
          <label for="weight" class="form-label">Weight (kg)</label>
          <input type="number" step="0.1" class="form-control" id="weight" name="weight" required value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>">
        </div>
        <div class="mb-3">
          <label for="height" class="form-label">Height (cm)</label>
          <input type="number" step="0.1" class="form-control" id="height" name="height" required value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Calculate BMI</button>
      </form>
      <?php if ($bmi !== null): ?>
        <div class="alert alert-info mt-3">
          Your BMI is <strong><?php echo $bmi; ?></strong>. Category: <strong><?php echo $category; ?></strong>.
        </div>
      <?php endif; ?>
    </section>
  </div>

  <script>
    const ctx = document.getElementById('goalsChart').getContext('2d');
    const goalsChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($goalsLabels); ?>,
        datasets: [{
          label: 'Hours per Week',
          data: <?php echo json_encode($goalsData); ?>,
          backgroundColor: 'rgba(54, 162, 235, 0.7)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Hours'
            }
          }
        }
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
