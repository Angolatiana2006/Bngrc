<?php require __DIR__ . '/../../layouts/header.php'; ?>


<h1>Liste des Courses</h1>



<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            
            <th>Conducteur</th>
            <th>Moto</th>
            <th>Date</th>
            <th>Heure Début</th>
            <th>Heure Fin</th>
            <th>Km</th>
            <th>Montant payé</th>
            <th>Lieu départ</th>
            <th>Lieu arrivée</th>
            <th>Valide</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($courses as $course): ?>
            <tr>
                
                <td><?= $course['conducteur_nom'] ?></td>
                <td><?= $course['moto_matricule'] ?></td>
                <td><?= $course['date'] ?></td>
                <td><?= $course['heure_debut'] ?></td>
                <td><?= $course['heure_fin'] ?></td>
                <td><?= $course['km'] ?></td>
                <td><?= $course['montant_paye'] ?></td>
                <td><?= $course['lieu_depart'] ?></td>
                <td><?= $course['lieu_arrivee'] ?></td>
                <td><?= $course['valide'] ? 'Oui' : 'Non' ?></td>
                <td>
    <?php if(!$course['valide']): ?>
        
        <a href="/courses/validate/<?= $course['id'] ?>">Valider</a>

        
        <a href="/courses/edit/<?= $course['id'] ?>">Modifier</a>
    <?php else: ?>
        -
    <?php endif; ?>
</td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
