<?php require __DIR__ . '/../../layouts/header.php'; ?>
<h1>Créer une nouvelle course</h1>

<div class="form-container">
    
    <form method="post" action="/courses/create">

        <label>Conducteur :</label>
        <select name="conducteur_id" required>
            <?php foreach($conducteurs as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['nom'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Moto :</label>
        <select name="moto_id" required>
            <?php foreach($motos as $m): ?>
                <option value="<?= $m['id'] ?>"><?= $m['matricule'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Date :</label>
        <input type="date" name="date" required><br><br>

        <label>Heure Début :</label>
        <input type="time" name="heure_debut" required><br><br>

        <label>Heure Fin :</label>
        <input type="time" name="heure_fin" required><br><br>

        <label>Km :</label>
        <input type="number" step="0.01" name="km" required><br><br>

        <label>Montant payé :</label>
        <input type="number" step="0.01" name="montant_paye" required><br><br>

        <label>Lieu départ :</label>
        <input type="text" name="lieu_depart" required><br><br>

        <label>Lieu arrivée :</label>
        <input type="text" name="lieu_arrivee" required><br><br>

        <button type="submit">Créer la course</button>
    </form>
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>