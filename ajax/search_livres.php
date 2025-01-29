<?php
require_once '../includes/db.php';

$search = isset($_GET['search']) ? "%{$_GET['search']}%" : '%';
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

try {
    $sql = "SELECT * FROM livres 
            WHERE (titre LIKE :search 
            OR auteur LIKE :search 
            OR isbn LIKE :search)";
    
    $params = [':search' => $search];
    
    if(!empty($categorie)) {
        $sql .= " AND categorie = :categorie";
        $params[':categorie'] = $categorie;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    ob_start();
    foreach($stmt as $livre): ?>
    <tr>
        <td><?= htmlspecialchars($livre['titre']) ?></td>
        <td><?= htmlspecialchars($livre['auteur']) ?></td>
        <td><?= htmlspecialchars($livre['isbn']) ?></td>
        <td><?= htmlspecialchars($livre['categorie']) ?></td>
        <td><?= $livre['disponible'] ? 'Oui' : 'Non' ?></td>
        <td>
            <button class="cyber-btn" onclick="editLivre(<?= $livre['id'] ?>)">✏️</button>
            <button class="cyber-btn2" onclick="deleteLivre(<?= $livre['id'] ?>)">🗑️</button>
        </td>
    </tr>
    <?php endforeach;
    
    echo ob_get_clean();

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 