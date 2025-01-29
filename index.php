<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once 'includes/db.php';

$stmt = $pdo->query("SELECT COUNT(*) as total FROM livres");
$livres = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM abonnes");
$abonnes = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM emprunts WHERE date_retour IS NULL");
$emprunts_en_cours = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bibliothèque Cyberpunk</title>
    <link rel="stylesheet" href="styles/cyberpunk.css">
    <link rel="stylesheet" href="styles/accueil.css">
    <style></style>
</head>
<body>
    <div class="header">
        <h1>Bibliothèque NAKAY</h1>
        <p>Système de Gestion Numérique</p>
    </div>

    <div class="dashboard">
        <div class="stat-card">
            <div class="stat-label">Livres Disponibles</div>
            <div class="stat-number"><?php echo $livres; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Abonnés Actifs</div>
            <div class="stat-number"><?php echo $abonnes; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Emprunts en Cours</div>
            <div class="stat-number"><?php echo $emprunts_en_cours; ?></div>
        </div>
    </div>

    <div class="nav-grid">
        <a href="livres.php" class="cyber-link">📚 Gestion des Livres</a>
        <a href="abonnes.php" class="cyber-link">👥 Gestion des Abonnés</a>
        <a href="emprunts.php" class="cyber-link">🔄 Gestion des Emprunts</a>
    </div>

    <button class="cyber-link-logout" id="logoutBtn">🚪 Déconnexion</button>

    <div id="logoutModal" class="cyber-modal">
        <div class="cyber-modal-content">
            <span class="close-modal">&times;</span>
            <h2>👋 Déconnexion réussie !</h2>
            <p>Vous allez être redirigé vers la page de connexion...</p>
            <div class="cyber-loader"></div>
        </div>
    </div>

    <script src="js/jquery-2.2.3.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#logoutBtn').click(function() {
            $('#logoutModal').fadeIn(300);
            
            $.ajax({
                url: 'logout.php',
                method: 'POST',
                data: { 
                    csrf_token: '<?= $_SESSION['csrf_token'] ?>'
                },
                success: function() {
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                }
            });
        });

        $('.close-modal').click(function() {
            $('#logoutModal').fadeOut(300);
        });
    });
    </script>
</body>
</html> 