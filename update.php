<?php
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['firstName']; 
    $last_name = $_POST['lastName']; 
    $phone_number = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET name = :first_name, :last_name, :email, :phone_number, :password, WHERE id = :id");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':phone', $phone_number);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "User updated successfully.";
        header("Location: index.html");
        exit();
    } else {
        echo "Error updating user.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
