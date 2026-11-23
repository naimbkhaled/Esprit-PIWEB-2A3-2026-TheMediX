const $ = s => document.querySelector(s);
const toast = m => { const t = document.createElement('div'); t.className = 'toast'; t.textContent = m; document.body.append(t); setTimeout(() => t.remove(), 2200); };

function modal(key, id) {
  const s = SCHEMA[key], v = id ? M[key].find(id) : {};
  document.body.insertAdjacentHTML('beforeend', `<div class="modal"><form class="card" data-e="${key}" data-id="${id || ''}">
    <h3>${id ? 'Modifier' : 'Ajouter'} · ${s.label}</h3>${s.f.map(f => input(f, v[f[0]])).join('')}
    <div class="row"><button type="button" class="btn ghost" data-act="close">Annuler</button><button class="btn">Enregistrer</button></div></form></div>`);
}

document.addEventListener('click', ev => {
  const b = ev.target.closest('[data-act]'); if (!b) return;
  const { act, e: k, id } = b.dataset;
  if (act === 'add') modal(k);
  if (act === 'edit') modal(k, id);
  if (act === 'close') $('.modal').remove();
  if (act === 'del' && confirm('Supprimer cet élément ?')) { M[k].remove(id); toast('Supprimé 🗑'); route(); }
  if (act === 'search') {
    const q = $('#q').value.toLowerCase();
    $('#res').innerHTML = M.dossier.all().filter(d => d.patient.toLowerCase().includes(q)).map(dossierCard).join('')
      || '<p>Aucun dossier trouvé. Vérifiez l’orthographe de votre nom.</p>';
  }
});

document.addEventListener('submit', ev => {
  ev.preventDefault();
  const f = ev.target, k = f.dataset.e, o = Object.fromEntries(new FormData(f));
  if (k === 'rendezvous') o.statut = o.statut || 'En attente';
  f.dataset.id ? M[k].update(f.dataset.id, o) : M[k].add(o);
  toast('Enregistré ✅'); route();
});

window.addEventListener('hashchange', route);
document.body.insertAdjacentHTML('afterbegin', Partials.spinner());
route();
setTimeout(() => { const s = $('#spinner'); s.style.opacity = 0; setTimeout(() => s.remove(), 400); }, 500);
