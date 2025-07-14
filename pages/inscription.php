<?php
require_once('../inc/functions.php'); 
require_once('../pages/traitement_incription.php'); 

myheader();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h2 class="text-center mb-0">Créer un compte</h2>
            </div>
            <div class="card-body bg-light">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom complet *</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="<?= htmlspecialchars($nom) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance *</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" 
                               value="<?= htmlspecialchars($date_naissance) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="genre" class="form-label">Genre *</label>
                        <select class="form-select" id="genre" name="genre" required>
                            <option value="">Sélectionnez votre genre</option>
                            <option value="H" <?= $genre === 'H' ? 'selected' : '' ?>>Homme</option>
                            <option value="F" <?= $genre === 'F' ? 'selected' : '' ?>>Femme</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($email) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ville" class="form-label">Ville *</label>
                        <input type="text" class="form-control" id="ville" name="ville" 
                               value="<?= htmlspecialchars($ville) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="mdp" class="form-label">Mot de passe *</label>
                        <input type="password" class="form-control" id="mdp" name="mdp" 
                               placeholder="Minimum 6 caractères" required>
                        <div class="form-text">Le mot de passe doit contenir au moins 6 caractères</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">S'inscrire</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">Déjà inscrit ? Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
myfooter();
?>
