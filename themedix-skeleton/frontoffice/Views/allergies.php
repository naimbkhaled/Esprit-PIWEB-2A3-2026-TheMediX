<?php $pageTitle = 'The Medix - Allergies'; $currentPage = 'allergies'; ?>
<!DOCTYPE html>
<html lang="fr">
<head><?php require_once 'includes/head.php'; ?></head>
<body>
<?php require_once 'includes/navbar.php'; ?>

<div class="mx-page">
    <h1>Allergies et intolérances</h1>
    <p>Substances à éviter pour le patient</p>

    <div class="mx-bar">
        <input class="mx-input" type="text" placeholder="Rechercher une substance...">
        <select class="mx-input"><option>Tous les types</option><option>Allergie</option><option>Intolérance</option></select>
        <button class="mx-btn primary" data-toggle="#form">+ Ajouter une allergie</button>
    </div>

    <form class="mx-form" id="form" hidden>
        <div class="mx-grid">
            <div><label>Substance</label><input class="mx-input" type="text" placeholder="Pénicilline, lactose..."></div>
            <div><label>Type</label><select class="mx-input"><option>Allergie</option><option>Intolérance</option></select></div>
            <div><label>Sévérité</label><select class="mx-input"><option>Légère</option><option>Modérée</option><option>Sévère</option></select></div>
        </div>
        <div class="mx-grid"><div><label>Réaction</label><textarea class="mx-input" rows="3" placeholder="Éruption cutanée..."></textarea></div></div>
        <button type="button" class="mx-btn primary">Enregistrer</button>
        <button type="reset" class="mx-btn">Annuler</button>
    </form>

    <table class="mx-table">
        <thead><tr><th>#</th><th>Substance</th><th>Type</th><th>Sévérité</th><th>Réaction</th><th class="end">Actions</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Pénicilline</td><td><span class="mx-badge red">Allergie</span></td><td><span class="mx-badge red">Sévère</span></td><td>Difficultés respiratoires</td>
                <td class="end"><button class="mx-btn small">Modifier</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
            <tr><td>2</td><td>Lactose</td><td><span class="mx-badge blue">Intolérance</span></td><td><span class="mx-badge yellow">Modérée</span></td><td>Troubles digestifs</td>
                <td class="end"><button class="mx-btn small">Modifier</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
            <tr><td>3</td><td>Aspirine</td><td><span class="mx-badge red">Allergie</span></td><td><span class="mx-badge green">Légère</span></td><td>Éruption cutanée</td>
                <td class="end"><button class="mx-btn small">Modifier</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php require_once 'includes/scripts.php'; ?>
</body>
</html>