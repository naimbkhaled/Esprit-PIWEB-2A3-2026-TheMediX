
const APP = document.body.dataset.app;
const CTRL = APP === 'front' ? new FrontController() : new AdminController();
const ROUTES = { front: ['home','rendezvous','consultation','dossier'], admin: ['dashboard','rendezvous','consultation','dossier'] };

function route() {
  const p = location.hash.slice(1), k = ROUTES[APP].includes(p) ? p : ROUTES[APP][0];
  APP === 'admin' && SCHEMA[k] ? CTRL.list(k) : CTRL[k]();
}
