<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php'; // Include DB connection
// Helper function to check if an email is unique
function isEmailUnique($email, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sign_up WHERE email = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetchColumn() == 0; // Returns true if no match, false if email is already taken
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['firstName'] ?? '';
    $last_name = $_POST['lastName'] ?? ''; 
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $date_birth = $_POST['date_birth'] ?? '';
 // Define validation regex
 $nameRegex = "/^[A-Za-zĀ-ž]{2,}$/"; // Allows Latvian characters
 $emailRegex = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/"; // Email format
 $passwordRegex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{15,}$/"; // Password format
 $phoneRegex = "/^[0-9]{8}$/"; // 8 digits phone number
 $today = date("Y-m-d"); // Current date

// Define the allowed domains for repeated emails
const allowedEmailDomains = ['gmail.com', 'yahoo.com', 'outlook.com ', 'inbox.lv']; // Add domains as needed
const emailDomain = email.split('@')[1];

// Allow repeated emails for the specified domains
if (allowedEmailDomains.includes(emailDomain)) {
    // Skip uniqueness check if the domain is in the allowed list
    console.log("Email is allowed to repeat for domain:", emailDomain);
} else {
    // Add the uniqueness check for other email domains here
    alert("The email address cannot be repeated for other domains.");
    return false;
}
 // Validation checks
 if (!preg_match($nameRegex, $first_name)) {
     echo json_encode(["status" => "error", "message" => "First name must be at least 2 letters and contain only letters."]);
     exit();
 }

 if (!preg_match($nameRegex, $last_name)) {
     echo json_encode(["status" => "error", "message" => "Last name must be at least 2 letters and contain only letters."]);
     exit();
 }

 if (!preg_match($emailRegex, $email)) {
     echo json_encode(["status" => "error", "message" => "Invalid email format."]);
     exit();
 }

 if (!preg_match($passwordRegex, $password)) {
     echo json_encode(["status" => "error", "message" => "Password must be at least 15 characters, including uppercase and lowercase letters, numbers, and special symbols."]);
     exit();
 }

 if (strtotime($date_birth) >= strtotime($today)) {
     echo json_encode(["status" => "error", "message" => "Date of birth must be before today."]);
     exit();
 }

 if (!preg_match($phoneRegex, $phone_number)) {
     echo json_encode(["status" => "error", "message" => "Phone number must be exactly 8 digits."]);
     exit();
 }

 // Check if email already exists
 $stmt = $pdo->prepare("SELECT id FROM sign_up WHERE email = :email");
 $stmt->bindParam(':email', $email);
 $stmt->execute();
 if ($stmt->rowCount() > 0) {
     echo json_encode(["status" => "error", "message" => "Email is already registered."]);
     exit();
 }

 // Insert user into the database
    // Check if any fields are empty
    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone_number) && !empty($password) && !empty($date_birth)) {
        try {
            // Prepare the SQL query
            $stmt = $pdo->prepare("INSERT INTO sign_up (first_name, last_name, email, phone_number, password, date_birth) VALUES (:first_name, :last_name, :email, :phone_number, :password, :date_birth)");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
            $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT)); // Hash password
            $stmt->bindParam(':date_birth', $date_birth);
            $stmt->execute();
            
            // Return success response
            echo json_encode(["status" => "success", "message" => "User created successfully"]);
        } catch (PDOException $e) {
            // Handle database error and return error message
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        // Handle empty fields and return error message
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
    }
}
?>
