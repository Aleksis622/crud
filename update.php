<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $first_name = $_POST['firstName'] ?? '';
    $last_name = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $date_birth = $_POST['date_birth'] ?? '';

    if (!empty($id) && !empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone_number) && !empty($password) && !empty($date_birth)) {
        try {
            $stmt = $pdo->prepare("UPDATE sign_up SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, password = :password, date_birth = :date_birth WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone_number' => $phone_number,
                ':password' => $password,
                ':date_birth' => $date_birth
            ]);
            echo json_encode(["status" => "success", "message" => "User updated successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
    }
}
?>
