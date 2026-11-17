const MENU = {
  front: [['home','Accueil'],['rendezvous','Rendez-vous'],['consultation','Consultations'],['dossier','Dossier médical']],
  admin: [['dashboard','📊 Dashboard'],['rendezvous','📅 Rendez-vous'],['consultation','🩺 Consultations'],['dossier','📁 Dossiers médicaux']]
};
const active = (page, cur) => page === cur ? 'active' : '';   

const Partials = {
  spinner: () => `<div id="spinner"><i></i></div>`,
  navbar: cur => `<header class="nav"><a class="logo" href="#home">🩺 Medi<b>Care</b></a>
    <nav>${MENU.front.map(([k, l]) => `<a href="#${k}" class="${active(k, cur)}">${l}</a>`).join('')}
    <a class="btn sm" href="admin.html">Back Office</a></nav></header>`,
  sidebar: cur => `<aside class="side"><a class="logo" href="#dashboard">🩺 Medi<b>Care</b></a>
    ${MENU.admin.map(([k, l]) => `<a href="#${k}" class="${active(k, cur)}">${l}</a>`).join('')}
    <a href="index.html">← Voir le site</a></aside>`,
  header: title => `<div class="top"><h3>${title}</h3><span>👩‍⚕️ Dr. Admin</span></div>`,
  footer: () => `<footer class="foot">© 2026 MediCare — Esprit · Web Technologies Project</footer>`
};
