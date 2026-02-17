<?php require __DIR__ . '/../../layouts/header.php'; ?>
<h1>Confirmation de suppression</h1>
<div class="form-container">
    

    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form action="/courses/delete-all" method="POST">
        <p>Entrer le code de confirmation pour confirmer votre demande </p>
        
        <label for="confirmation_code">Code de confirmation</label>
        <input type="text" id="confirmation_code" name="confirmation_code" required>

        <button type="submit">Supprimer</button>
    </form>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
