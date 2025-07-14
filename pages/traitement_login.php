<?php

require_once('../inc/functions.php'); 

if (isset($_SESSION['id_membre'])) {
    header('Location: objets.php');
    exit();
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mdp'] ?? '';

    if (empty($email)) {
        $error = "Veuillez saisir votre email.";
    } elseif (empty($mdp)) {
        $error = "Veuillez saisir votre mot de passe.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide.";
    } else {
        $id_membre = login($email, $mdp);
        if ($id_membre) {
            $_SESSION['id_membre'] = $id_membre;
            header('Location: objets.php');
            exit();
        } else {
            $error = "Email ou mot de passe incorrect.";
            $email = '';
        }
    }
}
?>