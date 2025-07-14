<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("connection.php");

function myheader()
{
    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emprunts d'Objets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        header {
            background: linear-gradient(135deg, #212529, #343a40);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        header h1 {
            font-size: 1.8rem;
            margin: 0;
        }
        nav a {
            font-weight: 500;
            color: #ffffff;
            margin-left: 1rem;
            transition: color 0.3s, text-decoration 0.3s;
        }
        nav a:hover {
            color: #ffc107;
            text-decoration: underline;
        }
        footer {
            background: #212529;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="text-white py-3">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="mb-0"> Emprunts d'Objets</h1>
            <nav class="nav">
                <a class="nav-link" href="../pages/inscription.php">Inscription</a>
                <a class="nav-link" href="../pages/login.php">Connexion</a>
            </nav>
        </div>
    </header>
    <main class="container my-5 flex-grow-1">
HTML;
}

function myfooter()
{
    echo <<<HTML
    </main>
    <footer class="text-white text-center py-4 mt-auto">
        &copy; 2025 Emprunts d'Objets. Tous droits réservés.
    </footer>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
}


function register($nom, $date_naissance, $genre, $email, $ville, $mdp)
{
    $conn = dbConnect();

    if (empty($nom) || empty($email) || empty($mdp) || empty($ville) || empty($date_naissance) || empty($genre)) {
        return ['success' => false, 'message' => 'Tous les champs sont obligatoires'];
    }

    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Email invalide'];
    }

    if (!isValidPassword($mdp)) {
        return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'];
    }

    $stmt = $conn->prepare("SELECT id_membre FROM membre WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return ['success' => false, 'message' => 'Cette adresse email est déjà utilisée'];
    }

    $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nom, $date_naissance, $genre, $email, $ville, $mdp_hash);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Inscription réussie'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
    }
}

function login($email, $mdp)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id_membre, mdp FROM membre WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();
        if (password_verify($mdp, $hash)) {
            return $id;
        }
    }
    return false;
}

function getObjetsFiltre($categorie = null, $nom = null, $dispo = false)
{
    $conn = dbConnect();
    $sql = "SELECT o.*, c.nom_categorie,
                CASE 
                    WHEN e.id_emprunt IS NOT NULL AND e.date_retour IS NULL THEN 'Emprunt en cours'
                    ELSE 'Disponible'
                END as statut_emprunt
            FROM objet o
            JOIN categorie_objet c ON o.id_categorie = c.id_categorie
            LEFT JOIN emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL
            WHERE 1=1";

    $params = [];
    $types = '';

    if ($categorie) {
        $sql .= " AND o.id_categorie = ?";
        $types .= "i";
        $params[] = $categorie;
    }

    if ($nom) {
        $sql .= " AND o.nom_objet LIKE ?";
        $types .= "s";
        $params[] = "%$nom%";
    }

    if ($dispo) {
        $sql .= " AND (e.id_emprunt IS NULL OR e.date_retour IS NOT NULL)";
    }

    $stmt = $conn->prepare($sql);

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result();
}


function getCategories()
{
    $conn = dbConnect();
    return $conn->query("SELECT * FROM categorie_objet ORDER BY nom_categorie");
}

function emprunterObjet($id_objet, $id_membre)
{
    $conn = dbConnect();

    $stmt = $conn->prepare("SELECT id_objet FROM emprunt WHERE id_objet = ? AND date_retour IS NULL");
    $stmt->bind_param("i", $id_objet);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return false;
    }

    $date_emprunt = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO emprunt (id_objet, id_membre, date_emprunt) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_objet, $id_membre, $date_emprunt);

    return $stmt->execute();
}

function getMesEmprunts($id_membre)
{
    $conn = dbConnect();
    $sql = "SELECT e.*, o.nom_objet, c.nom_categorie,
                   DATE_ADD(e.date_emprunt, INTERVAL 30 DAY) as date_retour_prevue
            FROM emprunt e
            JOIN objet o ON e.id_objet = o.id_objet
            JOIN categorie_objet c ON o.id_categorie = c.id_categorie
            WHERE e.id_membre = ?
            ORDER BY e.date_emprunt DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_membre);
    $stmt->execute();
    return $stmt->get_result();
}

