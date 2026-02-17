<?php
$pageTitle = 'Vendre un don';
$activeMenu = 'dons_liste';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Dons', 'url' => '/dons'],
    ['label' => 'Vendre']
];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="main-content-inner">

    <!-- Message d'avertissement si le don est encore demandé -->
    <?php if($besoinsActifs['existe']): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠️ Attention !</strong> Ce don est encore demandé par des villes :
            <ul class="mb-0 mt-2">
                <li><?= htmlspecialchars($besoinsActifs['message']) ?></li>
            </ul>
            <p class="mt-2 mb-0">Vous ne pouvez pas vendre ce don tant que ces besoins ne sont pas satisfaits.</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h4 class="header-title">Vente de don</h4>
            
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> 
                Les dons sont vendus avec une remise de <strong><?= $remise ?>%</strong> par rapport au prix d'achat.
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Détails du don</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Type de don :</td>
                                    <td><strong><?= htmlspecialchars($don['besoin_nom']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Catégorie :</td>
                                    <td><span class="badge badge-info"><?= htmlspecialchars($don['type']) ?></span></td>
                                </tr>
                                <tr>
                                    <td>Quantité disponible :</td>
                                    <td><strong><?= number_format($don['quantite_disponible'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Prix d'achat unitaire :</td>
                                    <td><?= number_format($prix_achat, 2) ?> Ar</td>
                                </tr>
                                <tr>
                                    <td>Prix de vente unitaire :</td>
                                    <td><strong class="text-success"><?= number_format($prix_vente, 2) ?> Ar</strong></td>
                                </tr>
                                <tr>
                                    <td>Remise appliquée :</td>
                                    <td><span class="badge badge-warning"><?= $remise ?>%</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Formulaire de vente</h5>
                            
                            <?php if($besoinsActifs['existe']): ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    Vente impossible car ce don est encore demandé.
                                </div>
                            <?php else: ?>
                                <form method="POST" action="/ventes/vendre" id="venteForm">
                                    <input type="hidden" name="don_id" value="<?= $don['id'] ?>">
                                    <input type="hidden" name="besoin_type_id" value="<?= $don['besoin_type_id'] ?>">

                                    <div class="form-group">
                                        <label for="quantite">Quantité à vendre <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.01" 
                                               max="<?= $don['quantite_disponible'] ?>" 
                                               class="form-control" id="quantite" name="quantite" required>
                                        <small class="text-muted">Maximum: <?= number_format($don['quantite_disponible'], 2) ?></small>
                                    </div>

                                    <div class="form-group">
                                        <label>Prix de vente unitaire</label>
                                        <input type="text" class="form-control" id="prix_vente" 
                                               value="<?= number_format($prix_vente, 2) ?> Ar" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Montant total de la vente</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-lg font-weight-bold" 
                                                   id="montant_total" value="0 Ar" readonly 
                                                   style="color: green;">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-success text-white" id="aperçu">
                                                    À percevoir
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="montant_total" id="montant_total_hidden" value="0">

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-success btn-lg" id="btnVendre" disabled>
                                            <i class="fa fa-usd"></i> Confirmer la vente
                                        </button>
                                        <a href="/dons" class="btn btn-secondary btn-lg">
                                            <i class="fa fa-times"></i> Annuler
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantiteInput = document.getElementById('quantite');
    const prixVente = <?= $prix_vente ?>;
    const maxQuantite = <?= $don['quantite_disponible'] ?>;
    const montantTotal = document.getElementById('montant_total');
    const montantTotalHidden = document.getElementById('montant_total_hidden');
    const btnVendre = document.getElementById('btnVendre');

    function calculerMontant() {
        const quantite = parseFloat(quantiteInput.value) || 0;
        const total = quantite * prixVente;
        
        if (quantite > 0 && quantite <= maxQuantite) {
            montantTotal.value = total.toLocaleString() + ' Ar';
            montantTotalHidden.value = total;
            montantTotal.style.color = 'green';
            btnVendre.disabled = false;
        } else {
            montantTotal.value = '0 Ar';
            montantTotalHidden.value = 0;
            montantTotal.style.color = 'black';
            btnVendre.disabled = true;
        }
    }

    quantiteInput.addEventListener('input', calculerMontant);
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>