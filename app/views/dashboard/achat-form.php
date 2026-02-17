
<?php
$pageTitle = 'Acheter des articles';
$activeMenu = 'achats';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Achats', 'url' => '/achats'],
    ['label' => 'Nouvel achat']
];
require_once __DIR__ . '/../layouts/header.php';


$totalArgentDisponible = 0;
foreach($donsArgent as $don) {
    $totalArgentDisponible += $don['quantite_disponible'];
}
?>


<?php if(isset($_GET['error']) && $_GET['error'] == 1): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreur(s) :</strong>
        <ul class="mb-0 mt-2">
            <?php 
            $errors = $_SESSION['achat_errors'] ?? [];
            foreach ($errors as $error): 
            ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
            <?php unset($_SESSION['achat_errors']); ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>


<?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Succès !</strong> L'achat a été effectué avec succès. Le stock a été mis à jour.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>


<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-white"> Porte-monnaie BNGRC</h5>
                        <p class="card-text h2"><?= number_format($totalArgentDisponible, 2) ?> AR</p>
                    </div>
                    <div>
                        <i class="fa fa-money fa-4x"></i>
                    </div>
                </div>
                <small class="text-white">Ce montant correspond au total des dons en argent disponibles</small>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <h4 class="header-title">Acheter des articles pour réapprovisionner le stock</h4>
        
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> 
            Le prix unitaire et le total se mettent à jour automatiquement.
        </div>
        
        <form method="POST" action="/achats/create" class="mt-4" id="achatForm">
            
            
            <div class="form-group">
                <label for="besoin_type_id">Article à acheter <span class="text-danger">*</span></label>
                <select class="form-control" id="besoin_type_id" name="besoin_type_id" required>
                    <option value="">-- Sélectionnez un article --</option>
                    <?php foreach($prixUnitaires as $prix): ?>
                        <option value="<?= $prix['besoin_type_id'] ?>" 
                                data-prix="<?= $prix['prix_unitaire'] ?>"
                                data-nom="<?= htmlspecialchars($prix['besoin_nom']) ?>">
                            <?= htmlspecialchars($prix['besoin_nom']) ?> 
                            (<?= htmlspecialchars($prix['besoin_type']) ?>) - 
                            <?= number_format($prix['prix_unitaire'], 2) ?> AR/unité
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            
            <div class="form-group">
                <label for="quantite">Quantité à acheter <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" class="form-control" id="quantite" name="quantite" required>
            </div>

            
            <div class="form-group">
                <label for="ville_id">Ces achats sont destinés à quelle ville ? <span class="text-danger">*</span></label>
                <select class="form-control" id="ville_id" name="ville_id" required>
                    <option value="">-- Sélectionnez une ville --</option>
                    <?php foreach($villes as $ville): ?>
                        <option value="<?= $ville['id'] ?>" <?= (isset($selected_ville) && $selected_ville == $ville['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ville['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">La ville qui recevra ces articles</small>
            </div>

            
            <div class="form-group">
                <label>Prix unitaire</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" 
                           id="prix_unitaire_affiche"
                           value="0 AR" 
                           readonly 
                           style="font-weight: bold; color: #007bff;">
                    <div class="input-group-append">
                        <span class="input-group-text bg-info text-white" id="article_nom_affiche">
                            Article non sélectionné
                        </span>
                    </div>
                </div>
            </div>

            
            <div class="form-group">
                <label>Total à payer</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg font-weight-bold" 
                           id="total_payer_affiche"
                           value="0 AR" 
                           style="color: black;" 
                           readonly>
                    <div class="input-group-append">
                        <span class="input-group-text bg-secondary text-white" id="verification_solde">
                            ⏳ En attente
                        </span>
                    </div>
                </div>
            </div>

            
            <input type="hidden" name="montant_total" id="montant_total" value="0">
            <input type="hidden" name="prix_unitaire" id="prix_unitaire_hidden" value="0">
            <input type="hidden" name="don_id" value="auto">

            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="btnConfirmer" disabled>
                    <i class="fa fa-shopping-cart"></i> Confirmer l'achat
                </button>
                <a href="/achats" class="btn btn-secondary btn-lg">
                    <i class="fa fa-times"></i> Annuler
                </a>
            </div>

        </form>
    </div>
</div>



<?php
echo "<!-- DEBUG: totalArgentDisponible brut = " . $totalArgentDisponible . " -->";
echo "<!-- DEBUG: totalArgentDisponible type = " . gettype($totalArgentDisponible) . " -->";
echo "<!-- DEBUG: totalArgentDisponible * 1 = " . ($totalArgentDisponible * 1) . " -->";
?>
<script>
console.log("DEBUG PHP:");
console.log("totalArgentDisponible brut: '<?= $totalArgentDisponible ?>'");
console.log("totalArgentDisponible type: <?= gettype($totalArgentDisponible) ?>");
console.log("totalArgentDisponible * 1: <?= $totalArgentDisponible * 1 ?>");
console.log("json_encode: <?= json_encode($totalArgentDisponible) ?>");
console.log("floatval: <?= floatval($totalArgentDisponible) ?>");
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const totalArgent = <?= floatval($totalArgentDisponible) ?>;
    console.log("Total Argent (depuis floatval):", totalArgent);
    
    const besoinSelect = document.getElementById('besoin_type_id');
    const quantiteInput = document.getElementById('quantite');
    const prixUnitaireAffiche = document.getElementById('prix_unitaire_affiche');
    const articleNomAffiche = document.getElementById('article_nom_affiche');
    const totalPayerAffiche = document.getElementById('total_payer_affiche');
    const verificationSolde = document.getElementById('verification_solde');
    const btnConfirmer = document.getElementById('btnConfirmer');
    const montantTotalHidden = document.getElementById('montant_total');
    const prixUnitaireHidden = document.getElementById('prix_unitaire_hidden');
    
    function mettreAJour() {
        const selectedOption = besoinSelect.options[besoinSelect.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            prixUnitaireAffiche.value = '0 AR';
            articleNomAffiche.textContent = 'Article non sélectionné';
            totalPayerAffiche.value = '0 AR';
            verificationSolde.innerHTML = '⏳ En attente';
            verificationSolde.className = 'input-group-text bg-secondary text-white';
            montantTotalHidden.value = 0;
            prixUnitaireHidden.value = 0;
            btnConfirmer.disabled = true;
            return;
        }
        
        
        const prix = parseFloat(selectedOption.dataset.prix) || 0;
        const nom = selectedOption.dataset.nom || 'Article';
        const quantite = parseFloat(quantiteInput.value) || 0;
        
        
        const total = quantite * prix;
        
        console.log("Prix:", prix, "Quantité:", quantite, "Total:", total);
        console.log("Comparaison:", total, ">", totalArgent, "?", total > totalArgent);
        
        
        prixUnitaireAffiche.value = prix.toFixed(2) + ' AR';
        articleNomAffiche.textContent = nom;
        prixUnitaireHidden.value = prix;
        
        if (quantite > 0 && prix > 0) {
            totalPayerAffiche.value = total.toFixed(2) + ' AR';
            montantTotalHidden.value = total;
            
            
            if (total > totalArgent) {
                totalPayerAffiche.style.color = 'red';
                verificationSolde.innerHTML = ' Solde insuffisant';
                verificationSolde.className = 'input-group-text bg-danger text-white';
                btnConfirmer.disabled = true;
            } else {
                totalPayerAffiche.style.color = 'green';
                verificationSolde.innerHTML = ' Solde suffisant';
                verificationSolde.className = 'input-group-text bg-warning';
                btnConfirmer.disabled = false;
            }
        } else {
            totalPayerAffiche.value = '0 AR';
            verificationSolde.innerHTML = quantite <= 0 ? '⏳ Entrez une quantité' : '⏳ En attente';
            verificationSolde.className = 'input-group-text bg-secondary text-white';
            montantTotalHidden.value = 0;
            btnConfirmer.disabled = true;
        }
    }
    
    besoinSelect.addEventListener('change', mettreAJour);
    quantiteInput.addEventListener('input', mettreAJour);
    mettreAJour();
});
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>