# 📚 Bibliothèque CyberNakay 🚀

![Bannière CyberNakay](images/logo-cyber-nakay.png)

**Système de gestion de bibliothèque futuriste** avec interface cyberpunk et fonctionnalités modernes.

## 🌟 Fonctionnalités

- 🎨 **Interface Cyberpunk** avec animations néon
- 📖 Gestion des livres (`CRUD complet`)
- 👥 Gestion des abonnés
- 🔄 Suivi des emprunts
- 🔒 Système d'authentification sécurisé
- 📱 Design responsive
- 📊 Tableau de bord statistique
- 🔎 Recherche intelligente
- 🚨 Notifications de retard

## 🛠 Technologies

- ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
- ![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
- ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
- ![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)
- ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

## 🎮 Démo

[![Voir la démo](https://img.shields.io/badge/YouTube-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](pas dispo)

## 🚀 Installation

1. Cloner le dépôt
```bash
git clone https://github.com/Nau-stack-110/biblio.git
```

2. Configurer la base de données
```sql
CREATE DATABASE bibliotheque;
USE bibliotheque;
SOURCE bibliotheque.sql;
```

3. Configurer les paramètres
```php
// includes/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bibliotheque');
```

4. Lancer le serveur
```bash
php -S localhost:8000
```

## 🖼 Captures d'écran

| ![Tableau de bord](screenshots/dashboard.png) | ![Gestion des livres](screenshots/book.png) |
|-----------------------------------------------|---------------------------------------------|
| ![Connexion](screenshots/login.png)           | ![Emprunts](screenshots/emprunt.png)          |

## 🔒 Sécurité

- 🔑 Hachage bcrypt
- 🛡 Protection CSRF
- 🔄 Sessions sécurisées
- 🚫 Injection SQL prévenue
- 🔒 Middleware d'authentification

## 📜 License

[MIT License](LICENSE) - © 2025 Biblio CyberNakay 

---

<div align="center">
  Made with ❤️ by [Naunau] | 
  ![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)(https://github.com/Nau-stack-110)
</div>