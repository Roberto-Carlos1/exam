<?php
session_start();
require('../inc/functions.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emprunter'])) {
    $id_objet = intval($_POST['id_objet']);
    $id_membre = $_SESSION['id_membre'];

    if (emprunterObjet($id_objet, $id_membre)) {
        $message = " Objet emprunté avec succès !";
    } else {
        $error = " Erreur lors de l'emprunt. L'objet n'est peut-être plus disponible.";
    }
}

$categories = getCategories();
$categorie = isset($_GET['cat']) ? intval($_GET['cat']) : null;
$objets = getObjets($categorie);

myheader();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"> Liste des Objets</h2>
    <div>
        <a href="mes_emprunts.php" class="btn btn-outline-primary btn-sm">Mes emprunts</a>
        <a href="../logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"> Filtrer par catégorie</h5>
    </div>
    <div class="card-body bg-light">
        <form method="get">
            <div class="row g-2">
                <div class="col-md-8">
                    <select name="cat" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Toutes les catégories --</option>
                        <?php
                        $categories->data_seek(0);
                        while ($row = $categories->fetch_assoc()) {
                        ?>
                            <option value="<?= $row['id_categorie'] ?>"
                                <?= $categorie == $row['id_categorie'] ? 'selected' : '' ?>>
                                <?= sanitizeInput($row['nom_categorie']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <?php if ($categorie): ?>
                        <a href="objets.php" class="btn btn-secondary w-100">Réinitialiser le filtre</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"> Objets disponibles</h5>
    </div>
    <div class="card-body bg-light">
        <?php if ($objets->num_rows == 0): ?>
            <div class="alert alert-info">
                Aucun objet trouvé dans cette catégorie.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Objet</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($obj = $objets->fetch_assoc()) { ?>
                            <tr>
                                <td class="fw-bold"><?= sanitizeInput($obj['nom_objet']) ?></td>
                                <td><?= sanitizeInput($obj['nom_categorie']) ?></td>
                                <td>
                                    <?php if ($obj['statut_emprunt'] === 'Emprunt en cours'): ?>
                                        <span class="badge bg-warning text-dark">Emprunté</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($obj['statut_emprunt'] !== 'Emprunt en cours'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id_objet" value="<?= $obj['id_objet'] ?>">
                                            <button type="submit" name="emprunter" class="btn btn-primary btn-sm"
                                                onclick="return confirm('Voulez-vous emprunter cet objet ?')">
                                                Emprunter
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Indisponible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
myfooter();
?>
