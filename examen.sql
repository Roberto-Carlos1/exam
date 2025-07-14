-- 1️⃣ Créer la base de données
CREATE DATABASE IF NOT EXISTS examen;
USE examen;

-- 2️⃣ Créer les tables
CREATE TABLE membre (
    id_membre INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    date_naissance DATE,
    genre ENUM('H', 'F'),
    email VARCHAR(100),
    ville VARCHAR(50),
    mdp VARCHAR(255),
    image_profil VARCHAR(255)
);

CREATE TABLE categorie_objet (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(50)
);

CREATE TABLE objet (
    id_objet INT AUTO_INCREMENT PRIMARY KEY,
    nom_objet VARCHAR(100),
    id_categorie INT,
    id_membre INT,
    FOREIGN KEY (id_categorie) REFERENCES categorie_objet(id_categorie),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

CREATE TABLE images_objet (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT,
    nom_image VARCHAR(255),
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet)
);

CREATE TABLE emprunt (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT,
    id_membre INT,
    date_emprunt DATE,
    date_retour DATE,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
('Alice Dupont', '1995-04-12', 'F', 'alice@example.com', 'Paris', 'mdp123', 'alice.jpg'),
('Bob Martin', '1990-08-22', 'H', 'bob@example.com', 'Lyon', 'mdp456', 'bob.jpg'),
('Claire Durand', '1988-11-05', 'F', 'claire@example.com', 'Marseille', 'mdp789', 'claire.jpg'),
('David Petit', '1992-02-17', 'H', 'david@example.com', 'Toulouse', 'mdpabc', 'david.jpg');

INSERT INTO categorie_objet (nom_categorie) VALUES
('Esthétique'),
('Bricolage'),
('Mécanique'),
('Cuisine');

INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES

('Sèche-cheveux', 1, 1),
('Lisseur', 1, 1),
('Peinture murale', 2, 1),
('Tournevis', 2, 1),
('Perceuse', 2, 1),
('Clé à molette', 3, 1),
('Pompe à vélo', 3, 1),
('Mixeur', 4, 1),
('Grille-pain', 4, 1),
('Robot de cuisine', 4, 1),

('Tondeuse', 1, 2),
('Ponceuse', 2, 2),
('Scie sauteuse', 2, 2),
('Perforateur', 2, 2),
('Cric auto', 3, 2),
('Batterie voiture', 3, 2),
('Bouilloire', 4, 2),
('Machine à café', 4, 2),
('Moulin à café', 4, 2),
('Blender', 4, 2),

('Lime à ongles', 1, 3),
('Fer à friser', 1, 3),
('Pinceau peinture', 2, 3),
('Marteau', 2, 3),
('Visseuse', 2, 3),
('Cric moto', 3, 3),
('Pompe auto', 3, 3),
('Four', 4, 3),
('Micro-ondes', 4, 3),
('Cafetière', 4, 3),

('Rasoir', 1, 4),
('Tondeuse barbe', 1, 4),
('Pistolet à colle', 2, 4),
('Perceuse visseuse', 2, 4),
('Scie circulaire', 2, 4),
('Compresseur', 3, 4),
('Crick hydraulique', 3, 4),
('Plaque cuisson', 4, 4),
('Friteuse', 4, 4),
('Gaufrier', 4, 4);

INSERT INTO images_objet (id_objet, nom_image) VALUES

(1, 'seche_cheveux1.jpg'),
(1, 'seche_cheveux2.jpg'),
(2, 'lisseur1.jpg'),
(2, 'lisseur2.jpg'),
(3, 'peinture_murale1.jpg'),
(4, 'tournevis1.jpg'),
(4, 'tournevis2.jpg'),
(5, 'perceuse1.jpg'),
(5, 'perceuse2.jpg'),
(5, 'perceuse3.jpg'),
(6, 'cle_molette1.jpg'),
(7, 'pompe_velo1.jpg'),
(7, 'pompe_velo2.jpg'),
(8, 'mixeur1.jpg'),
(8, 'mixeur2.jpg'),
(9, 'grille_pain1.jpg'),
(10, 'robot_cuisine1.jpg'),
(10, 'robot_cuisine2.jpg'),
(10, 'robot_cuisine3.jpg'),

(11, 'tondeuse1.jpg'),
(11, 'tondeuse2.jpg'),
(12, 'ponceuse1.jpg'),
(12, 'ponceuse2.jpg'),
(13, 'scie_sauteuse1.jpg'),
(13, 'scie_sauteuse2.jpg'),
(14, 'perforateur1.jpg'),
(14, 'perforateur2.jpg'),
(15, 'cric_auto1.jpg'),
(15, 'cric_auto2.jpg'),
(16, 'batterie_voiture1.jpg'),
(17, 'bouilloire1.jpg'),
(18, 'machine_cafe1.jpg'),
(18, 'machine_cafe2.jpg'),
(19, 'moulin_cafe1.jpg'),
(20, 'blender1.jpg'),
(20, 'blender2.jpg'),

(21, 'lime_ongles1.jpg'),
(22, 'fer_friser1.jpg'),
(22, 'fer_friser2.jpg'),
(23, 'pinceau_peinture1.jpg'),
(24, 'marteau1.jpg'),
(24, 'marteau2.jpg'),
(25, 'visseuse1.jpg'),
(25, 'visseuse2.jpg'),
(26, 'cric_moto1.jpg'),
(27, 'pompe_auto1.jpg'),
(27, 'pompe_auto2.jpg'),
(28, 'four1.jpg'),
(28, 'four2.jpg'),
(28, 'four3.jpg'),
(29, 'micro_ondes1.jpg'),
(30, 'cafetiere1.jpg'),
(30, 'cafetiere2.jpg'),

(31, 'rasoir1.jpg'),
(32, 'tondeuse_barbe1.jpg'),
(32, 'tondeuse_barbe2.jpg'),
(33, 'pistolet_colle1.jpg'),
(34, 'perceuse_visseuse1.jpg'),
(34, 'perceuse_visseuse2.jpg'),
(35, 'scie_circulaire1.jpg'),
(35, 'scie_circulaire2.jpg'),
(35, 'scie_circulaire3.jpg'),
(36, 'compresseur1.jpg'),
(36, 'compresseur2.jpg'),
(37, 'crick_hydraulique1.jpg'),
(38, 'plaque_cuisson1.jpg'),
(38, 'plaque_cuisson2.jpg'),
(39, 'friteuse1.jpg'),
(39, 'friteuse2.jpg'),
(40, 'gaufrier1.jpg');

INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES

(1, 2, '2025-07-01', '2025-07-10'),
(2, 3, '2025-07-02', '2025-07-11'),
(5, 4, '2025-07-03', '2025-07-12'),
(12, 1, '2025-07-04', '2025-07-13'),
(15, 3, '2025-07-05', '2025-07-14'),
(18, 4, '2025-07-06', '2025-07-15'),
(21, 1, '2025-07-07', '2025-07-16'),
(25, 2, '2025-07-08', '2025-07-17'),
(31, 3, '2025-07-09', '2025-07-18'),
(35, 4, '2025-07-10', '2025-07-19'),

(8, 2, '2025-07-12', NULL),
(14, 1, '2025-07-13', NULL),
(22, 4, '2025-07-14', NULL),
(28, 1, '2025-07-15', NULL),
(36, 3, '2025-07-16', NULL),

(3, 2, '2025-06-20', '2025-06-25'),
(7, 3, '2025-06-22', '2025-06-30'),
(11, 4, '2025-06-25', '2025-07-05'),
(16, 1, '2025-06-28', '2025-07-08'),
(20, 3, '2025-06-30', '2025-07-09'),
(24, 2, '2025-07-01', '2025-07-08'),
(30, 4, '2025-07-03', '2025-07-12'),
(32, 1, '2025-07-05', '2025-07-14'),
(37, 2, '2025-07-08', '2025-07-17'),
(40, 3, '2025-07-11', '2025-07-20');

SELECT * FROM membre;

SELECT 
    o.nom_objet,
    c.nom_categorie,
    m.nom as proprietaire,
    m.ville
FROM objet o
JOIN categorie_objet c ON o.id_categorie = c.id_categorie
JOIN membre m ON o.id_membre = m.id_membre
ORDER BY m.nom, c.nom_categorie;

SELECT 
    e.id_emprunt,
    o.nom_objet,
    proprietaire.nom as proprietaire,
    emprunteur.nom as emprunteur,
    e.date_emprunt
FROM emprunt e
JOIN objet o ON e.id_objet = o.id_objet
JOIN membre proprietaire ON o.id_membre = proprietaire.id_membre
JOIN membre emprunteur ON e.id_membre = emprunteur.id_membre
WHERE e.date_retour IS NULL
ORDER BY e.date_emprunt;

SELECT 
    c.nom_categorie,
    COUNT(o.id_objet) as nombre_objets
FROM categorie_objet c
LEFT JOIN objet o ON c.id_categorie = o.id_categorie
GROUP BY c.id_categorie, c.nom_categorie
ORDER BY nombre_objets DESC;

SELECT 
    o.nom_objet,
    m.nom as proprietaire,
    GROUP_CONCAT(i.nom_image SEPARATOR ', ') as images
FROM objet o
JOIN membre m ON o.id_membre = m.id_membre
LEFT JOIN images_objet i ON o.id_objet = i.id_objet
GROUP BY o.id_objet, o.nom_objet, m.nom
ORDER BY o.nom_objet;

SELECT 
    m.nom,
    COUNT(e.id_emprunt) as total_emprunts,
    SUM(CASE WHEN e.date_retour IS NULL THEN 1 ELSE 0 END) as emprunts_en_cours
FROM membre m
LEFT JOIN emprunt e ON m.id_membre = e.id_membre
GROUP BY m.id_membre, m.nom
ORDER BY total_emprunts DESC;