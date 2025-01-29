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
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(["success" => false, "message" => "Token CSRF invalide"]));
    }
    
    $id_livre = filter_input(INPUT_POST, 'id_livre', FILTER_VALIDATE_INT);
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

    <div id="editModal" class="cyber-modal">
        <div class="cyber-modal-content">
            <span class="close-modal">&times;</span>
            <h2>📝 Modifier le livre</h2>
            <form id="editForm">
                <input type="hidden" id="editId">
                <div class="input-group">
                    <label class="cyber-label">📖 Titre</label>
                    <input type="text" id="editTitre" class="cyber-input" required>
                </div>
                <div class="input-group">
                    <label class="cyber-label">👤 Auteur</label>
                    <input type="text" id="editAuteur" class="cyber-input" required>
                </div>
                <div class="input-group">
                    <label class="cyber-label">🔢 ISBN</label>
                    <input type="text" id="editIsbn" class="cyber-input" required>
                </div>
                <div class="input-group">
                    <label class="cyber-label">😽 Catégorie</label>
                    <select id="editCategorie" class="cyber-input" required>
                        <?php foreach($categories as $categorie): ?>
                            <option value="<?= htmlspecialchars($categorie) ?>"><?= htmlspecialchars($categorie) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label class="cyber-label">
                        <input type="checkbox" id="editDisponible"> Disponible
                    </label>
                </div>
                <button type="submit" class="cyber-button">💾 Enregistrer</button>
            </form>
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

        setTimeout(function() {
            $('.success-message').fadeOut();
        }, 8000);
    });

    function deleteLivre(id) {
        if(confirm("⚠️ Êtes-vous sûr de vouloir supprimer ce livre ?")) {
            $.post('livres.php', { 
                supprimer_livre: true, 
                id_livre: id,
                csrf_token: '<?= $_SESSION['csrf_token'] ?>'
            }, function(response) {
                const data = JSON.parse(response);
                if(data.success) {
                    showSuccessMessage('🗑️ Livre supprimé avec succès !');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            }).fail(function(xhr) {
                alert('❌ Erreur: ' + xhr.responseText);
            });
        }
    }

    function editLivre(id) {
        $.ajax({
            url: 'ajax/get_livre.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(livre) {
                $('#editId').val(livre.id);
                $('#editTitre').val(livre.titre);
                $('#editAuteur').val(livre.auteur);
                $('#editIsbn').val(livre.isbn);
                $('#editCategorie').val(livre.categorie);
                $('#editDisponible').prop('checked', livre.disponible);
                
                $('#editModal').fadeIn(300);
            },
            error: function(xhr) {
                const error = JSON.parse(xhr.responseText);
                alert('❌ Erreur: ' + error.error);
            }
        });
    }

    $('#editForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            id: $('#editId').val(),
            titre: $('#editTitre').val(),
            auteur: $('#editAuteur').val(),
            isbn: $('#editIsbn').val(),
            categorie: $('#editCategorie').val(),
            disponible: $('#editDisponible').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: 'ajax/update_livre.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#editModal').fadeOut(300);
                showSuccessMessage('📚 Livre mis à jour avec succès ! 🎉');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        });
    });

    function showSuccessMessage(message) {
        const $msg = $(`
            <div class="success-flash">
                <span class="emoji">${message.match(/^\p{Emoji}+/u)[0]}</span>
                ${message}
            </div>
        `);
        $('body').append($msg);
        setTimeout(() => $msg.fadeOut(500, () => $msg.remove()), 3000);
    }

    $('.close-modal').click(function() {
        $('#editModal').fadeOut(300);
    });
    </script>

    <style>
    .cyber-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
    }

    .cyber-modal-content {
        position: relative;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(145deg, #0a0a2a, #1a1a4a);
        border: 2px solid var(--neon-pink);
        box-shadow: 0 0 50px rgba(255, 0, 255, 0.3);
        border-radius: 15px;
        padding: 30px;
        max-width: 500px;
        animation: modalEntry 0.5s ease;
    }

    @keyframes modalEntry {
        from { opacity: 0; transform: translate(-50%, -60%); }
        to { opacity: 1; transform: translate(-50%, -50%); }
    }

    .success-flash {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: rgba(0, 0, 0, 0.9);
        border: 2px solid #00ff00;
        color: #00ff00;
        padding: 20px 30px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 1.2rem;
        animation: slideIn 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28);
        backdrop-filter: blur(5px);
        z-index: 2000;
    }

    .success-flash .emoji {
        font-size: 1.5rem;
        filter: drop-shadow(0 0 5px #00ff00);
    }

    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .success-flash {
        animation: slideIn 0.5s, pulse 1.5s infinite 0.5s;
    }

    .input-group {
        margin: 1.5rem 0;
        position: relative;
    }

    .cyber-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        color: var(--neon-blue);
        text-shadow: 0 0 15px var(--neon-blue);
    }

    .cyber-input {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid var(--neon-blue);
        transition: all 0.3s ease;
    }

    .cyber-input:focus {
        border-color: var(--neon-pink);
        box-shadow: 0 0 20px var(--neon-pink);
    }

    .cyber-button[type="submit"] {
        background: linear-gradient(45deg, var(--neon-blue), var(--neon-pink));
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        margin-top: 1.5rem;
        transition: transform 0.3s ease;
    }

    .cyber-button[type="submit"]:hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px var(--neon-pink);
    }

    @keyframes inputPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    .cyber-input:focus {
        animation: inputPulse 1.5s infinite;
    }
    </style>
</body>
</html> 