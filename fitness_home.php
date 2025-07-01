<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Fitness Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">FITNESS JOY</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link" href="#">Workouts</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Routes</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Community</a></li>
          <li class="nav-item">
            <a class="nav-link btn btn-outline-primary px-4" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">LOG IN</a>
          </li>
          <li class="nav-item ms-2">
            <a class="nav-link btn btn-primary px-4 text-white" href="#" data-bs-toggle="modal" data-bs-target="#signupModal">SIGN UP</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Signup Modal -->
  <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-4">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold" id="signupModalLabel">Sign Up</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="signup.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="fullname" class="form-label fw-semibold">Full Name</label>
              <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter Full Name" required />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required />
            </div>
            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required />
            </div>
            <div class="mb-3">
              <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
              <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" />
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Sign Up</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-4">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold" id="loginModalLabel">Log In</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="login.php" method="POST">
            <div class="mb-3">
              <label for="loginEmail" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter Email" required />
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label fw-semibold">Password</label>
              <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter Password" required />
            </div>
            <div class="mb-3 text-end">
              <a href="forgot_password_request.html" class="text-decoration-none">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-dark w-100 fw-bold">LOG IN</button>
          </form>
          <div class="d-grid mt-3">
            <a href="#" class="btn btn-outline-danger fw-bold d-flex align-items-center justify-content-center gap-2">
              <img src="images/app.jpg" alt="Google Logo" style="height:20px; width:20px;" />
              Login with Google
            </a>
          </div>
          <div class="mt-3 text-center">
            <span>Don't have an account? <a href="#" class="text-decoration-underline" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);
      const alertType = urlParams.get('alertType');
      const alertText = urlParams.get('alertText');
      const formType = urlParams.get('formType'); // 'signup' or 'login'

      function createBootstrapAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
          <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        return alertDiv;
      }

      if (alertType && alertText && formType) {
        const modalBody = document.querySelector(`#${formType}Modal .modal-body form`);
        if (modalBody) {
          const existingAlert = modalBody.querySelector('.alert');
          if (existingAlert) {
            existingAlert.remove();
          }
          const alertElement = createBootstrapAlert(alertType, decodeURIComponent(alertText));
          modalBody.prepend(alertElement);

          // Show the corresponding modal
          const modal = new bootstrap.Modal(document.getElementById(formType + 'Modal'));
          modal.show();
        }

        // Remove alert params from URL without reloading
        if (history.replaceState) {
          const url = new URL(window.location);
          url.searchParams.delete('alertType');
          url.searchParams.delete('alertText');
          url.searchParams.delete('formType');
          history.replaceState(null, '', url.toString());
        }
      } else if (alertType && alertText) {
        // Show general alert at top of page
        const container = document.querySelector('.container') || document.body;
        const existingAlert = container.querySelector('.alert');
        if (existingAlert) {
          existingAlert.remove();
        }
        const alertElement = createBootstrapAlert(alertType, decodeURIComponent(alertText));
        container.prepend(alertElement);

        // Remove alert params from URL without reloading
        if (history.replaceState) {
          const url = new URL(window.location);
          url.searchParams.delete('alertType');
          url.searchParams.delete('alertText');
          history.replaceState(null, '', url.toString());
        }
      }
    });
  </script>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
