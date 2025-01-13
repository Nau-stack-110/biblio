<?php
session_start(); 
require_once 'includes/db.php';

if(isset($_POST['ajouter_livre'])) {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $isbn = $_POST['isbn'];
    $categorie = $_POST['categorie'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    $sql = "INSERT INTO livres (titre, auteur, isbn, categorie, disponible) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titre, $auteur, $isbn, $categorie, $disponible]);
    
    // Stocker le message dans la session
    $_SESSION['message'] = "📚 Livre ajouté avec succès ! 🎉"; 
    header("Location: livres.php");
    exit; 
}

if(isset($_POST['supprimer_livre'])) {
    $id_livre = $_POST['id_livre'];
    $sql = "DELETE FROM livres WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_livre]);
    echo json_encode(["success" => true, "message" => "📖 Livre supprimé avec succès ! 🗑️"]); 
    exit; 
}

// Récupération des livres
$query = "SELECT * FROM livres";
$livres = $pdo->query($query)->fetchAll();

// Récupération des catégories
$categorieQuery = "SELECT DISTINCT categorie FROM livres";
$categories = $pdo->query($categorieQuery)->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Livres</title>
    <link rel="stylesheet" href="styles/cyberpunk.css">
    <script src="js/jquery-2.2.3.min.js"></script>
</head>
<body>
    <a href="index.php" class="cyber-button">🏠 Retour à l'accueil</a>
    
    <div class="container">
        <div class="cyber-card">
            <h2>Ajouter un Livre</h2>
            <form method="POST" class="cyber-form">
                <input type="text" name="titre" placeholder="Titre" required>
                <input type="text" name="auteur" placeholder="Auteur" required>
                <input type="text" name="isbn" placeholder="ISBN" required>
                <select name="categorie" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach($categories as $categorie): ?>
                        <option value="<?= htmlspecialchars($categorie) ?>"><?= htmlspecialchars($categorie) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>
                    <input type="checkbox" name="disponible" checked> Disponible
                </label>
                <button type="submit" name="ajouter_livre" class="cyber-button">Ajouter le livre</button>
            </form>
            <?php if(isset($_SESSION['message'])): ?>
                <div class="success-message"><?= htmlspecialchars($_SESSION['message']) ?></div>
                <?php unset($_SESSION['message']);?>
            <?php endif; ?>
        </div>

        <div class="cyber-card">
            <h2>Liste des Livres</h2>
            <input type="text" id="searchLivre" placeholder="Rechercher un livre" />
            <select id="filterCategorie">
                <option value="">Filtrer par catégorie</option>
                <?php foreach($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie) ?>"><?= htmlspecialchars($categorie) ?></option>
                <?php endforeach; ?>
            </select>
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>ISBN</th>
                        <th>Catégorie</th>
                        <th>Disponible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="livresTableBody">
                    <?php foreach($livres as $livre): ?>
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
                </tbody>
            </table>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#searchLivre').on('input', function() {
            const searchValue = $(this).val();
            const filterValue = $('#filterCategorie').val();
            $.ajax({
                url: 'ajax/search_livres.php',
                method: 'GET',
                data: { search: searchValue, categorie: filterValue },
                success: function(data) {
                    $('#livresTableBody').html(data);
                }
            });
        });

        $('#filterCategorie').on('change', function() {
            const filterValue = $(this).val();
            const searchValue = $('#searchLivre').val();
            $.ajax({
                url: 'ajax/search_livres.php',
                method: 'GET',
                data: { search: searchValue, categorie: filterValue },
                success: function(data) {
                    $('#livresTableBody').html(data);
                }
            });
        });

        // Disparition du message après 8 secondes
        setTimeout(function() {
            $('.success-message').fadeOut();
        }, 8000);
    });

    function deleteLivre(id) {
        if(confirm("Êtes-vous sûr de vouloir supprimer ce livre ?")) {
            $.post('livres.php', { supprimer_livre: true, id_livre: id }, function(response) {
                const data = JSON.parse(response);
                alert(data.message); 
                location.reload(); 
            });
        }
    }

    function editLivre(id) {
        console.log("Édition du livre " + id);
    }
    </script>
</body>
</html> 