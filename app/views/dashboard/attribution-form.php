<?php
$pageTitle = 'Formulaire d\'attribution';
$activeMenu = 'attribution';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Attributions', 'url' => '/attributions'],
    ['label' => 'Attribuer']
];
require_once __DIR__ . '/../layouts/header.php';
?>

    <div class="main-content">
        <!-- HEADER (identique) -->
        <div class="header-area">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="nav-btn pull-left">
                        <span></span><span></span><span></span>
                    </div>
                </div>
                <div class="col-md-6 clearfix">
                    <div class="user-profile pull-right">
                        <h4 class="user-name">Administrateur BNGRC</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- TITRE -->
        <div class="page-title-area">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">Attribuer un don</h4>
                </div>
                <div class="col-sm-6">
                    <div class="breadcrumbs pull-right">
                        <a href="/dashboard">Tableau de bord</a> <span>/</span> 
                        <a href="/attributions">Attributions</a> <span>/</span> 
                        Formulaire
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content-inner">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Attribution pour : <?= htmlspecialchars($besoin['ville_nom']) ?></h4>
                    
                    <div class="alert alert-info">
                        <strong>Détail du besoin :</strong><br>
                        <?= htmlspecialchars($besoin['besoin_nom']) ?> (<?= htmlspecialchars($besoin['besoin_type']) ?>)<br>
                        Quantité totale demandée : <?= number_format($besoin['quantite'], 2) ?><br>
                        Quantité restante à attribuer : <strong><?= number_format($quantiteRestante, 2) ?></strong>
                    </div>

                    <form method="POST" action="/attributions/attribuer">
                        <input type="hidden" name="besoin_id" value="<?= $besoin['id'] ?>">
                        
                        <div class="form-group">
                            <label for="don_id">Sélectionnez le don à attribuer</label>
                            <select class="form-control" id="don_id" name="don_id" required>
                                <option value="">-- Choisissez un don --</option>
                                <?php foreach($donsDisponibles as $don): ?>
                                    <option value="<?= $don['id'] ?>">
                                        <?= htmlspecialchars($don['besoin_nom']) ?> - 
                                        Stock: <?= number_format($don['quantite_disponible'], 2) ?> 
                                        (reçu le <?= date('d/m/Y', strtotime($don['date_don'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantite">Quantité à attribuer</label>
                            <input type="number" step="0.01" min="0.01" 
                                   max="<?= $quantiteRestante ?>" 
                                   class="form-control" 
                                   id="quantite" 
                                   name="quantite" 
                                   required>
                            <small class="text-muted">Maximum: <?= number_format($quantiteRestante, 2) ?></small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> Confirmer l'attribution
                        </button>
                        <a href="/attributions" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Annuler
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Mettre à jour le max de la quantité en fonction du don sélectionné
    $('#don_id').change(function() {
        var selected = $(this).find('option:selected');
        // Cette fonctionnalité nécessiterait de stocker les quantités disponibles
        // Pour l'instant, on garde la validation côté serveur
    });
});
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

