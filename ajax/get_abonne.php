<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

try {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if(!$id) throw new Exception('ID invalide');

    $stmt = $pdo->prepare("SELECT * FROM abonnes WHERE id = ?");
    $stmt->execute([$id]);
    $abonne = $stmt->fetch();

    if(!$abonne) throw new Exception('Abonné non trouvé');
    
    echo json_encode($abonne);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 