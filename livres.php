<?php
require_once 'includes/db.php';

// Traitement de l'ajout d'un livre
if(isset($_POST['ajouter_livre'])) {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $isbn = $_POST['isbn'];
    $categorie = $_POST['categorie'];
    
    $sql = "INSERT INTO livres (titre, auteur, isbn, categorie) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titre, $auteur, $isbn, $categorie]);
}

// Filtres
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';
$disponibilite = isset($_GET['disponibilite']) ? $_GET['disponibilite'] : '';

// Requête de base
$query = "SELECT * FROM livres WHERE 1=1";
$params = [];

if($search) {
    $query .= " AND (titre LIKE ? OR auteur LIKE ? OR isbn LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

if($categorie) {
    $query .= " AND categorie = ?";
    $params[] = $categorie;
}

if($disponibilite !== '') {
    $query .= " AND disponible = ?";
    $params[] = $disponibilite;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$livres = $stmt->fetchAll();

// Récupérer les catégories uniques
$stmt = $pdo->query("SELECT DISTINCT categorie FROM livres");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Livres</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/cyberpunk.css">
</head>
<body>
    <a href="index.php" class="cyber-button">Retour à l'accueil</a>
    <?php include 'includes/nav.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <form method="POST" action="">
                <input type="text" name="titre" placeholder="Titre du livre" required>
                <input type="text" name="auteur" placeholder="Auteur" required>
                <button type="submit" class="cyber-button">Ajouter le livre</button>
            </form>
        </div>

        <div class="cyber-card">
            <h2>Liste des Livres</h2>
            <div class="filter-section">
                <input type="text" id="searchLivre" class="cyber-input" 
                       placeholder="Rechercher un livre..." 
                       value="<?= htmlspecialchars($search) ?>">
                
                <select id="filterCategorie" class="cyber-input">
                    <option value="">Toutes les catégories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" 
                                <?= $categorie === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="filterDispo" class="cyber-input">
                    <option value="">Tous les états</option>
                    <option value="1" <?= $disponibilite === '1' ? 'selected' : '' ?>>Disponible</option>
                    <option value="0" <?= $disponibilite === '0' ? 'selected' : '' ?>>Emprunté</option>
                </select>
            </div>
            
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>ISBN</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($livres as $livre): ?>
                    <tr>
                        <td><?= htmlspecialchars($livre['titre']) ?></td>
                        <td><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td><?= htmlspecialchars($livre['isbn']) ?></td>
                        <td><?= htmlspecialchars($livre['categorie']) ?></td>
                        <td>
                            <span class="status-badge <?= $livre['disponible'] ? 'available' : 'borrowed' ?>">
                                <?= $livre['disponible'] ? 'Disponible' : 'Emprunté' ?>
                            </span>
                        </td>
                        <td>
                            <button class="cyber-btn" onclick="editLivre(<?= $livre['id'] ?>)">Éditer</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function updateFilters() {
        const search = document.getElementById('searchLivre').value;
        const categorie = document.getElementById('filterCategorie').value;
        const disponibilite = document.getElementById('filterDispo').value;
        
        let url = 'livres.php?';
        if(search) url += `search=${search}&`;
        if(categorie) url += `categorie=${categorie}&`;
        if(disponibilite !== '') url += `disponibilite=${disponibilite}`;
        
        window.location.href = url;
    }

    document.getElementById('searchLivre').addEventListener('input', updateFilters);
    document.getElementById('filterCategorie').addEventListener('change', updateFilters);
    document.getElementById('filterDispo').addEventListener('change', updateFilters);
    </script>
</body>
</html>
