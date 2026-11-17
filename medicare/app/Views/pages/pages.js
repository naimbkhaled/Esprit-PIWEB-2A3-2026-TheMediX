const badge = s => `<span class="b ${s === 'Confirmé' ? 'ok' : s === 'Annulé' ? 'no' : ''}">${s}</span>`;
const input = ([n, l, t = 'text', o], v = '') => `<label>${l}${t === 'select'
  ? `<select name="${n}">${o.map(x => `<option ${x == v ? 'selected' : ''}>${x}</option>`).join('')}</select>`
  : `<input name="${n}" type="${t}" value="${v}" required>`}</label>`;

const table = (key, actions) => {
  const s = SCHEMA[key], rows = M[key].all();
  const btn = (a, r, i, c) => `<button class="btn sm ${c}" data-act="${a}" data-e="${key}" data-id="${r.id}">${i}</button>`;
  return `<div class="card tbl"><div class="row"><h3>${s.icon} ${s.label}</h3>
    ${actions ? `<button class="btn" data-act="add" data-e="${key}">+ Ajouter</button>` : ''}</div>
    <div class="scroll"><table><tr>${s.f.map(f => `<th>${f[1]}</th>`).join('')}${actions ? '<th></th>' : ''}</tr>
    ${rows.map(r => `<tr>${s.f.map(f => `<td>${f[0] === 'statut' ? badge(r.statut) : r[f[0]]}</td>`).join('')}
      ${actions ? `<td>${btn('edit', r, '✏️', 'ghost')} ${btn('del', r, '🗑', 'red')}</td>` : ''}</tr>`).join('')
      || '<tr><td colspan="9">Aucune donnée. Cliquez sur « Ajouter ».</td></tr>'}</table></div></div>`;
};

const stats = link => `<div class="grid">${Object.entries(SCHEMA).map(([k, s]) =>
  `<div class="card stat"><div class="ic">${s.icon}</div><h2>${M[k].all().length}</h2><p>${s.label}</p>
   ${link ? `<a class="btn sm" href="#${k}">Gérer</a>` : ''}</div>`).join('')}</div>`;

const dossierCard = d => `<div class="card"><h3>📁 ${d.patient}</h3>
  <p>Âge : <b>${d.age}</b> · Groupe : <b>${d.groupe}</b></p><p>Allergies : ${d.allergies}</p><p>Antécédents : ${d.antecedents}</p></div>`;

const SERVICES = {
  rendezvous:   ['Réservez en 30 secondes avec le médecin de votre choix.', 'Réserver'],
  consultation: ['Retrouvez le détail de vos consultations passées.', 'Voir mes consultations'],
  dossier:      ['Consultez votre dossier médical, où que vous soyez.', 'Ouvrir mon dossier']
};

const V = {
  home: () => `<section class="hero"><div><h1>Votre santé,<br>simplifiée.</h1>
      <p>Prenez rendez-vous, suivez vos consultations et gardez votre dossier médical à portée de clic.</p>
      <a class="btn white" href="#rendezvous">📅 Prendre rendez-vous</a> <a class="btn dark" href="#dossier">📁 Mon dossier</a></div>
      <div class="blob">🩺</div></section>
    <section class="wrap"><h2 class="ttl">Nos services</h2><div class="grid">
      ${Object.entries(SERVICES).map(([k, [d, b]]) => `<div class="card"><div class="ic">${SCHEMA[k].icon}</div>
        <h3>${SCHEMA[k].label}</h3><p>${d}</p><a class="btn" href="#${k}">${b}</a></div>`).join('')}</div>
      <h2 class="ttl" style="margin-top:40px">MediCare en chiffres</h2>${stats(false)}</section>`,

  book: () => `<section class="wrap"><h2 class="ttl">Prendre un rendez-vous</h2>
    <div class="two"><form class="card" data-e="rendezvous"><h3>📅 Nouveau rendez-vous</h3>
      ${SCHEMA.rendezvous.f.filter(f => f[0] !== 'statut').map(f => input(f)).join('')}
      <button class="btn">Réserver</button></form>${table('rendezvous', false)}</div></section>`,

  consultations: () => `<section class="wrap"><div class="row"><h2 class="ttl">Mes consultations</h2><a class="btn" href="#rendezvous">+ Nouveau rendez-vous</a></div>
    <div class="grid">${M.consultation.all().map(c => `<div class="card"><h3>🩺 ${c.motif}</h3>
      <p>${c.patient} · ${c.date}</p><p>Diagnostic : <b>${c.diagnostic}</b></p><span class="b ok">${c.prix} DT</span></div>`).join('')}</div></section>`,

  dossier: () => `<section class="wrap"><h2 class="ttl">Mon dossier médical</h2>
    <div class="card search"><input id="q" placeholder="Tapez votre nom (ex : Amine)"><button class="btn" data-act="search">🔍 Rechercher</button></div>
    <div id="res" class="grid" style="margin-top:22px"></div></section>`,

  dashboard: () => `${stats(true)}<h2 class="ttl" style="margin-top:34px">Prochains rendez-vous</h2>${table('rendezvous', false)}`
};
