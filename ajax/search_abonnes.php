<?php
require_once '../includes/db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM abonnes WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY nom";
$stmt = $pdo->prepare($query);
$searchTerm = "%$search%";
$stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
$abonnes = $stmt->fetchAll();

foreach ($abonnes as $abonne): ?>
    <tr>
        <td><?= htmlspecialchars($abonne['nom']) ?></td>
        <td><?= htmlspecialchars($abonne['prenom']) ?></td>
        <td><?= htmlspecialchars($abonne['email']) ?></td>
        <td><?= htmlspecialchars($abonne['telephone']) ?></td>
        <td><?= htmlspecialchars($abonne['date_inscription']) ?></td>
        <td>
            <button class="cyber-btn" onclick="editAbonne(<?= $abonne['id'] ?>)">Éditer</button>
            <button class="cyber-btn2" onclick="deleteAbonne(<?= $abonne['id'] ?>)">Supprimer</button>
        </td>
    </tr>
<?php endforeach; ?> 