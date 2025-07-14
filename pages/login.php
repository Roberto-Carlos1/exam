<?php
session_start();
require('../inc/functions.php');

// Si déjà connecté, rediriger
if (isset($_SESSION['id_membre'])) {
    header('Location: objets.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];

    if (empty($email) || empty($mdp)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $id_membre = login($email, $mdp);
        if ($id_membre) {
            $_SESSION['id_membre'] = $id_membre;
            header('Location: objets.php');
            exit();
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}

myheader();
?>

<div class="container">
    <h2>Connexion</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= ($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <input class="form-control" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input class="form-control" type="password" name="mdp" placeholder="Mot de passe" required>
        </div>
        <button class="btn btn-primary">Se connecter</button>
    </form>

    <div class="mt-3">
        <a href="inscription.php">Créer un compte</a>
    </div>
</div>

<?php
myfooter();
?>