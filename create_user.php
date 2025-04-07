<?php
// Enable error reporting for debugging (this will help to identify if something goes wrong)
ini_set('display_errors', 0); // Disable displaying errors directly on the page
ini_set('log_errors', 1);     // Enable error logging
ini_set('error_log', '/path/to/php-error.log');  // Set the error log path

// Start output buffering to catch any unexpected output
ob_start();

header('Content-Type: application/json');

// Database connection (you should already have this)
require 'config.php';

// Allowed email domains
$allowedEmailDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'inbox.lv'];

// Define a try-catch block to catch potential exceptions
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Collect and sanitize data
        $first_name = trim($_POST['firstName'] ?? '');
        $last_name = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $date_birth = trim($_POST['date_birth'] ?? '');

        // Validate fields
        if (empty($first_name) || !preg_match("/^[A-Za-zĀ-ž]{2,}$/", $first_name)) {
            throw new Exception("First name must be at least 2 letters and contain only letters.");
        }

        if (empty($last_name) || !preg_match("/^[A-Za-zĀ-ž]{2,}$/", $last_name)) {
            throw new Exception("Last name must be at least 2 letters and contain only letters.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array(substr(strrchr($email, "@"), 1), $allowedEmailDomains)) {
            throw new Exception("Invalid email format or email domain not allowed.");
        }

        if (empty($phone_number) || !preg_match("/^[0-9]{8}$/", $phone_number)) {
            throw new Exception("Phone number must be exactly 8 digits.");
        }

        // Password validation (must be at least 15 characters, including letters, numbers, and special characters)
        if (empty($password) || !preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{15,}$/", $password)) {
            throw new Exception("Password must be at least 15 characters, including letters, numbers, and special symbols.");
        }

        // Validate Date of Birth (must be before today)
        if (empty($date_birth) || $date_birth >= date('Y-m-d')) {
            throw new Exception("Date of birth must be before today.");
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO sign_up (first_name, last_name, email, phone_number, password, date_birth)
                               VALUES (:first_name, :last_name, :email, :phone_number, :password, :date_birth)");

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindParam(':date_birth', $date_birth);

        $stmt->execute();

        // Send successful response
        echo json_encode(["status" => "success", "message" => "User created successfully"]);
        ob_end_flush();
        exit;

    } else {
        // Method not allowed
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        ob_end_flush();
        exit;
    }
} catch (Exception $e) {
    // Handle user input errors
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    ob_end_flush();
    exit;
} catch (Throwable $e) {
    // Handle server errors
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error occurred.", "debug" => $e->getMessage()]);
    ob_end_flush();
    exit;
}
?>



