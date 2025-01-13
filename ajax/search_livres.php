<?php
require_once '../includes/db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

$query = "SELECT * FROM livres WHERE titre LIKE ? OR auteur LIKE ?";
$params = ["%$search%", "%$search%"];

if ($categorie) {
    $query .= " AND categorie = ?";
    $params[] = $categorie;
}

$query .= " ORDER BY titre";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$livres = $stmt->fetchAll();

foreach ($livres as $livre): ?>
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
<?php endforeach; ?> 