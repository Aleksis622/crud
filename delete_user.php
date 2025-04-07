<?php
require 'config.php'; // Include DB connection

// Get the user ID from the query parameter
$userId = $_GET['id'] ?? null;

if ($userId) {
    try {
        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM sign_up WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "User not found or already deleted"]);
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo json_encode(["status" => "error", "message" => "Error deleting user: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
}
?>
