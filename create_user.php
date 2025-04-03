<?php
require 'config.php'; // Include DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['firstName'] ?? '';
    $last_name = $_POST['lastName'] ?? ''; 
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($first_name) && !empty($last_name) && !empty($email)&& !empty($phone_number)&& !empty($password)) {
        {
        try {
            
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone_number,password) VALUES (:first_name, :last_name, :email, :phone_number, :password)");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            echo json_encode(["status" => "success", "message" => "User created successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
}

?>
