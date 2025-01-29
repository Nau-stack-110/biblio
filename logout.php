<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
    
    session_unset();
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

header("Location: login.php?logout=1");
exit; 