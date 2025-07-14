<?php
session_start();
require('../inc/functions.php');

redirectIfNotLoggedIn();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_objet = $_POST['nom_objet'] ?? '';
    $id_categorie = intval($_POST['id_categorie'] ?? 0);
    $description = $_POST['description'] ?? '';
    $id_membre = $_SESSION['id_membre'];

    $result = ajouterObjet($nom_objet, $id_categorie, $description, $id_membre);

    if ($result['success']) {
        echo '<div class="alert alert-success">' . htmlspecialchars($result['message']) . '</div>';
    } else {
        echo '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
    }
}

$categories = getCategories();
myheader();
?>

<div class="container">
    <h2>Ajouter un objet</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom_objet" class="form-label">Nom de l'objet *</label>
            <input type="text" name="nom_objet" id="nom_objet" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="id_categorie" class="form-label">Catégorie *</label>
            <select name="id_categorie" id="id_categorie" class="form-select" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php while ($cat = $categories->fetch_assoc()) { ?>
                    <option value="<?= $cat['id_categorie'] ?>"><?= sanitizeInput($cat['nom_categorie']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (optionnel)</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Images (optionnel)</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter l'objet</button>
    </form>
</div>

<?php myfooter(); ?>
