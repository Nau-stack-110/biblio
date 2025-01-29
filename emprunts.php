<?php
require_once 'includes/db.php';

define('DUREE_EMPRUNT', 14);

if(isset($_POST['ajouter_emprunt'])) {
    $id_livre = $_POST['id_livre'];
    $id_abonne = $_POST['id_abonne'];
    $date_emprunt = date('Y-m-d');
    $date_retour_prevue = date('Y-m-d', strtotime("+".DUREE_EMPRUNT." days"));
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("SELECT disponible FROM livres WHERE id = ?");
        $stmt->execute([$id_livre]);
        $livre = $stmt->fetch();
        
        if(!$livre['disponible']) {
            throw new Exception("Ce livre n'est pas disponible");
        }
        
        $sql = "INSERT INTO emprunts (id_livre, id_abonne, date_emprunt, date_retour_prevue) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_livre, $id_abonne, $date_emprunt, $date_retour_prevue]);
        
        $pdo->commit();
        $message = "Emprunt enregistré avec succès";
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Traitement du retour d'un livre
if(isset($_POST['retourner_livre'])) {
    $id_emprunt = $_POST['id_emprunt'];
    $date_retour = date('Y-m-d');
    
    $sql = "UPDATE emprunts SET date_retour = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_retour, $id_emprunt]);
}

$query = "SELECT e.*, 
          l.titre as livre_titre, 
          CONCAT(a.nom, ' ', a.prenom) as abonne_nom,
          DATEDIFF(e.date_retour_prevue, CURRENT_DATE) as jours_restants
          FROM emprunts e
          JOIN livres l ON e.id_livre = l.id
          JOIN abonnes a ON e.id_abonne = a.id
          WHERE e.date_retour IS NULL
          ORDER BY e.date_retour_prevue ASC";
$emprunts = $pdo->query($query)->fetchAll();

$livres_dispo = $pdo->query("SELECT * FROM livres WHERE disponible = 1")->fetchAll();
$abonnes = $pdo->query("SELECT * FROM abonnes")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Emprunts</title>
    <link rel="stylesheet" href="styles/cyberpunk.css">
    <link rel="stylesheet" href="styles/emprunt.css">
</head>
<body>
    <a href="index.php" class="cyber-button"> 🏠 Retour à l'accueil</a>
    <?php include 'includes/nav.php'; ?>
    
    <div class="container">
        <?php if(isset($message)): ?>
            <div class="cyber-card success-message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="cyber-card error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="cyber-card">
            <h2>Nouvel Emprunt</h2>
            <form method="POST" class="cyber-form">
                <div class="form-group">
                    <select name="id_livre" class="cyber-input" required>
                        <option value="">Sélectionner un livre</option>
                        <?php foreach($livres_dispo as $livre): ?>
                            <option value="<?= $livre['id'] ?>">
                                <?= htmlspecialchars($livre['titre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="id_abonne" class="cyber-input" required>
                        <option value="">Sélectionner un abonné</option>
                        <?php foreach($abonnes as $abonne): ?>
                            <option value="<?= $abonne['id'] ?>">
                                <?= htmlspecialchars($abonne['nom'] . ' ' . $abonne['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="ajouter_emprunt" class="cyber-btn">Enregistrer l'emprunt</button>
            </form>
        </div>

        <div class="cyber-card">
            <h2>Emprunts en cours</h2>
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Abonné</th>
                        <th>Date d'emprunt</th>
                        <th>Date de retour prévue</th>
                        <th>Jours restants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($emprunts as $emprunt): ?>
                        <tr class="<?= $emprunt['jours_restants'] < 0 ? 'retard' : '' ?>">
                            <td><?= htmlspecialchars($emprunt['livre_titre']) ?></td>
                            <td><?= htmlspecialchars($emprunt['abonne_nom']) ?></td>
                            <td><?= htmlspecialchars($emprunt['date_emprunt']) ?></td>
                            <td><?= htmlspecialchars($emprunt['date_retour_prevue']) ?></td>
                            <td>
                                <?php if($emprunt['jours_restants'] < 0): ?>
                                    <span class="retard-badge">
                                        Retard de <?= abs($emprunt['jours_restants']) ?> jours
                                    </span>
                                <?php else: ?>
                                    <?= $emprunt['jours_restants'] ?> jours
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_emprunt" value="<?= $emprunt['id'] ?>">
                                    <button type="submit" name="retourner_livre" class="cyber-btn">
                                        Retourner
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('.success-message, .error-message');
        messages.forEach(msg => {
            setTimeout(() => {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            }, 3000);
        });
    });
    </script>
</body>
</html>
