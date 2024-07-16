<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch messages
$sql = "SELECT messages.id, messages.message, users.username, messages.user_id, messages.created_at 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        ORDER BY messages.created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 600px;
        }
        h2 {
            text-align: center;
        }
        .chat-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
            margin-bottom: 10px;
        }
        .message {
            padding: 10px;
            border-radius: 50px;
            margin-bottom: 10px;
            max-width: 70%;
            clear: both;
        }
       
        .received {
            background-color: #f1f1f1; /* Light gray */
            float: left;
            text-align: center;
        }
        .input-box {
            display: flex;
            margin-top: 10px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div class="chat-box" id="chat-box">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo ($msg['user_id'] == $_SESSION['user_id']) ? 'sent' : 'received'; ?>">
                    <span><?php echo htmlspecialchars($msg['username']) . ': ' . htmlspecialchars($msg['message']); ?> (<?php echo date('h:i A', strtotime($msg['created_at'])); ?>)</span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="input-box">
            <input type="text" id="message" placeholder="Type your message..." required>
            <button type="button" id="send">Send</button>
        </div>
    </div>

    <script>
        document.getElementById('send').onclick = function() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;

            if (message.trim() === '') return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = ''; // Clear input
                    loadMessages(); // Reload messages
                } else {
                    alert(data.error || 'Message sending failed!');
                }
            });
        };

        function loadMessages() {
            fetch('load_messages.php')
                .then(response => response.json())
                .then(data => {
                    const chatBox = document.getElementById('chat-box');
                    chatBox.innerHTML = ''; // Clear the chat box

                    if (data.success) {
                        data.messages.forEach(msg => {
                            const div = document.createElement('div');
                            // Determine if the message is sent or received
                            div.className = msg.user_id == <?php echo $_SESSION['user_id']; ?> ? 'message sent' : 'message received';
                            div.textContent = `${msg.username}: ${msg.message} (${new Date(msg.created_at).toLocaleTimeString()})`;
                            chatBox.appendChild(div);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight; // Scroll to bottom
                    } else {
                        console.error(data.error || 'Failed to load messages.');
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        setInterval(loadMessages, 5000); // Load messages every 5 seconds
        loadMessages(); // Initial load
    </script>
</body>
</html>
