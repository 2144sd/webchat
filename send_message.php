<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->message) && !empty(trim($data->message))) {
        $message = trim($data->message);
        $userId = $_SESSION['user_id'];

        $sql = "INSERT INTO messages (user_id, message, created_at) VALUES (:user_id, :message, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Message sending failed']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
