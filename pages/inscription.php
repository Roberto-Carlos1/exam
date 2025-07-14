<?php
session_start();
require('../inc/functions.php');

// Si déjà connecté, rediriger vers objets
if (isset($_SESSION['id_membre'])) {
    header('Location: objets.php');
    exit();
}

$nom = $date_naissance = $genre = $email = $ville = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $nom = trim($_POST['nom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $mdp = $_POST['mdp'] ?? '';

    // Validation côté serveur
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
        // Tentative d'inscription
        $result = register($nom, $date_naissance, $genre, $email, $ville, $mdp);
        
        if ($result['success']) {
            $success = $result['message'];
            
            // Connexion automatique après inscription
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

myheader();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0">Inscription</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
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
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
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