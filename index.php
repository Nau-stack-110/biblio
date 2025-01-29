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
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            margin: 20px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid var(--neon-blue);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px var(--neon-blue);
        }

        .stat-number {
            font-size: 3em;
            color: var(--neon-blue);
            margin: 10px 0;
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .stat-label {
            font-size: 1.2em;
            color: #fff;
            text-transform: uppercase;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }

        .cyber-link {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, var(--dark-bg), rgba(0, 243, 255, 0.1));
            border: 2px solid var(--neon-pink);
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            font-size: 1.2em;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .cyber-link:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px var(--neon-pink);
            background: linear-gradient(45deg, rgba(0, 243, 255, 0.1), var(--dark-bg));
        }

        .header {
            text-align: center;
            padding: 40px 0;
            background: linear-gradient(180deg, var(--dark-bg), rgba(0, 0, 0, 0.8));
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 3em;
            color: var(--neon-blue);
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 0 0 10px var(--neon-blue);
            margin: 0;
        }

        .header p {
            color: var(--neon-pink);
            font-size: 1.2em;
            margin-top: 10px;
        }

        .cyber-link-logout {
            background: linear-gradient(45deg, #ff0000, #8b0000);
            border: 2px solid var(--neon-red);
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 auto;
        }

        .cyber-link-logout:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px var(--neon-red);
            background: linear-gradient(45deg, #8b0000, #ff0000);
        }

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
            background: rgba(0, 0, 0, 0.9);
            border: 3px solid var(--neon-blue);
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            text-align: center;
            animation: modalEntry 0.5s ease;
        }

        @keyframes modalEntry {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            color: var(--neon-pink);
            font-size: 28px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            text-shadow: 0 0 15px var(--neon-pink);
        }

        .cyber-loader {
            width: 40px;
            height: 40px;
            margin: 20px auto;
            border: 4px solid var(--neon-blue);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
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