<?php $pageTitle = 'The Medix - Médicaments'; $currentPage = 'medicaments'; ?>
<!DOCTYPE html>
<html lang="fr">
<head><?php require_once 'includes/head.php'; ?></head>
<body>
<?php require_once 'includes/navbar.php'; ?>

<div class="mx-page">
    <h1>Médicaments</h1>
    <p>Catalogue des médicaments disponibles</p>

    <div class="mx-bar">
        <input class="mx-input" type="text" placeholder="Rechercher un médicament...">
        <button class="mx-btn primary" data-toggle="#form">+ Ajouter un médicament</button>
    </div>

    <form class="mx-form" id="form" hidden>
        <div class="mx-grid">
            <div><label>Nom</label><input class="mx-input" type="text" placeholder="Ex : Paracétamol"></div>
            <div><label>Dosage</label><input class="mx-input" type="text" placeholder="Ex : 500 mg"></div>
            <div><label>Forme</label>
                <select class="mx-input"><option>Comprimé</option><option>Gélule</option><option>Sirop</option><option>Injection</option></select>
            </div>
        </div>
        <div class="mx-grid"><div><label>Description</label><textarea class="mx-input" rows="3"></textarea></div></div>
        <button type="button" class="mx-btn primary">Enregistrer</button>
        <button type="reset" class="mx-btn">Annuler</button>
    </form>

    <table class="mx-table">
        <thead><tr><th>#</th><th>Nom</th><th>Dosage</th><th>Forme</th><th>Description</th><th class="end">Actions</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Paracétamol</td><td>500 mg</td><td>Comprimé</td><td>Antalgique</td>
                <td class="end"><button class="mx-btn small">Modifier</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
            <tr><td>2</td><td>Amoxicilline</td><td>1 g</td><td>Gélule</td><td>Antibiotique</td>
                <td class="end"><button class="mx-btn small">Modifier</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
            <tr><td>3</td><td>Metformine</td><td>850 mg</td><td>Comprimé</td><td>Diabète type 2</td>
                <td class="end"><button class="mx-btn small">Modifier</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php require_once 'includes/scripts.php'; ?>
</body>
</html>