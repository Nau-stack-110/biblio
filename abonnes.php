<?php
session_start();
require_once 'includes/db.php';

$message = ''; 

if(isset($_POST['ajouter_abonne'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    
    $sql = "INSERT INTO abonnes (nom, prenom, email, telephone) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $telephone]);
    
    // Stocker le message dans la session
    $_SESSION['message'] = "🎉 Abonné créé avec succès ! 📚"; 
    header("Location: abonnes.php");
    exit; 
}

if(isset($_POST['supprimer_abonne'])) {
    $id_abonne = $_POST['id_abonne'];
    $sql = "DELETE FROM abonnes WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_abonne]);
    echo json_encode(["success" => true, "message" => "📖 Abonné supprimé avec succès ! 🗑️"]); // Message de succès
    exit; 
}

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
    <link rel="stylesheet" href="styles/cyberpunk.css">
    <script src="js/jquery-2.2.3.min.js"></script>
</head>
<body>
    <a href="index.php" class="cyber-button">🏠 Retour à l'accueil</a>
    
    <div class="container">
        <div class="cyber-card">
            <h2>Ajouter un Abonné</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="text" name="nom" placeholder="Nom de l'abonné" required>
                    <input type="text" name="prenom" placeholder="Prénom de l'abonné" required>
                    <input type="text" name="email" placeholder="Email" required>
                    <input type="number" name="telephone" placeholder="Téléphone" required>
                    <button type="submit" name="ajouter_abonne" class="cyber-button">Ajouter l'abonné</button>
                </form>
            </div>
            <?php if(isset($_SESSION['message'])): ?>
                <div class="success-message"><?= htmlspecialchars($_SESSION['message']) ?></div>
                <?php unset($_SESSION['message']);?>
            <?php endif; ?>
        </div>

        <div class="cyber-card">
            <h2>Liste des Abonnés</h2>
            <input type="text" id="searchAbonne" placeholder="Rechercher un abonné" />
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
                            <button class="cyber-btn" onclick="editAbonne(<?= $abonne['id'] ?>)">✏️ Editer</button>
                            <button class="cyber-btn2" onclick="deleteAbonne(<?= $abonne['id'] ?>)">🗑️ Supprimer</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#searchAbonne').on('input', function() {
            const searchValue = $(this).val();
            $.ajax({
                url: 'ajax/search_abonnes.php',
                method: 'GET',
                data: { search: searchValue },
                success: function(data) {
                    $('tbody').html(data);
                }
            });
        });

        // Disparition du message après 8 secondes
        setTimeout(function() {
            $('.success-message').fadeOut();
        }, 8000);
    });

    function deleteAbonne(id) {
        if(confirm("Êtes-vous sûr de vouloir supprimer cet abonné ?")) {
            $.post('abonnes.php', { supprimer_abonne: true, id_abonne: id }, function(response) {
                const data = JSON.parse(response);
                alert(data.message);
                location.reload(); 
            });
        }
    }

    function editAbonne(id) {
        console.log("Édition de l'abonné " + id);
    }
    </script>
</body>
</html>
