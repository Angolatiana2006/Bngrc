<?php
$pageTitle = 'Liste des dons';
$activeMenu = 'dons_liste';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Liste des dons']
];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="main-content-inner">

    <!-- MESSAGES DE SUCCÈS -->
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Succès !</strong> Le don a été ajouté avec succès.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- BOUTON D'AJOUT -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="/dons/create" class="btn btn-primary">
                <i class="fa fa-plus"></i> Ajouter un nouveau don
            </a>
            <a href="/dons/disponibles" class="btn btn-info">
                <i class="fa fa-check"></i> Voir les dons disponibles
            </a>
        </div>
    </div>

    <!-- TABLEAU DES DONS -->
    <div class="card">
        <div class="card-body">
            <h4 class="header-title">Stock BNGRC - Tous les dons</h4>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Type de don</th>
                            <th>Catégorie</th>
                            <th>Quantité reçue</th>
                            <th>Quantité attribuée</th>
                            <th>Montant utilisé (achats)</th>
                            <th>Stock disponible</th>
                            <th>Date de réception</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($dons)): ?>
                            <?php foreach($dons as $don): ?>
                                <?php 
                                    // Calculer le disponible en tenant compte des attributions ET des achats
                                    $quantite_attribuee = $don['quantite_attribuee'] ?? 0;
                                    $montant_achats = $don['montant_utilise_achats'] ?? 0;
                                    
                                    // Pour les dons en argent, le disponible est en montant
                                    if ($don['type'] === 'argent') {
                                        $disponible = $don['quantite'] - $montant_achats;
                                        $unite = 'AR';
                                    } else {
                                        // Pour les dons en nature, le disponible est en quantité
                                        $disponible = $don['quantite'] - $quantite_attribuee;
                                        $unite = '';
                                    }
                                ?>
                                <tr>
                                    <td><?= $don['id'] ?></td>
                                    <td><?= htmlspecialchars($don['besoin_nom'] ?? '') ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= htmlspecialchars($don['type'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <?= number_format($don['quantite'], 2) ?>
                                        <?= $don['type'] === 'argent' ? 'AR' : '' ?>
                                    </td>
                                    <td class="text-right">
                                        <?= number_format($quantite_attribuee, 2) ?>
                                        <?= $don['type'] === 'argent' ? 'AR' : '' ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if($don['type'] === 'argent'): ?>
                                            <span class="text-info">
                                                <?= number_format($montant_achats, 2) ?> AR
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right <?= $disponible > 0 ? 'text-success' : 'text-muted' ?>">
                                        <strong><?= number_format($disponible, 2) ?></strong>
                                        <?= $don['type'] === 'argent' ? 'AR' : '' ?>
                                    </td>
                                    <td>
                                        <?php if(isset($don['date_don'])): ?>
                                            <?= date('d/m/Y H:i', strtotime($don['date_don'])) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="/dons/edit/<?= $don['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="/dons/delete/<?= $don['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Aucun don enregistré</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>