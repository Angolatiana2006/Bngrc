<?php
$pageTitle = 'Liste des ventes';
$activeMenu = 'ventes';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Ventes']
];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="main-content-inner">

    <!-- MESSAGES DE SUCCÈS -->
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Succès !</strong> La vente a été effectuée avec succès.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- STATISTIQUES DES VENTES -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">💰 Total des ventes</h5>
                    <h3><?= number_format($totalVentes, 2) ?> Ar</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">📊 Nombre de ventes</h5>
                    <h3><?= count($ventes) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">📦 Articles vendus</h5>
                    <h3>
                        <?php 
                        $totalArticles = 0;
                        foreach($ventes as $v) {
                            $totalArticles += $v['quantite'];
                        }
                        echo number_format($totalArticles, 2);
                        ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIQUES PAR TYPE -->
    <?php if(!empty($stats)): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ventes par type d'article</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Type d'article</th>
                                    <th>Nombre de ventes</th>
                                    <th>Quantité vendue</th>
                                    <th>Montant total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stats as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['besoin_nom']) ?></td>
                                    <td class="text-center"><?= $s['nombre_ventes'] ?></td>
                                    <td class="text-right"><?= number_format($s['quantite_totale'], 2) ?></td>
                                    <td class="text-right"><strong><?= number_format($s['montant_total'], 2) ?> Ar</strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TABLEAU DES VENTES -->
    <div class="card">
        <div class="card-body">
            <h4 class="header-title">Historique des ventes</h4>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Article vendu</th>
                            <th>Quantité</th>
                            <th>Prix achat (u.)</th>
                            <th>Prix vente (u.)</th>
                            <th>Remise</th>
                            <th>Montant total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($ventes)): ?>
                            <?php foreach($ventes as $v): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($v['date_vente'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($v['besoin_nom']) ?>
                                        <small class="text-muted">(<?= htmlspecialchars($v['besoin_type']) ?>)</small>
                                    </td>
                                    <td class="text-right"><?= number_format($v['quantite'], 2) ?></td>
                                    <td class="text-right"><?= number_format($v['prix_achat_unitaire'], 2) ?> Ar</td>
                                    <td class="text-right text-success">
                                        <strong><?= number_format($v['prix_vente_unitaire'], 2) ?> Ar</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">-<?= $v['pourcentage_remise'] ?>%</span>
                                    </td>
                                    <td class="text-right">
                                        <strong><?= number_format($v['montant_total_vente'], 2) ?> Ar</strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucune vente enregistrée</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>