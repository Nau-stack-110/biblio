<?php
session_start();
require_once 'includes/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Token CSRF invalide !');
    }
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $admin = $stmt->fetch();
    
    if($admin) {
        $_SESSION['admin'] = $admin;
        header("Location: index.php");
        exit;
    } else {
        $error = "🔐 Identifiants incorrecte !";
    }
}

if(isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

if(isset($_GET['logout'])): ?>
<div class="logout-message">
    <span>👋 Ravis de vous revoir !</span>
</div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin - Bibliothèque Cyberpunk</title>
    <link rel="stylesheet" href="styles/cyberpunk.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(45deg, #0a0a2a, #1a1a4a);
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        .cyber-login-box {
            background: rgba(0, 0, 0, 0.95);
            padding: 2rem 2.5rem;
            border: 3px solid #00f3ff;
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(255, 0, 255, 0.5);
            position: relative;
            width: 100%;
            max-width: 400px;
            transform-style: preserve-3d;
        }

        .cyber-login-box::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--neon-blue), var(--neon-pink));
            z-index: -1;
            animation: borderFlow 3s linear infinite;
        }

        @keyframes borderFlow {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .input-group {
            margin: 2rem 0;
            width: 100%;
        }

        .cyber-label {
            display: block;
            color: #00f3ff;
            margin-bottom: 1rem;
            font-size: 1.3rem;
            text-shadow: 0 0 15px #00f3ff;
            background: rgba(0, 0, 0, 0.7);
            padding: 8px 15px;
            border-radius: 5px;
            width: fit-content;
            transform: translateX(-10px);
        }

        .cyber-input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(0, 0, 0, 0.9);
            border: 2px solid #ff00ff;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            margin: 0 auto;
            display: block;
            width: 100%;
            max-width: 300px;
        }

        .cyber-input::placeholder {
            color: #666;
        }

        .cyber-button-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(45deg, #00f3ff, #ff00ff);
            border: none;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 5px;
            margin-top: 1.5rem;
            max-width: 300px;
            margin: 2rem auto 0;
            display: block;
        }

        .cyber-button-login:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px var(--neon-pink);
        }

        .error-message {
            color: #ff0000;
            text-align: center;
            margin: 1rem 0;
            text-shadow: 0 0 10px #ff0000;
            animation: errorPulse 1s infinite;
            background: rgba(255, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ff0000;
            font-size: 1.1rem;
            backdrop-filter: blur(5px);
        }

        @keyframes errorPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 480px) {
            .cyber-login-box {
                padding: 2rem 1.5rem;
            }
            
            .cyber-input {
                padding: 12px 15px;
                font-size: 1rem;
                max-width: 250px;
            }
            
            .cyber-label {
                font-size: 1.1rem;
            }
        }

        .logout-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            padding: 15px 25px;
            border-radius: 8px;
            animation: slideIn 0.5s ease-out;
            backdrop-filter: blur(5px);
        }

        .logout-message span {
            color: #00ff00;
            text-shadow: 0 0 10px #00ff00;
            font-size: 1.1rem;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .login-logo {
            text-align: center;
            animation: logoFloat 3s ease-in-out infinite;
        }

        .login-logo img {
            width: 150px;
            height: 150px;
            margin-bottom: 0.7rem;
            filter: drop-shadow(0 0 10px var(--neon-blue));
        }

        .login-logo h2 {
            color: var(--neon-pink);
            text-shadow: 0 0 15px var(--neon-pink);
            font-size: 2rem;
            margin: 0;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @media (max-width: 480px) {
            .login-logo img {
                width: 100px;
                height: 100px;
            }
        }

        .cyber-input:focus {
            animation: inputGlow 1.5s infinite alternate;
        }

        @keyframes inputGlow {
            from {
                box-shadow: 0 0 10px var(--neon-pink);
            }
            to {
                box-shadow: 0 0 20px var(--neon-blue);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="cyber-login-box">
            <div class="login-logo">
                <img src="images/logo-cyber-nakay.png" alt="CyberNakay Logo">
            </div>
            
            <div class="form-container">
                <?php if($error): ?>
                    <div class="error-message"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="input-group">
                        <label class="cyber-label">📧 Email</label>
                        <input type="email" name="email" class="cyber-input" placeholder="example@gmail.com" required>
                    </div>
                    
                    <div class="input-group">
                        <label class="cyber-label">🔑 Mot de passe</label>
                        <input type="password" name="password" class="cyber-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="cyber-button-login">
                        <span class="btn-content">🔓 Connexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 