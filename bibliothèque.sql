-- Création de la base de données
CREATE DATABASE IF NOT EXISTS bibliotheque;
USE bibliotheque;

-- Structure de la table abonnes
CREATE TABLE IF NOT EXISTS abonnes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    date_inscription DATETIME DEFAULT CURRENT_DATE
);

-- Structure de la table livres
CREATE TABLE IF NOT EXISTS livres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    isbn VARCHAR(13) UNIQUE,
    categorie VARCHAR(50) NOT NULL,
    disponible BOOLEAN DEFAULT true
);

-- Structure de la table emprunts
CREATE TABLE IF NOT EXISTS emprunts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_livre INT,
    id_abonne INT,
    date_emprunt DATE NOT NULL,
    date_retour_prevue DATE NOT NULL,
    date_retour DATE,
    FOREIGN KEY (id_livre) REFERENCES livres(id) ON DELETE CASCADE,
    FOREIGN KEY (id_abonne) REFERENCES abonnes(id) ON DELETE CASCADE
);

-- Insertion de données de test pour les abonnés
INSERT INTO abonnes (nom, prenom, email, telephone) VALUES
('Dupont', 'Jean', 'jean.dupont@email.com', '0123456789'),
('Martin', 'Marie', 'marie.martin@email.com', '0234567890'),
('Bernard', 'Sophie', 'sophie.bernard@email.com', '0345678901'),
('Petit', 'Pierre', 'pierre.petit@email.com', '0456789012'),
('Robert', 'Alice', 'alice.robert@email.com', '0567890123');

-- Insertion de données de test pour les livres
INSERT INTO livres (titre, auteur, isbn, categorie, disponible) VALUES
('Les Misérables', 'Victor Hugo', '9780140862072', 'Roman classique', true),
('1984', 'George Orwell', '9780451524935', 'Science-fiction', true),
('Le Petit Prince', 'Antoine de Saint-Exupéry', '9782070612758', 'Conte', true),
('L''Étranger', 'Albert Camus', '9782070360024', 'Roman', true),
('Madame Bovary', 'Gustave Flaubert', '9782070413119', 'Roman classique', true),
('Le Seigneur des Anneaux', 'J.R.R. Tolkien', '9782070612887', 'Fantasy', true),
('Harry Potter à l''école des sorciers', 'J.K. Rowling', '9782070541270', 'Fantasy', true),
('Don Quichotte', 'Miguel de Cervantes', '9782070413126', 'Roman classique', true),
('Les Fleurs du Mal', 'Charles Baudelaire', '9782070413133', 'Poésie', true),
('Le Rouge et le Noir', 'Stendhal', '9782070413140', 'Roman classique', true);

-- Insertion de données de test pour les emprunts
INSERT INTO emprunts (id_livre, id_abonne, date_emprunt, date_retour_prevue, date_retour) VALUES
(1, 1, '2024-03-01', '2024-03-15', '2024-03-14'),
(2, 2, '2024-03-05', '2024-03-19', NULL),
(3, 3, '2024-03-10', '2024-03-24', NULL),
(4, 4, '2024-02-28', '2024-03-13', '2024-03-12'),
(5, 5, '2024-03-08', '2024-03-22', NULL);

-- Création d'un trigger pour mettre à jour la disponibilité des livres
DELIMITER //
CREATE TRIGGER after_emprunt_insert
AFTER INSERT ON emprunts
FOR EACH ROW
BEGIN
    UPDATE livres SET disponible = FALSE
    WHERE id = NEW.id_livre;
END//

CREATE TRIGGER after_emprunt_update
AFTER UPDATE ON emprunts
FOR EACH ROW
BEGIN
    IF NEW.date_retour IS NOT NULL AND OLD.date_retour IS NULL THEN
        UPDATE livres SET disponible = TRUE
        WHERE id = NEW.id_livre;
    END IF;
END//
DELIMITER ;
