<?php
require 'config.php'; // Include your DB connection

try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, phone_number, date_birth FROM sign_up");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>