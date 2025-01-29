<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if(!$id) {
        throw new Exception('ID invalide');
    }

    $stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
    $stmt->execute([$id]);
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$livre) {
        throw new Exception('Livre non trouvé');
    }

    // Convertir le booléen pour JavaScript
    $livre['disponible'] = (bool)$livre['disponible'];
    
    echo json_encode($livre);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 