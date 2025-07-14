<?php
session_start();
require('../inc/functions.php');

if (isset($_SESSION['id_membre'])) {
    header('Location: objets.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim(htmlspecialchars($_POST['nom']));
    $date_naissance = $_POST['date_naissance'];
    $genre = $_POST['genre'];
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $ville = trim(htmlspecialchars($_POST['ville']));
    $mdp = $_POST['mdp'];

    if (!$nom || !$date_naissance || !$genre || !$email || !$ville || strlen($mdp) < 6) {
        $error = "Veuillez remplir correctement tous les champs et respecter les contraintes.";
    } else {
        $result = register(
            $nom,
            $date_naissance,
            $genre,
            $email,
            $ville,
            $mdp
        );

        if ($result['success']) {
            $id_membre = login($email, $mdp);
            if ($id_membre) {
                $_SESSION['id_membre'] = $id_membre;
                header('Location: objets.php');
                exit();
            } else {
                $error = "Inscription réussie mais erreur lors de la connexion automatique.";
            }
        } else {
            $error = $result['message'];
        }
    }

    if ($result['success']) {
        $id_membre = login($_POST['email'], $_POST['mdp']);
        if ($id_membre) {
            $_SESSION['id_membre'] = $id_membre;
            header('Location: objets.php');
            exit();
        } else {
            $error = "Inscription réussie mais erreur lors de la connexion automatique.";
        }
    } else {
        $error = $result['message'];
    }
}

myheader();
?>

<div class="container">
    <h2>Inscription</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <input class="form-control" name="nom" placeholder="Nom complet" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date de naissance</label>
            <input class="form-control" type="date" name="date_naissance" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Genre</label>
            <select class="form-select" name="genre" required>
                <option value="">Sélectionnez</option>
                <option value="H">Homme</option>
                <option value="F">Femme</option>
            </select>
        </div>
        <div class="mb-3">
            <input class="form-control" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" name="ville" placeholder="Ville" required>
        </div>
        <div class="mb-3">
            <input class="form-control" type="password" name="mdp" placeholder="Mot de passe (min 6 caractères)" required>
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>

    <div class="mt-3">
        <a href="login.php">Déjà inscrit ? Se connecter</a>
    </div>
</div>

<?php
myfooter();
?>