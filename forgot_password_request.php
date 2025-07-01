<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['alert_message'] = ['type' => 'danger', 'text' => 'Please enter your email address.'];
        header('Location: fitness_home.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert_message'] = ['type' => 'danger', 'text' => 'Invalid email format.'];
        header('Location: fitness_home.php');
        exit;
    }

    // Check if email exists in users2 table
    $stmt = $conn->prepare("SELECT id FROM users2 WHERE email = ?");
    if ($stmt === false) {
        $_SESSION['alert_message'] = ['type' => 'danger', 'text' => 'Database error: ' . htmlspecialchars($conn->error)];
        header('Location: fitness_home.php');
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Generate OTP or reset token (for simplicity, generate a 6-digit OTP)
        $otp = rand(100000, 999999);

        // Store OTP in session or database as per your system (here using session for demo)
        $_SESSION['password_reset_otp'] = $otp;
        $_SESSION['password_reset_email'] = $email;

        // TODO: Send OTP to user's email - implement email sending here
        // For now, just set success message
        $_SESSION['alert_message'] = ['type' => 'success', 'text' => 'OTP sent to your email address.'];

        header('Location: fitness_home.php');
        exit;
    } else {
        $_SESSION['alert_message'] = ['type' => 'danger', 'text' => 'Email not found.'];
        header('Location: fitness_home.php');
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['alert_message'] = ['type' => 'danger', 'text' => 'Invalid request method.'];
    header('Location: fitness_home.php');
    exit;
}
?>
