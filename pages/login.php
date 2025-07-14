<?php

session_start();
require('../inc/functions.php');

myheader();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = login($_POST['email'], $_POST['mdp']);
    if ($id) {
        $_SESSION['id_membre'] = $id;
        header('Location: objets.php');
    } else {
        echo "Identifiants invalides.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h2>Connexion</h2>
    <form method="post">
        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-2" type="password" name="mdp" placeholder="Mot de passe" required>
        <button class="btn btn-success">Se connecter</button>
    </form>
    <a href="inscription.php">Créer un compte</a>
</body>
</html>

<?php
myfooter();
?>
