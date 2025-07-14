<?php
session_start();
require('../inc/functions.php');

redirectIfNotLoggedIn();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retourner'])) {
    $id_emprunt = intval($_POST['id_emprunt']);

    if (retournerObjet($id_emprunt, $_SESSION['id_membre'])) {
        $message = "Objet retourné avec succès !";
    } else {
        $error = "Erreur lors du retour de l'objet.";
    }
}

$emprunts = getMesEmprunts($_SESSION['id_membre']);
$stats = getEmpruntStats($_SESSION['id_membre']);

myheader();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Mes Emprunts</h2>
    <div>
        <span class="me-3">Bonjour, <strong><?= getNomMembre($_SESSION['id_membre']) ?></strong></span>
        <a href="objets.php" class="btn btn-primary btn-sm">Retour aux objets</a>
        <a href="../logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">Total emprunts</h5>
                <p class="card-text display-6 text-primary">
                    <?= $stats['total_emprunts'] ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">En cours</h5>
                <p class="card-text display-6 text-warning">
                    <?= $stats['emprunts_en_cours'] ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h5 class="card-title text-success">Retournés</h5>
                <p class="card-text display-6 text-success">
                    <?= $stats['emprunts_retournes'] ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">En retard</h5>
                <p class="card-text display-6 text-danger">
                    <?= $stats['emprunts_en_retard'] ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Historique des emprunts</h5>
    </div>
    <div class="card-body">
        <?php if ($emprunts->num_rows == 0): ?>
            <div class="alert alert-info">
                <h4>Aucun emprunt trouvé</h4>
                <p>Vous n'avez aucun emprunt pour le moment.</p>
                <a href="objets.php" class="btn btn-primary">Découvrir les objets disponibles</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Objet</th>
                            <th>Catégorie</th>
                            <th>Date d'emprunt</th>
                            <th>Date retour prévue</th>
                            <th>Date retour effectif</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($emprunt = $emprunts->fetch_assoc()) { ?>
                            <tr>
                                <td><?= sanitizeInput($emprunt['nom_objet']) ?></td>
                                <td><?= sanitizeInput($emprunt['nom_categorie']) ?></td>
                                <td><?= formatDate($emprunt['date_emprunt']) ?></td>
                                <td>
                                    <?php
                                    $date_prevue = $emprunt['date_retour_prevue'];
                                    $classe = (!$emprunt['date_retour'] && isDatePassed($date_prevue)) ? 'text-danger fw-bold' : '';
                                    ?>
                                    <span class="<?= $classe ?>">
                                        <?= formatDate($date_prevue) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $emprunt['date_retour'] ?
                                        formatDate($emprunt['date_retour']) :
                                        '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <?php if ($emprunt['date_retour']): ?>
                                        <span class="badge bg-success">Retourné</span>
                                    <?php elseif (isDatePassed($emprunt['date_retour_prevue'])): ?>
                                        <span class="badge bg-danger">En retard</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">En cours</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$emprunt['date_retour']): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id_emprunt" value="<?= $emprunt['id_emprunt'] ?>">
                                            <button type="submit" name="retourner" class="btn btn-success btn-sm"
                                                onclick="return confirm('Confirmer le retour de cet objet ?')">
                                                Retourner
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
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