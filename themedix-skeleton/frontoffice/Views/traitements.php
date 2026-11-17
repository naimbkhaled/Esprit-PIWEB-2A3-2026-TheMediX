<?php $pageTitle = 'The Medix - Traitements'; $currentPage = 'traitements'; ?>
<!DOCTYPE html>
<html lang="fr">
<head><?php require_once 'includes/head.php'; ?></head>
<body>
<?php require_once 'includes/navbar.php'; ?>

<div class="mx-page">
    <h1>Historique des traitements</h1>
    <p>Suivi des traitements en cours et passés</p>

    <div class="mx-bar">
        <input class="mx-input" type="text" placeholder="Rechercher un médicament...">
        <select class="mx-input"><option>Tous les statuts</option><option>En cours</option><option>Terminé</option><option>Arrêté</option></select>
        <button class="mx-btn primary" data-toggle="#form">+ Nouveau traitement</button>
    </div>

    <form class="mx-form" id="form" hidden>
        <div class="mx-alert">⚠ Vérifiez les allergies du patient avant de valider ce traitement.</div>
        <div class="mx-grid">
            <div><label>Médicament</label>
                <select class="mx-input"><option>Paracétamol 500 mg</option><option>Amoxicilline 1 g</option><option>Metformine 850 mg</option></select>
            </div>
            <div><label>Posologie</label><input class="mx-input" type="text" placeholder="1 comprimé"></div>
            <div><label>Fréquence</label><input class="mx-input" type="text" placeholder="3 fois / jour"></div>
        </div>
        <div class="mx-grid">
            <div><label>Date de début</label><input class="mx-input" type="date"></div>
            <div><label>Date de fin</label><input class="mx-input" type="date"></div>
            <div><label>Statut</label><select class="mx-input"><option>En cours</option><option>Terminé</option><option>Arrêté</option></select></div>
        </div>
        <button type="button" class="mx-btn primary">Enregistrer</button>
        <button type="reset" class="mx-btn">Annuler</button>
    </form>

    <table class="mx-table">
        <thead><tr><th>#</th><th>Médicament</th><th>Posologie</th><th>Fréquence</th><th>Début</th><th>Fin</th><th>Statut</th><th class="end">Actions</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Metformine 850 mg</td><td>1 comprimé</td><td>2 fois / jour</td><td>01/09/2026</td><td>—</td>
                <td><span class="mx-badge green">En cours</span></td>
                <td class="end"><button class="mx-btn small">Détails</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
            <tr><td>2</td><td>Amoxicilline 1 g</td><td>1 gélule</td><td>3 fois / jour</td><td>10/08/2026</td><td>17/08/2026</td>
                <td><span class="mx-badge grey">Terminé</span></td>
                <td class="end"><button class="mx-btn small">Détails</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
            <tr><td>3</td><td>Paracétamol 500 mg</td><td>1 comprimé</td><td>Si douleur</td><td>02/07/2026</td><td>05/07/2026</td>
                <td><span class="mx-badge red">Arrêté</span></td>
                <td class="end"><button class="mx-btn small">Détails</button> <button class="mx-btn small danger">Supprimer</button></td></tr>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php require_once 'includes/scripts.php'; ?>
</body>
</html>