<?php
session_start();
if(!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
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
    header('Content-Type: application/json');
    
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(["success" => false, "message" => "Token CSRF invalide"]));
    }
    
    $id_abonne = filter_input(INPUT_POST, 'id_abonne', FILTER_VALIDATE_INT);
    
    try {
        $sql = "DELETE FROM abonnes WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_abonne]);
        
        echo json_encode(["success" => true, "message" => "Abonné supprimé"]);
    } catch(Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
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
    <link rel="stylesheet" href="styles/abonnes.css">
    <script src="js/jquery-2.2.3.min.js"></script>
   <style></style>
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

    <div id="editAbonneModal" class="cyber-modal">
        <div class="cyber-modal-content">
            <span class="close-modal">&times;</span>
            <h2>👤 Modifier l'abonné</h2>
            <form id="editAbonneForm">
                <input type="hidden" id="editAbonneId">
                <div class="input-group">
                    <label class="cyber-label">📛 Nom</label>
                    <input type="text" id="editNom" class="cyber-input" required>
                </div>
                <div class="input-group">
                    <label class="cyber-label">📛 Prénom</label>
                    <input type="text" id="editPrenom" class="cyber-input" required>
                </div>
                <div class="input-group">
                    <label class="cyber-label">📧 Email</label>
                    <input type="email" id="editEmail" class="cyber-input" required>
                </div>
                <div class="input-group">
                    <label class="cyber-label">😽 Téléphone</label>
                    <input type="tel" id="editTelephone" class="cyber-input" required>
                </div>
                <button type="submit" class="cyber-button">💾 Sauvegarder</button>
            </form>
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
        if(confirm("⚠️ Supprimer cet abonné ?")) {
            $.ajax({
                url: 'abonnes.php',
                method: 'POST',
                data: { 
                    supprimer_abonne: true, 
                    id_abonne: id,
                    csrf_token: '<?= $_SESSION['csrf_token'] ?>'
                },
                dataType: 'json',
                success: function(data) {
                    // Vérifier si data est déjà un objet JSON
                    if(typeof data === 'string') {
                        try {
                            data = JSON.parse(data);
                        } catch(e) {
                            showErrorMessage('❌ Réponse serveur invalide');
                            return;
                        }
                    }
                    
                    if(data.success) {
                        showSuccessMessage('🗑️ Abonné supprimé avec succès !');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showErrorMessage('❌ ' + data.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage('❌ Erreur serveur : ' + xhr.statusText);
                }
            });
        }
    }

    function editAbonne(id) {
        $.ajax({
            url: 'ajax/get_abonne.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(abonne) {
                $('#editAbonneId').val(abonne.id);
                $('#editNom').val(abonne.nom);
                $('#editPrenom').val(abonne.prenom);
                $('#editEmail').val(abonne.email);
                $('#editTelephone').val(abonne.telephone);
                
                $('#editAbonneModal').fadeIn(300);
            },
            error: function(xhr) {
                showErrorMessage('❌ Erreur lors du chargement des données');
            }
        });
    }

    $('#editAbonneForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            id: $('#editAbonneId').val(),
            nom: $('#editNom').val(),
            prenom: $('#editPrenom').val(),
            email: $('#editEmail').val(),
            telephone: $('#editTelephone').val(),
            csrf_token: '<?= $_SESSION['csrf_token'] ?>'
        };

        $.ajax({
            url: 'ajax/update_abonne.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#editAbonneModal').fadeOut(300);
                    showSuccessMessage('👤 Abonné mis à jour avec succès ! 🎉');
                    setTimeout(() => location.reload(), 3500);
                } else {
                    showErrorMessage('❌ ' + response.error);
                }
            },
            error: function(xhr) {
                showErrorMessage('❌ Erreur serveur : ' + xhr.statusText);
            }
        });
    });

    $(document).on('click', '.close-modal', function() {
        $('.cyber-modal').fadeOut(300);
    });

    $(document).on('click', function(e) {
        if ($(e.target).hasClass('cyber-modal')) {
            $('.cyber-modal').fadeOut(300);
        }
    });

    function showSuccessMessage(message) {
        const $msg = $(`
            <div class="success-flash">
                <span class="emoji">${message.match(/^\p{Emoji}+/u)[0]}</span>
                ${message}
            </div>
        `);
        
        $('body').append($msg);
        
        setTimeout(() => {
            $msg.fadeOut(500, () => $msg.remove());
        }, 3000);
    }

    function showErrorMessage(message) {
        const $msg = $(`
            <div class="error-message">
                <span class="emoji">❌</span>
                ${message}
            </div>
        `);
        $('body').append($msg);
        setTimeout(() => $msg.fadeOut(500, () => $msg.remove()), 3000);
    }
    </script>
</body>
</html>
