<?php
require_once 'includes/db.php';

// Traitement de l'ajout d'un abonné
if(isset($_POST['ajouter_abonne'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    
    $sql = "INSERT INTO abonnes (nom, prenom, email, telephone) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $telephone]);
}

// Recherche d'abonnés
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM abonnes WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY nom";
$stmt = $pdo->prepare($query);
$searchTerm = "%$search%";
$stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
$abonnes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Abonnés</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/cyberpunk.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <div class="container">
        <a href="index.php" class="cyber-button">Retour à l'accueil</a>
        
        <div class="form-container">
            <form method="POST" action="">
                <input type="text" name="nom" placeholder="Nom de l'abonné" required>
                <input type="text" name="prenom" placeholder="Prénom de l'abonné" required>
                <button type="submit" class="cyber-button">Ajouter l'abonné</button>
            </form>
        </div>

        <div class="cyber-card">
            <h2>Liste des Abonnés</h2>
            <div class="filter-section">
                <input type="text" id="searchAbonne" class="cyber-input" 
                       placeholder="Rechercher un abonné..." 
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($abonnes as $abonne): ?>
                    <tr>
                        <td><?= htmlspecialchars($abonne['nom']) ?></td>
                        <td><?= htmlspecialchars($abonne['prenom']) ?></td>
                        <td><?= htmlspecialchars($abonne['email']) ?></td>
                        <td><?= htmlspecialchars($abonne['telephone']) ?></td>
                        <td><?= htmlspecialchars($abonne['date_inscription']) ?></td>
                        <td>
                            <button class="cyber-btn" onclick="editAbonne(<?= $abonne['id'] ?>)">Éditer</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.getElementById('searchAbonne').addEventListener('input', function(e) {
        const searchValue = e.target.value;
        window.location.href = `abonnes.php?search=${searchValue}`;
    });

    function editAbonne(id) {
        // Fonction à implémenter pour l'édition
        console.log("Édition de l'abonné " + id);
    }
    </script>
</body>
</html>
