<?php

require_once('../inc/functions.php'); 

if (isset($_SESSION['id_membre'])) {
    header('Location: objets.php');
    exit();
}

$nom = $date_naissance = $genre = $email = $ville = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $nom = trim($_POST['nom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $mdp = $_POST['mdp'] ?? '';

    $errors = [];

    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire";
    }

    if (empty($date_naissance)) {
        $errors[] = "La date de naissance est obligatoire";
    }

    if (empty($genre)) {
        $errors[] = "Le genre est obligatoire";
    }

    if (empty($email)) {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($ville)) {
        $errors[] = "La ville est obligatoire";
    }

    if (empty($mdp)) {
        $errors[] = "Le mot de passe est obligatoire";
    } elseif (strlen($mdp) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }

    if (empty($errors)) {
        $result = register($nom, $date_naissance, $genre, $email, $ville, $mdp);

        if ($result['success']) {
            $success = $result['message'];

            $id_membre = login($email, $mdp);
            if ($id_membre) {
                $_SESSION['id_membre'] = $id_membre;
                header('Location: objets.php');
                exit();
            } else {
                $error = "Inscription réussie mais erreur lors de la connexion automatique. Veuillez vous connecter manuellement.";
            }
        } else {
            $error = $result['message'];
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

?>