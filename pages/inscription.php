<?php

session_start();
require('../inc/functions.php');
myheader();

if (isset($_SESSION['id_membre'])) {
    header('Location: objets.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (register($_POST['nom'], $_POST['date_naissance'], $_POST['genre'], $_POST['email'],  $_POST['mdp'])) {
       
        header('Location: objets.php');
        exit();
    } else {
        echo "Erreur lors de l'inscription.";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container">
    <h2>Inscription</h2>
    <form method="post">
        <input class="form-control mb-2" name="nom" placeholder="Nom" required>
        <input class="form-control mb-2" type="date" name="date_naissance" required>
        <select class="form-control mb-2" name="genre">
            <option value="H">Homme</option>
            <option value="F">Femme</option>
        </select>
        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-2" type="password" name="mdp" placeholder="Mot de passe" required>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</body>

</html>

<?php
myfooter();
?>