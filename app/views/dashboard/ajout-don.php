<?php
$pageTitle = 'Ajouter un don';
$activeMenu = 'don';
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Dons', 'url' => '/dons'],
    ['label' => 'Ajouter un don']
];
require_once __DIR__ . '/../layouts/header.php';
?>

        

        <div class="main-content-inner">

            
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

           
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Nouveau don</h4>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        Vous pouvez soit sélectionner un type de don existant dans la liste, soit saisir un nouveau type.
                    </div>
                    
                    <form method="POST" action="/dons/create" class="mt-4">
                        
                        
                        <div class="form-group">
                            <label for="besoin_type_id">Type de don existant</label>
                            <select class="form-control" id="besoin_type_id" name="besoin_type_id">
                                <option value="">-- Sélectionnez un type existant (optionnel) --</option>
                                <?php foreach($typesBesoins as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= (isset($old['besoin_type_id']) && $old['besoin_type_id'] == $type['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?> (<?= htmlspecialchars($type['type']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Laissez vide si vous voulez créer un nouveau type</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        
                        <h5 class="text-success">Ou créez un nouveau type :</h5>
                        
                        <div class="form-group">
                            <label for="type_categorie">Catégorie du nouveau type</label>
                            <select class="form-control" id="type_categorie" name="type_categorie">
                                <option value="">-- Sélectionnez une catégorie --</option>
                                <option value="nature" <?= (isset($old['type_categorie']) && $old['type_categorie'] == 'nature') ? 'selected' : '' ?>>Nature</option>
                                <option value="materiaux" <?= (isset($old['type_categorie']) && $old['type_categorie'] == 'materiaux') ? 'selected' : '' ?>>Matériaux</option>
                                <option value="argent" <?= (isset($old['type_categorie']) && $old['type_categorie'] == 'argent') ? 'selected' : '' ?>>Argent</option>
                                <option value="autres" <?= (isset($old['type_categorie']) && $old['type_categorie'] == 'autres') ? 'selected' : '' ?>>Autres</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nom_besoin">Nom du nouveau don</label>
                            <input type="text" class="form-control" id="nom_besoin" name="nom_besoin" 
                                   placeholder="Ex: riz, sucre, ciment, etc." 
                                   value="<?= htmlspecialchars($old['nom_besoin'] ?? '') ?>">
                            <small class="form-text text-muted">Remplissez ces deux champs seulement si c'est un nouveau type</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        
                        <div class="form-group">
                            <label for="quantite">Quantité <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="quantite" name="quantite" 
                                   value="<?= htmlspecialchars($old['quantite'] ?? '') ?>" required>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer le don
                            </button>
                            <a href="/dons" class="btn btn-secondary">
                                <i class="fa fa-list"></i> Voir la liste des dons
                            </a>
                            <a href="/dashboard" class="btn btn-light">
                                <i class="fa fa-arrow-left"></i> Retour au dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>