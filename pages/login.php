<?php
require_once('../inc/functions.php'); 
require_once('../pages/traitement_login.php'); 

myheader();
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h2 class="text-center mb-0">Connexion</h2>
            </div>
            <div class="card-body bg-light">
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
                        <button type="submit" class="btn btn-dark">Se connecter</button>
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
