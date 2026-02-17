<?php
$pageTitle = 'Attribution des dons';
$activeMenu = 'attribution';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Attribution des dons']
];
require_once __DIR__ . '/../layouts/header.php';
?>

        <div class="main-content-inner">

            
            <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Succès !</strong> Le don a été attribué avec succès.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['success']) && $_GET['success'] == 'delete'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Succès !</strong> L'attribution a été annulée.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur(s) :</strong>
                    <ul class="mb-0 mt-2">
                        <?php 
                        $errors = $_SESSION['attribution_errors'] ?? [];
                        foreach ($errors as $error): 
                        ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['attribution_errors']); ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Stock disponible par type de don</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Type de don</th>
                                            <th>Catégorie</th>
                                            <th>Quantité disponible</th>
                                            <th>Unité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($donsDisponibles)): ?>
                                            <?php foreach($donsDisponibles as $don): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($don['besoin_nom']) ?></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?= htmlspecialchars($don['type']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong><?= number_format($don['quantite_disponible'], 2) ?></strong>
                                                    </td>
                                                    <td>-</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    Aucun don disponible dans le stock
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="header-title">Besoins par ville</h4>
                    <p class="text-muted">Cliquez sur "Attribuer" pour affecter des dons à un besoin</p>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Ville</th>
                                    <th>Besoin</th>
                                    <th>Quantité demandée</th>
                                    <th>Déjà attribué</th>
                                    <th>Reste</th>
                                    <th>Unité</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($besoins)): ?>
                                    <?php foreach($besoins as $b): ?>
                                        <?php 
                                            $reste = $b['reste'] ?? 0;
                                            $rowClass = $reste > 0 ? '' : 'table-success';
                                        ?>
                                        <tr class="<?= $rowClass ?>">
                                            <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                            <td>
                                                <?= htmlspecialchars($b['besoin_nom'] ?? '') ?>
                                                <small class="text-muted">(<?= htmlspecialchars($b['besoin_type'] ?? '') ?>)</small>
                                            </td>
                                            <td class="text-right"><?= number_format($b['besoin_quantite'] ?? 0, 2) ?></td>
                                            <td class="text-right"><?= number_format($b['quantite_attribuee'] ?? 0, 2) ?></td>
                                            <td class="text-right <?= $reste > 0 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                                <?= number_format($reste, 2) ?>
                                            </td>
                                            <td><?= htmlspecialchars($b['unite'] ?? '-') ?></td>
                                            <td class="text-center">
                                                <?php if($reste > 0): ?>
                                                    <a href="/attributions/attribuer/<?= $b['id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-share"></i> Attribuer
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Satisfait</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Aucun besoin enregistré</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Historique des attributions</h4>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Ville</th>
                                    <th>Besoin</th>
                                    <th>Quantité attribuée</th>
                                    <th>Don source</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($historique)): ?>
                                    <?php foreach($historique as $h): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($h['date_attribution'])) ?></td>
                                            <td><?= htmlspecialchars($h['ville_nom'] ?? '') ?></td>
                                            <td>
                                                <?= htmlspecialchars($h['besoin_nom'] ?? '') ?>
                                                <small class="text-muted">(<?= htmlspecialchars($h['besoin_type'] ?? '') ?>)</small>
                                            </td>
                                            <td class="text-right">
                                                <strong><?= number_format($h['quantite'], 2) ?></strong>
                                                <small><?= htmlspecialchars($h['unite'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                Don #<?= $h['don_id'] ?> - 
                                                <?= number_format($h['don_quantite'], 2) ?> total
                                            </td>
                                            <td class="text-center">
                                                <a href="/attributions/delete/<?= $h['id'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Annuler cette attribution ? Le stock sera remis à disposition.')"
                                                   title="Annuler">
                                                    <i class="fa fa-undo"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune attribution pour le moment</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>