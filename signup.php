<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Please fill all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Invalid email format.']);
        exit;
    }

    // Handle profile image upload
    $profile_image_name = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileSize = $_FILES['profile_image']['size'];
        $fileType = $_FILES['profile_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_image_name = $newFileName;
            } else {
                echo json_encode(['alertType' => 'danger', 'alertText' => 'Error moving the uploaded file.']);
                exit;
            }
        } else {
            echo json_encode(['alertType' => 'danger', 'alertText' => 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions)]);
            exit;
        }
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users2 (fullname, email, password, profile_image) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(['alertType' => 'danger', 'alertText' => 'Prepare failed: ' . htmlspecialchars($conn->error)]);
        exit;
    }
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $profile_image_name);

    if ($stmt->execute()) {
        echo json_encode(['alertType' => 'success', 'alertText' => 'Signup successful. You can now log in.']);
    } else {
        if ($conn->errno === 1062) {
            echo json_encode(['alertType' => 'warning', 'alertText' => 'This email is already registered.']);
        } else {
            echo json_encode(['alertType' => 'danger', 'alertText' => 'Error: ' . htmlspecialchars($conn->error)]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['alertType' => 'danger', 'alertText' => 'Invalid request method.']);
}
?>
