<?php
require_once 'includes/db.php';

// Statistiques des livres
$stmt = $pdo->query("SELECT COUNT(*) as total FROM livres");
$livres = $stmt->fetch()['total'];

// Statistiques des abonnés
$stmt = $pdo->query("SELECT COUNT(*) as total FROM abonnes");
$abonnes = $stmt->fetch()['total'];

// Statistiques des emprunts
$stmt = $pdo->query("SELECT COUNT(*) as total FROM emprunts WHERE date_retour IS NULL");
$emprunts_en_cours = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bibliothèque Cyberpunk</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
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
            margin: 20px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Bibliothèque Cyberpunk</h1>
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
        <a href="livres.php" class="cyber-link">Gestion des Livres</a>
        <a href="abonnes.php" class="cyber-link">Gestion des Abonnés</a>
        <a href="emprunts.php" class="cyber-link">Gestion des Emprunts</a>
    </div>
</body>
</html> 