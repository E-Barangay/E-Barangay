<?php
// Save this as: adminContent/check_email.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../sharedAssets/connect.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $userID = isset($_POST['userID']) ? intval($_POST['userID']) : 0;

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['exists' => false, 'error' => 'Invalid email format']);
        exit();
    }

    // Check if email exists in database
    // If userID is provided (editing existing user), exclude that user from check
    if ($userID > 0) {
        $query = "SELECT userID FROM users WHERE email = '$email' AND userID != $userID LIMIT 1";
    } else {
        // For new residents (add modal)
        $query = "SELECT userID FROM users WHERE email = '$email' LIMIT 1";
    }
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        $exists = mysqli_num_rows($result) > 0;
        echo json_encode(['exists' => $exists]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request - email parameter missing']);
}
?>