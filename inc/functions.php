<?php
require("connection.php");

function myheader() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Site Examen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white text-center py-4 mb-4">
        <h1>Mon Site Examen</h1>
        <nav class="nav justify-content-center">
            <a class="nav-link text-white" href="../pages/inscription.php">Accueil</a>
            <a class="nav-link text-white" href="../pages/objets.php">Objets</a>
        </nav>
    </header>
    <main class="container mb-5">
HTML;
}

function myfooter() {
    echo <<<HTML
    </main>
    <footer class="bg-dark text-white text-center py-3 fixed-bottom">
        &copy; 2025 - Mon Site Examen
    </footer>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
}

function register($nom, $date_naissance, $genre, $email, $mdp) {
    $conn = dbConnect();
    
    // Validation des données
    if (!isValidEmail($email)) {
        return false;
    }
    
    if (!isValidPassword($mdp)) {
        return false;
    }
    
    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT id_membre FROM membre WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        return false; // Email déjà utilisé
    }
    
    $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO membre (nom, date_naissance, genre, email, mdp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nom, $date_naissance, $genre, $email, $mdp_hash);
    return $stmt->execute();
}

function login($email, $mdp) {
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

function getObjets($categorie = null) {
    $conn = dbConnect();
    $sql = "SELECT o.*, c.nom_categorie,
                   CASE 
                       WHEN e.id_emprunt IS NOT NULL AND e.date_retour IS NULL 
                       THEN 'Emprunt en cours'
                       ELSE 'Disponible'
                   END as statut_emprunt
            FROM objet o
            JOIN categorie_objet c ON o.id_categorie = c.id_categorie
            LEFT JOIN emprunt e ON o.id_objet = e.id_objet 
               AND e.date_retour IS NULL
            ";
    
    if ($categorie) {
        $sql .= " WHERE o.id_categorie = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categorie);
        $stmt->execute();
        return $stmt->get_result();
    } else {
        return $conn->query($sql);
    }
}

function getCategories() {
    $conn = dbConnect();
    return $conn->query("SELECT * FROM categorie_objet ORDER BY nom_categorie");
}

function emprunterObjet($id_objet, $id_membre) {
    $conn = dbConnect();
    
    // Vérifier si l'objet est disponible
    $stmt = $conn->prepare("SELECT id_objet FROM emprunt WHERE id_objet = ? AND date_retour IS NULL");
    $stmt->bind_param("i", $id_objet);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        return false; // Objet déjà emprunté
    }
    
    // Créer l'emprunt
    $date_emprunt = date('Y-m-d');
    $date_retour_prevue = date('Y-m-d', strtotime('+30 days')); // 30 jours par défaut
    
    $stmt = $conn->prepare("INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour_prevue) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_objet, $id_membre, $date_emprunt, $date_retour_prevue);
    
    return $stmt->execute();
}

function getMesEmprunts($id_membre) {
    $conn = dbConnect();
    $sql = "SELECT e.*, o.nom_objet, c.nom_categorie
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

function retournerObjet($id_emprunt, $id_membre) {
    $conn = dbConnect();
    
    // Vérifier que l'emprunt appartient bien au membre
    $stmt = $conn->prepare("SELECT id_emprunt FROM emprunt WHERE id_emprunt = ? AND id_membre = ? AND date_retour IS NULL");
    $stmt->bind_param("ii", $id_emprunt, $id_membre);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 0) {
        return false;
    }
    
    // Marquer comme retourné
    $date_retour = date('Y-m-d');
    $stmt = $conn->prepare("UPDATE emprunt SET date_retour = ? WHERE id_emprunt = ?");
    $stmt->bind_param("si", $date_retour, $id_emprunt);
    
    return $stmt->execute();
}

function getNomMembre($id_membre) {
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

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPassword($password) {
    return strlen($password) >= 6;
}

function isLoggedIn() {
    return isset($_SESSION['id_membre']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: objets.php');
        exit();
    }
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function isDatePassed($date) {
    return strtotime($date) < time();
}

function getEmpruntStats($id_membre) {
    $conn = dbConnect();
    $sql = "SELECT 
                COUNT(*) as total_emprunts,
                SUM(CASE WHEN date_retour IS NULL THEN 1 ELSE 0 END) as emprunts_en_cours,
                SUM(CASE WHEN date_retour IS NOT NULL THEN 1 ELSE 0 END) as emprunts_retournes,
                SUM(CASE WHEN date_retour IS NULL AND date_retour_prevue < CURDATE() THEN 1 ELSE 0 END) as emprunts_en_retard
            FROM emprunt 
            WHERE id_membre = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_membre);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
?>