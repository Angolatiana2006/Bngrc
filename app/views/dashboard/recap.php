<?php
$pageTitle = 'Récapitulatif financier';
$activeMenu = 'recap';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Récapitulatif']
];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="header-title">Récapitulatif financier global</h4>
                    <button class="btn btn-primary" onclick="refreshStats()">
                        <i class="fa fa-refresh"></i> Actualiser
                    </button>
                </div>
                <p class="text-muted">Dernière mise à jour: <span id="timestamp"><?= date('d/m/Y H:i:s') ?></span></p>
            </div>
        </div>
    </div>
</div>


<div class="row" id="statsContainer">
    <!-- Besoins -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Besoins totaux</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['total_besoins_montant'], 2) ?> Ar
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Besoins satisfaits</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['total_satisfait_montant'], 2) ?> Ar
                        </div>
                        <div class="text-xs">
                            <?= $stats['pourcentage_satisfait'] ?>% des besoins
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dons en argent -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Dons en argent reçus</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['total_dons_argent'], 2) ?> Ar
                        </div>
                        <div class="text-xs">
                            Utilisé: <?= number_format($stats['total_argent_utilise'], 2) ?> Ar<br>
                            Restant: <?= number_format($stats['total_argent_restant'], 2) ?> Ar
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-money fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dons en nature -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Dons en nature (valeur)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['total_dons_nature_valeur'], 2) ?> Ar
                        </div>
                        <div class="text-xs">
                            Distribué: <?= number_format($stats['total_nature_utilise'], 2) ?> Ar<br>
                            Restant: <?= number_format($stats['total_nature_restant_valeur'], 2) ?> Ar
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-gift fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Tableau des achats par ville -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Montants d'achat par ville</h5>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Ville</th>
                                <th>Nombre d'achats</th>
                                <th>Montant total acheté</th>
                            </tr>
                        </thead>
                        <tbody id="villeAchatsTable">
                            <?php foreach($achatsParVille as $ville): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ville['ville_nom']) ?></td>
                                    <td class="text-center"><?= $ville['nombre_achats'] ?></td>
                                    <td class="text-right">
                                        <strong><?= number_format($ville['total_achete'], 2) ?> Ar</strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
function refreshStats() {
    $('#statsContainer').append('<div class="col-12 text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
    
    $.ajax({
        url: '/achats/recap-ajax',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                
                $('#timestamp').text(response.timestamp);
                
                
                var stats = response.stats;
                var html = `
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Besoins totaux</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${stats.total_besoins_montant.toLocaleString()} Ar
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Besoins satisfaits</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${stats.total_satisfait_montant.toLocaleString()} Ar
                                        </div>
                                        <div class="text-xs">
                                            ${stats.pourcentage_satisfait}% des besoins
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Dons en argent reçus</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${stats.total_dons_argent.toLocaleString()} Ar
                                        </div>
                                        <div class="text-xs">
                                            Utilisé: ${stats.total_argent_utilise.toLocaleString()} Ar<br>
                                            Restant: ${stats.total_argent_restant.toLocaleString()} Ar
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-money fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Dons en nature (valeur)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${stats.total_dons_nature_valeur.toLocaleString()} Ar
                                        </div>
                                        <div class="text-xs">
                                            Distribué: ${stats.total_nature_utilise.toLocaleString()} Ar<br>
                                            Restant: ${stats.total_nature_restant_valeur.toLocaleString()} Ar
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-gift fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#statsContainer').html(html);
                
                
                var tableHtml = '';
                response.achatsParVille.forEach(function(ville) {
                    tableHtml += `
                        <tr>
                            <td>${ville.ville_nom}</td>
                            <td class="text-center">${ville.nombre_achats}</td>
                            <td class="text-right"><strong>${ville.total_achete.toLocaleString()} Ar</strong></td>
                        </tr>
                    `;
                });
                $('#villeAchatsTable').html(tableHtml);
            }
        },
        error: function() {
            alert('Erreur lors de l\'actualisation des données');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>