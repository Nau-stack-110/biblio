<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

try {
    // Vérification CSRF
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Token CSRF invalide');
    }

    // Validation des données
    $data = [
        'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
        'nom' => trim(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING)),
        'prenom' => trim(filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING)),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'telephone' => filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING)
    ];

    // Vérification des champs obligatoires
    if(empty($data['nom']) || empty($data['prenom'])) {
        throw new Exception('Nom et prénom sont obligatoires');
    }

    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email invalide');
    }

    // Mise à jour dans la base
    $sql = "UPDATE abonnes SET 
            nom = :nom,
            prenom = :prenom,
            email = :email,
            telephone = :telephone
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    if(!$stmt->execute($data)) {
        throw new Exception('Erreur lors de la mise à jour');
    }

    echo json_encode([
        'success' => true,
        'message' => '👤 Abonné mis à jour avec succès ! 🎉'
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => '🔥 Erreur : ' . $e->getMessage()
    ]);
} 