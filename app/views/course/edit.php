<?php require __DIR__ . '/../../layouts/header.php'; ?>
<div class="form-container">
    <form method="post" action="/courses/update/<?= $course['id'] ?>">
        <label>Affectation :</label>
        <select name="affectation_id" required>
            <?php foreach($affectations as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['id'] == $course['affectation_id'] ? 'selected' : '' ?>>
                    <?= $a['conducteur_nom'] ?> - <?= $a['moto_matricule'] ?> - <?= $a['date'] ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Heure début :</label>
        <input type="time" name="heure_debut" value="<?= $course['heure_debut'] ?>" required><br>

        <label>Heure fin :</label>
        <input type="time" name="heure_fin" value="<?= $course['heure_fin'] ?>" required><br>

        <label>Km :</label>
        <input type="number" step="0.01" name="km" value="<?= $course['km'] ?>" required><br>

        <label>Montant payé :</label>
        <input type="number" step="0.01" name="montant_paye" value="<?= $course['montant_paye'] ?>" required><br>

        <label>Lieu départ :</label>
        <input type="text" name="lieu_depart" value="<?= $course['lieu_depart'] ?>" required><br>

        <label>Lieu arrivée :</label>
        <input type="text" name="lieu_arrivee" value="<?= $course['lieu_arrivee'] ?>" required><br>

        <button type="submit">Modifier</button>
    </form>
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
