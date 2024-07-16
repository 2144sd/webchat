<?php
session_start();
include 'database.php';

try {
    $sql = "SELECT messages.id, messages.message, users.username, messages.created_at 
            FROM messages 
            JOIN users ON messages.user_id = users.id 
            ORDER BY messages.created_at ASC"; // Use ASC to show oldest first
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
