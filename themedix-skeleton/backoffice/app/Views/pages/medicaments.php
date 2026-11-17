<h1>Médicaments</h1>
<p class="subtitle">Gestion du catalogue des médicaments</p>

<div class="toolbar">
    <input class="input" type="text" placeholder="Rechercher un médicament...">
    <button class="btn primary" data-toggle="#form">+ Ajouter un médicament</button>
</div>

<form class="card" id="form" hidden>
    <div class="grid">
        <div><label>Nom</label><input class="input" type="text" placeholder="Ex : Paracétamol"></div>
        <div><label>Dosage</label><input class="input" type="text" placeholder="Ex : 500 mg"></div>
        <div><label>Forme</label>
            <select class="input"><option>Comprimé</option><option>Gélule</option><option>Sirop</option><option>Injection</option></select>
        </div>
    </div>
    <div class="grid"><div><label>Description</label><textarea class="input" rows="3"></textarea></div></div>
    <button type="button" class="btn primary">Enregistrer</button>
    <button type="reset" class="btn">Annuler</button>
</form>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>#</th><th>Nom</th><th>Dosage</th><th>Forme</th><th>Description</th><th class="end">Actions</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Paracétamol</td><td>500 mg</td><td>Comprimé</td><td>Antalgique</td>
                <td class="end"><button class="btn small">Modifier</button> <button class="btn small danger">Supprimer</button></td></tr>
            <tr><td>2</td><td>Amoxicilline</td><td>1 g</td><td>Gélule</td><td>Antibiotique</td>
                <td class="end"><button class="btn small">Modifier</button> <button class="btn small danger">Supprimer</button></td></tr>
            <tr><td>3</td><td>Metformine</td><td>850 mg</td><td>Comprimé</td><td>Diabète type 2</td>
                <td class="end"><button class="btn small">Modifier</button> <button class="btn small danger">Supprimer</button></td></tr>
        </tbody>
    </table>
</div>