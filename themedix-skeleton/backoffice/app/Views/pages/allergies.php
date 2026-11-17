<h1>Allergies et intolérances</h1>
<p class="subtitle">Gestion des allergies et intolérances des patients</p>

<div class="toolbar">
    <input class="input" type="text" placeholder="Rechercher une substance...">
    <select class="input"><option>Tous les types</option><option>Allergie</option><option>Intolérance</option></select>
    <button class="btn primary" data-toggle="#form">+ Ajouter une allergie</button>
</div>

<form class="card" id="form" hidden>
    <div class="grid">
        <div><label>Patient</label><select class="input"><option>Patient 1</option><option>Patient 2</option></select></div>
        <div><label>Substance</label><input class="input" type="text" placeholder="Pénicilline, lactose..."></div>
        <div><label>Type</label><select class="input"><option>Allergie</option><option>Intolérance</option></select></div>
        <div><label>Sévérité</label><select class="input"><option>Légère</option><option>Modérée</option><option>Sévère</option></select></div>
    </div>
    <div class="grid"><div><label>Réaction</label><textarea class="input" rows="3" placeholder="Éruption cutanée..."></textarea></div></div>
    <button type="button" class="btn primary">Enregistrer</button>
    <button type="reset" class="btn">Annuler</button>
</form>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>#</th><th>Patient</th><th>Substance</th><th>Type</th><th>Sévérité</th><th>Réaction</th><th class="end">Actions</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Patient 1</td><td>Pénicilline</td><td><span class="badge red">Allergie</span></td><td><span class="badge red">Sévère</span></td><td>Difficultés respiratoires</td>
                <td class="end"><button class="btn small">Modifier</button> <button class="btn small danger">Supprimer</button></td></tr>
            <tr><td>2</td><td>Patient 2</td><td>Lactose</td><td><span class="badge blue">Intolérance</span></td><td><span class="badge yellow">Modérée</span></td><td>Troubles digestifs</td>
                <td class="end"><button class="btn small">Modifier</button> <button class="btn small danger">Supprimer</button></td></tr>
            <tr><td>3</td><td>Patient 1</td><td>Aspirine</td><td><span class="badge red">Allergie</span></td><td><span class="badge green">Légère</span></td><td>Éruption cutanée</td>
                <td class="end"><button class="btn small">Modifier</button> <button class="btn small danger">Supprimer</button></td></tr>
        </tbody>
    </table>
</div>