<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
try {
    $data = [
        'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
        'titre' => filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING),
        'auteur' => filter_input(INPUT_POST, 'auteur', FILTER_SANITIZE_STRING),
        'isbn' => filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING),
        'categorie' => filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING),
        'disponible' => filter_input(INPUT_POST, 'disponible', FILTER_VALIDATE_BOOL)
    ];
    foreach($data as $key => $value) {
        if($value === false || $value === null) {
            throw new Exception("Champ $key invalide");
        }
    }

    $sql = "UPDATE livres SET 
            titre = :titre,
            auteur = :auteur,
            isbn = :isbn,
            categorie = :categorie,
            disponible = :disponible 
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    if($stmt->rowCount() === 0) {
        throw new Exception("Aucune modification effectuée");
    }
    echo json_encode(['success' => true, 'message' => '📖 Livre mis à jour !']);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 