function retournerObjet($id_emprunt, $id_membre)
{
    $conn = dbConnect();

    $stmt = $conn->prepare("SELECT id_emprunt FROM emprunt WHERE id_emprunt = ? AND id_membre = ? AND date_retour IS NULL");
    $stmt->bind_param("ii", $id_emprunt, $id_membre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        return false;
    }

    $date_retour = date('Y-m-d');
    $stmt = $conn->prepare("UPDATE emprunt SET date_retour = ? WHERE id_emprunt = ?");
    $stmt->bind_param("si", $date_retour, $id_emprunt);

    return $stmt->execute();
}

function getNomMembre($id_membre)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT nom FROM membre WHERE id_membre = ?");
    $stmt->bind_param("i", $id_membre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['nom'];
    }
    return "Utilisateur";
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPassword($password)
{
    return strlen($password) >= 6;
}

function isLoggedIn()
{
    return isset($_SESSION['id_membre']);
}

function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function redirectIfLoggedIn()
{
    if (isLoggedIn()) {
        header('Location: objets.php');
        exit();
    }
}

function logout()
{
    session_destroy();
    header('Location: login.php');
    exit();
}

function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function displayMessage($message, $type = 'info')
{
    $class = '';
    switch ($type) {
        case 'success':
            $class = 'alert-success';
            break;
        case 'error':
            $class = 'alert-danger';
            break;
        case 'warning':
            $class = 'alert-warning';
            break;
        default:
            $class = 'alert-info';
    }

    echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
    echo sanitizeInput($message);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

function formatDate($date)
{
    return date('d/m/Y', strtotime($date));
}

function isDatePassed($date)
{
    return strtotime($date) < time();
}

function getEmpruntStats($id_membre)
{
    $conn = dbConnect();
    $sql = "SELECT 
                COUNT(*) as total_emprunts,
                SUM(CASE WHEN date_retour IS NULL THEN 1 ELSE 0 END) as emprunts_en_cours,
                SUM(CASE WHEN date_retour IS NOT NULL THEN 1 ELSE 0 END) as emprunts_retournes,
                SUM(CASE WHEN date_retour IS NULL AND DATE_ADD(date_emprunt, INTERVAL 30 DAY) < CURDATE() THEN 1 ELSE 0 END) as emprunts_en_retard
            FROM emprunt 
            WHERE id_membre = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_membre);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

function enregistrerImage($id_objet, $nom_fichier, $is_principale = 0)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO image_objet (id_objet, nom_fichier, principale) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $id_objet, $nom_fichier, $is_principale);
    $stmt->execute();
}

function getImagePrincipale($id_objet)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT nom_fichier FROM image_objet WHERE id_objet = ? AND principale = 1 LIMIT 1");
    $stmt->bind_param("i", $id_objet);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return "../uploads/".$row['nom_fichier'];
    }
    return "../assets/default.png"; 
}

function supprimerImage($id_image, $id_objet)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT nom_fichier FROM image_objet WHERE id_image = ? AND id_objet = ?");
    $stmt->bind_param("ii", $id_image, $id_objet);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $fichier = "../uploads/".$row['nom_fichier'];
        if (file_exists($fichier)) {
            unlink($fichier);
        }
        $stmt = $conn->prepare("DELETE FROM image_objet WHERE id_image = ?");
        $stmt->bind_param("i", $id_image);
        $stmt->execute();
    }
}
function categorieExiste($id_categorie) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id_categorie FROM categorie_objet WHERE id_categorie = ?");
    $stmt->bind_param("i", $id_categorie);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

function ajouterObjet($nom_objet, $id_categorie, $description, $id_membre) {
    $conn = dbConnect();

    // Vérifier que la catégorie existe
    if (!categorieExiste($id_categorie)) {
        return ['success' => false, 'message' => "La catégorie sélectionnée n'existe pas."];
    }

    $stmt = $conn->prepare("INSERT INTO objet (nom_objet, id_categorie, description, id_membre) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        return ['success' => false, 'message' => "Erreur SQL: " . $conn->error];
    }

    $stmt->bind_param("sisi", $nom_objet, $id_categorie, $description, $id_membre);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => "Objet ajouté avec succès."];
    } else {
        return ['success' => false, 'message' => "Erreur lors de l'ajout de l'objet: " . $stmt->error];
    }
}
