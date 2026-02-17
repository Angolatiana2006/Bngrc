<?php
$pageTitle = 'Ajouter un besoin';
$activeMenu = 'besoin';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Ajouter un besoin']
];
require_once __DIR__ . '/../layouts/header.php';
?>

        

        <div class="main-content-inner">

            <!-- AFFICHAGE DES ERREURS -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur(s) :</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- FORMULAIRE D'AJOUT DE BESOIN -->
            <!-- FORMULAIRE D'AJOUT DE BESOIN -->
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Nouveau besoin</h4>
                    
                    <form method="POST" action="/besoins/create" class="mt-4">
                        <div class="form-group">
                            <label for="ville_id">Ville <span class="text-danger">*</span></label>
                            <select class="form-control" id="ville_id" name="ville_id" required>
                                <option value="">Sélectionnez une ville</option>
                                <?php foreach($villes as $ville): ?>
                                    <option value="<?= $ville['id'] ?>" <?= (isset($old['ville_id']) && $old['ville_id'] == $ville['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ville['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="besoin_type_id">Type de besoin <span class="text-danger">*</span></label>
                            <select class="form-control" id="besoin_type_id" name="besoin_type_id" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="1" <?= (isset($old['besoin_type_id']) && $old['besoin_type_id'] == 1) ? 'selected' : '' ?>>Nature (arbres, plantes)</option>
                                <option value="3" <?= (isset($old['besoin_type_id']) && $old['besoin_type_id'] == 3) ? 'selected' : '' ?>>Matériaux (ciment, bois)</option>
                                <option value="5" <?= (isset($old['besoin_type_id']) && $old['besoin_type_id'] == 5) ? 'selected' : '' ?>>Argent</option>
                                <option value="6" <?= (isset($old['besoin_type_id']) && $old['besoin_type_id'] == 6) ? 'selected' : '' ?>>Autres</option>
                            </select>
                            <small class="form-text text-muted">Sélectionnez la catégorie du besoin</small>
                        </div>

                        <div class="form-group">
                            <label for="nom_besoin">Nom du besoin <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom_besoin" name="nom_besoin" 
                                placeholder="Ex: riz, sucre, tôle, ciment, etc." 
                                value="<?= htmlspecialchars($old['nom_besoin'] ?? '') ?>" required>
                            <small class="form-text text-muted">Saisissez le nom précis du besoin (riz, sucre, tôle, etc.)</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="quantite">Quantité <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="quantite" name="quantite" 
                                    value="<?= htmlspecialchars($old['quantite'] ?? '') ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="unite">Unité</label>
                                <input type="text" class="form-control" id="unite" name="unite" 
                                    placeholder="kg, Ar, ..." value="<?= htmlspecialchars($old['unite'] ?? '') ?>">
                                <small class="form-text text-muted">Ex: kg, Ar, m², etc.</small>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer le besoin
                            </button>
                            <a href="/dashboard" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>