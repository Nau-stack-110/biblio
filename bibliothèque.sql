CREATE DATABASE IF NOT EXISTS bibliotheque;
USE bibliotheque;

CREATE TABLE IF NOT EXISTS abonnes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NULL,
    date_inscription DATETIME DEFAULT CURRENT_DATE
);

CREATE TABLE IF NOT EXISTS livres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    isbn VARCHAR(13) UNIQUE,
    categorie VARCHAR(50) NOT NULL,
    disponible BOOLEAN DEFAULT true
);

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

CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL
);

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

INSERT INTO admin (nom, email, password) VALUES
('Arnaud', 'admin@gmail.com', 'Admin00@@');

-- trigger pour mettre à jour la disponibilité des livres
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
