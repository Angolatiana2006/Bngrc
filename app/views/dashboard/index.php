<?php
$pageTitle = 'Tableau de bord financier';
$activeMenu = 'dashboard';
$breadcrumbs = [
    ['label' => 'Tableau de bord']
];
require_once __DIR__ . '/../layouts/header.php';
?>


<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title text-white"> Total besoins</h6>
                <h3><?= number_format($stats['total_besoins'], 2) ?> Ar</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title text-white"> Total satisfait</h6>
                <h3><?= number_format($stats['total_satisfait'], 2) ?> Ar</h3>
                <small><?= $stats['pourcentage_satisfait'] ?>% des besoins</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title text-white"> Dons en argent</h6>
                <h3><?= number_format($stats['total_dons_argent'], 2) ?> Ar</h3>
                <small>Utilisé: <?= number_format($stats['total_argent_utilise'], 2) ?> Ar</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title text-white"> Dons en nature</h6>
                <h3><?= number_format($stats['total_dons_nature'], 2) ?> Ar</h3>
                <small>Distribué: <?= number_format($stats['total_nature_utilisee'], 2) ?> Ar</small>
            </div>
        </div>
    </div>
</div>




<div class="card mb-4">
    <div class="card-body">
        <h4 class="header-title">Synthèse financière par ville</h4>

        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Ville</th>
                        <th>Besoin</th>
                        <th>Quantité</th>
                        <th>Prix unit.</th>
                        <th>Montant total</th>
                        <th>Déjà attribué</th>
                        <th>Achats effectués</th>
                        <th>Reste à satisfaire</th>
                    </tr>
                </thead>
                <tbody>
                        <?php 
                        $villeCourante = '';
                        $totalVille = 0;
                        $totalAttribueVille = 0;
                        $totalAchatsVille = 0;
                        
                        
                        $achatsParVilleMap = [];
                        foreach($achatsParVille as $a) {
                            $achatsParVilleMap[$a['ville_nom']] = [
                                'total' => $a['total_achete'],
                                'nombre' => $a['nombre_achats']
                            ];
                        }
                        ?>
                        
                        <?php if(!empty($besoinsParVille)): ?>
                            <?php foreach($besoinsParVille as $b): ?>
                                <?php if($villeCourante != $b['ville_nom'] && $villeCourante != ''): ?>
                                    
                                    <tr class="table-info">
                                        <td colspan="5" class="text-right"><strong>Total <?= $villeCourante ?> :</strong></td>
                                        <td class="text-right"><strong><?= number_format($totalAttribueVille, 2) ?> Ar</strong></td>
                                        <td class="text-right"><strong><?= number_format($totalAchatsVille, 2) ?> Ar</strong></td>
                                        <td class="text-right"><strong><?= number_format($totalVille - $totalAttribueVille - $totalAchatsVille, 2) ?> Ar</strong></td>
                                    </tr>
                                    <?php 
                                    $totalVille = 0;
                                    $totalAttribueVille = 0;
                                    $totalAchatsVille = 0;
                                    ?>
                                <?php endif; ?>
                                
                                <tr>
                                    <td><?= htmlspecialchars($b['ville_nom']) ?></td>
                                    <td><?= htmlspecialchars($b['besoin_nom']) ?> (<?= htmlspecialchars($b['besoin_type']) ?>)</td>
                                    <td class="text-right"><?= number_format($b['quantite_demandee'], 2) ?> <?= $b['unite'] ?></td>
                                    <td class="text-right"><?= number_format($b['prix_unitaire'], 2) ?> Ar</td>
                                    <td class="text-right"><?= number_format($b['montant_total_besoin'], 2) ?> Ar</td>
                                    <td class="text-right text-success"><?= number_format($b['montant_satisfait_dons'], 2) ?> Ar</td>
                                    <td class="text-right text-info">
                                        <?php 
                                        
                                        $montantAchat = isset($achatsParVilleMap[$b['ville_nom']]) ? $achatsParVilleMap[$b['ville_nom']]['total'] : 0;
                                        echo number_format($montantAchat, 2) . ' Ar';
                                        
                                       
                                        $totalAchatsVille += $montantAchat;
                                        ?>
                                    </td>
                                    <td class="text-right <?= ($b['montant_restant'] > 0) ? 'text-danger' : 'text-success' ?>">
                                        <strong><?= number_format($b['montant_restant'], 2) ?> Ar</strong>
                                    </td>
                                </tr>
                                
                                <?php 
                                $villeCourante = $b['ville_nom'];
                                $totalVille += $b['montant_total_besoin'];
                                $totalAttribueVille += $b['montant_satisfait_dons'];
                                ?>
                            <?php endforeach; ?>
                            
                            
                            <tr class="table-info">
                                <td colspan="5" class="text-right"><strong>Total <?= $villeCourante ?> :</strong></td>
                                <td class="text-right"><strong><?= number_format($totalAttribueVille, 2) ?> Ar</strong></td>
                                <td class="text-right"><strong><?= number_format($totalAchatsVille, 2) ?> Ar</strong></td>
                                <td class="text-right"><strong><?= number_format($totalVille - $totalAttribueVille - $totalAchatsVille, 2) ?> Ar</strong></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Aucun besoin enregistré</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                
                
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="4" class="text-right">TOTAUX GÉNÉRAUX :</th>
                        <th class="text-right"><?= number_format($stats['total_besoins'], 2) ?> Ar</th>
                        <th class="text-right"><?= number_format($stats['total_satisfait_dons'], 2) ?> Ar</th>
                        <th class="text-right"><?= number_format($stats['total_achats'], 2) ?> Ar</th>
                        <th class="text-right"><?= number_format($stats['total_besoins'] - $stats['total_satisfait'], 2) ?> Ar</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- TABLEAU DES ACHATS PAR VILLE -->
<div class="card mb-4">
    <div class="card-body">
        <h4 class="header-title">Détail des achats par ville</h4>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Ville</th>
                        <th>Nombre d'achats</th>
                        <th>Quantité totale achetée</th>
                        <th>Montant total des achats</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($achatsParVille)): ?>
                        <?php foreach($achatsParVille as $a): ?>
                            <?php if($a['nombre_achats'] > 0): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['ville_nom']) ?></td>
                                <td class="text-center"><?= $a['nombre_achats'] ?></td>
                                <td class="text-right"><?= number_format($a['quantite_totale_achetee'], 2) ?></td>
                                <td class="text-right"><strong><?= number_format($a['total_achete'], 2) ?> Ar</strong></td>
                                
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun achat effectué</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php require_once __DIR__ . '/../layouts/footer.php'; ?>