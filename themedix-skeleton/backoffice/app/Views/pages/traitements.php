<h1>Historique des traitements</h1>
<p class="subtitle">Suivi des traitements en cours et passés</p>

<div class="toolbar">
    <input class="input" type="text" placeholder="Rechercher...">
    <select class="input"><option>Tous les statuts</option><option>En cours</option><option>Terminé</option><option>Arrêté</option></select>
    <button class="btn primary" data-toggle="#form">+ Nouveau traitement</button>
</div>

<form class="card" id="form" hidden>
    <div class="alert">⚠ Vérifiez les allergies du patient avant de valider ce traitement.</div>
    <div class="grid">
        <div><label>Patient</label><select class="input"><option>Patient 1</option><option>Patient 2</option></select></div>
        <div><label>Médicament</label><select class="input"><option>Paracétamol 500 mg</option><option>Amoxicilline 1 g</option><option>Metformine 850 mg</option></select></div>
        <div><label>Posologie</label><input class="input" type="text" placeholder="1 comprimé"></div>
        <div><label>Fréquence</label><input class="input" type="text" placeholder="3 fois / jour"></div>
    </div>
    <div class="grid">
        <div><label>Date de début</label><input class="input" type="date"></div>
        <div><label>Date de fin</label><input class="input" type="date"></div>
        <div><label>Statut</label><select class="input"><option>En cours</option><option>Terminé</option><option>Arrêté</option></select></div>
    </div>
    <button type="button" class="btn primary">Enregistrer</button>
    <button type="reset" class="btn">Annuler</button>
</form>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>#</th><th>Patient</th><th>Médicament</th><th>Posologie</th><th>Début</th><th>Fin</th><th>Statut</th><th class="end">Actions</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Patient 1</td><td>Metformine 850 mg</td><td>2 fois / jour</td><td>01/09/2026</td><td>—</td>
                <td><span class="badge green">En cours</span></td>
                <td class="end"><button class="btn small">Détails</button> <button class="btn small danger">Supprimer</button></td></tr>
            <tr><td>2</td><td>Patient 2</td><td>Amoxicilline 1 g</td><td>3 fois / jour</td><td>10/08/2026</td><td>17/08/2026</td>
                <td><span class="badge grey">Terminé</span></td>
                <td class="end"><button class="btn small">Détails</button> <button class="btn small danger">Supprimer</button></td></tr>
            <tr><td>3</td><td>Patient 1</td><td>Paracétamol 500 mg</td><td>Si douleur</td><td>02/07/2026</td><td>05/07/2026</td>
                <td><span class="badge red">Arrêté</span></td>
                <td class="end"><button class="btn small">Détails</button> <button class="btn small danger">Supprimer</button></td></tr>
        </tbody>
    </table>
</div>