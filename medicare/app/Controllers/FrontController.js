class FrontController extends Controller {
  constructor() { super('front'); }
  home()         { this.render('Accueil', 'home', V.home()); }
  rendezvous()   { this.render('Rendez-vous', 'rendezvous', V.book()); }
  consultation() { this.render('Consultations', 'consultation', V.consultations()); }
  dossier()      { this.render('Dossier médical', 'dossier', V.dossier()); }
}
