<?php require __DIR__ . '/../../layouts/header.php'; ?>
<body>

<div class="report-container">

    <h1>Rapport des courses</h1>

    <form method="GET" action="/courses/report_list">
        <label>Du :</label>
        <input type="date" name="date_debut"
            <?php
            if (isset($dateDebut)) {
                echo 'value="' . $dateDebut . '"';
            }
            ?>
        >

        <label>Au :</label>
        <input type="date" name="date_fin"
            <?php
            if (isset($dateFin)) {
                echo 'value="' . $dateFin . '"';
            }
            ?>
        >

        <button type="submit">Filtrer</button>
        <a href="/courses" class="btn-retour">Retour</a>
    </form>

    <h2>Total Recette</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Total Recettes</th>
            <th>Total Dépenses</th>
            <th>Total Bénéfices</th>
        </tr>
        <tr>
            <td>
                <strong>
                    <?php
                    if (isset($totals['total_recette'])) {
                        echo $totals['total_recette'];
                    } else {
                        echo 0;
                    }
                    ?>
                </strong>
            </td>

            <td>
                <strong>
                    <?php
                    if (isset($totals['total_depense'])) {
                        echo $totals['total_depense'];
                    } else {
                        echo 0;
                    }
                    ?>
                </strong>
            </td>

            <td>
                <strong>
                    <?php
                    if (isset($totals['total_benefice'])) {
                        echo $totals['total_benefice'];
                    } else {
                        echo 0;
                    }
                    ?>
                </strong>
            </td>
        </tr>
    </table>

    <br>
    

    <h2>Total Recette Par Date</h2>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th>Date</th>
            <th>Recette</th>
            <th>Dépense</th>
            <th>Bénéfice</th>
        </tr>

        <?php if (empty($rows)): ?>
            <tr>
                <td colspan="4" align="center">Aucune donnée</td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['recette'] ?></td>
                    <td><?= $row['depense'] ?></td>
                    <td><?= $row['benefice'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

</div>

</body>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
</html>
