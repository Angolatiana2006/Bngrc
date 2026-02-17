<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="form-container">
    <form action="/courses/update" method="POST">
        <h1>Modifier le prix de l'essence</h1>

        <?php if (isset($current['prix_par_litre'])): ?>
            <p>Prix actuel : <?= $current['prix_par_litre'] ?> Ar</p>
        <?php endif; ?>

        <label for="prix_par_litre">Nouveau prix par litre (Ar)</label>
        <input 
            type="number" 
            id="prix_par_litre" 
            name="prix_par_litre" 
            step="0.01" 
            min="0" 
            placeholder="Entrez le nouveau prix" 
            required
        >

        <button type="submit">Enregistrer</button>
    </form>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
