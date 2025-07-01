<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Please fill all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Invalid email format.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, fullname, password FROM users2 WHERE email = ?");
    if ($stmt === false) {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Database error: ' . htmlspecialchars($conn->error)]);
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $fullname, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $fullname;
            $_SESSION['email'] = $email;
            echo json_encode(['alertType' => 'success', 'alertText' => 'Login successful. Redirecting...']);
            exit;
        } else {
            echo json_encode(['alertType' => 'danger', 'alertText' => 'Incorrect password.']);
            exit;
        }
    } else {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Email not found.']);
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['alertType' => 'danger', 'alertText' => 'Invalid request method.']);
}
?>
