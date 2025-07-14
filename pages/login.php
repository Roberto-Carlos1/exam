<?php
session_start();
require('../inc/functions.php');

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

myheader();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0">Connexion</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($email) ?>" 
                               placeholder="Votre adresse email" required>
                    </div>

                    <div class="mb-3">
                        <label for="mdp" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mdp" name="mdp" 
                               placeholder="Votre mot de passe" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="inscription.php" class="text-decoration-none">Pas encore inscrit ? Créer un compte</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
myfooter();
?>