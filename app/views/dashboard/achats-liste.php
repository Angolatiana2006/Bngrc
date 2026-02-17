<?php
$pageTitle = 'Liste des achats';
$activeMenu = 'achats';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Achats']
];
require_once __DIR__ . '/../layouts/header.php';

// Calculer le solde actuel des dons en argent
$donsDisponibles = \app\models\Don::getDisponibles();
$totalArgentRestant = 0;
foreach($donsDisponibles as $don) {
    if ($don['type'] === 'argent') {
        $totalArgentRestant += $don['quantite_disponible'];
    }
}
?>

<!-- MESSAGES -->
<?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Succès !</strong> L'achat a été enregistré avec succès.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- SOLDE ACTUEL -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title text-white">💰 Solde actuel du porte-monnaie</h5>
                <p class="card-text h2"><?= number_format($totalArgentRestant, 2) ?> AR</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <a href="/achats/create" class="btn btn-primary mt-4">
            <i class="fa fa-plus"></i> Nouvel achat
        </a>
        <a href="/achats/recap" class="btn btn-info mt-4">
            <i class="fa fa-pie-chart"></i> Récapitulatif
        </a>
    </div>
</div>

<!-- TABLEAU DES ACHATS -->
<div class="card">
    <div class="card-body">
        <h4 class="header-title">Historique des achats</h4>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover">
                <!-- Dans le tableau, ajoutez une colonne pour la ville -->
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Ville destinataire</th>  <!-- Nouvelle colonne -->
                        <th>Article acheté</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Montant total</th>
                        <th>Don source</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($achats)): ?>
                        <?php foreach($achats as $a): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($a['date_achat'])) ?></td>
                                <td>
                                    <span class="badge badge-primary">
                                        <?= htmlspecialchars($a['ville_nom'] ?? 'Non assignée') ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($a['besoin_nom'] ?? '') ?>
                                    <small class="text-muted">(<?= htmlspecialchars($a['besoin_type'] ?? '') ?>)</small>
                                </td>
                                <td class="text-right"><?= number_format($a['quantite'], 2) ?></td>
                                <td class="text-right"><?= number_format($a['prix_unitaire'], 2) ?> Ar</td>
                                <td class="text-right">
                                    <strong><?= number_format($a['montant_total'], 2) ?> Ar</strong>
                                </td>
                                <td>
                                    Don #<?= $a['don_id'] ?> 
                                    (<?= number_format($a['don_quantite'], 2) ?> Ar)
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Aucun achat enregistré</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>