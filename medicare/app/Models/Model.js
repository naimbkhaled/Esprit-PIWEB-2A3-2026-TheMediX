
class Model {
  constructor(name, seed = []) {
    this.key = 'medicare_' + name;
    if (!localStorage.getItem(this.key)) this.save(seed);
  }
  all()        { return JSON.parse(localStorage.getItem(this.key) || '[]'); }
  save(list)   { localStorage.setItem(this.key, JSON.stringify(list)); }
  find(id)     { return this.all().find(x => x.id == id); }
  add(o)       { this.save([...this.all(), { ...o, id: Date.now() }]); }
  update(id,o) { this.save(this.all().map(x => x.id == id ? { ...x, ...o } : x)); }
  remove(id)   { this.save(this.all().filter(x => x.id != id)); }
}

// The 3 entities: [fieldName, label, inputType, options]
const SCHEMA = {
  rendezvous:   { label: 'Rendez-vous',        icon: '📅', f: [['patient','Patient'],['date','Date','date'],['heure','Heure','time'],['medecin','Médecin'],['statut','Statut','select',['En attente','Confirmé','Annulé']]] },
  consultation: { label: 'Consultations',      icon: '🩺', f: [['patient','Patient'],['date','Date','date'],['motif','Motif'],['diagnostic','Diagnostic'],['prix','Prix (DT)','number']] },
  dossier:      { label: 'Dossiers médicaux',  icon: '📁', f: [['patient','Patient'],['age','Âge','number'],['groupe','Groupe sanguin','select',['A+','A-','B+','B-','AB+','AB-','O+','O-']],['allergies','Allergies'],['antecedents','Antécédents']] }
};

const SEED = {
  rendezvous: [
    { id: 1, patient: 'Amine Ben Salah', date: '2026-11-20', heure: '09:30', medecin: 'Dr. Trabelsi', statut: 'Confirmé' },
    { id: 2, patient: 'Sana Gharbi',     date: '2026-11-21', heure: '14:00', medecin: 'Dr. Hamdi',    statut: 'En attente' }],
  consultation: [
    { id: 1, patient: 'Amine Ben Salah', date: '2026-11-10', motif: 'Fièvre persistante', diagnostic: 'Grippe saisonnière', prix: 60 },
    { id: 2, patient: 'Sana Gharbi',     date: '2026-11-12', motif: 'Contrôle annuel',    diagnostic: 'RAS',                prix: 50 }],
  dossier: [
    { id: 1, patient: 'Amine Ben Salah', age: 29, groupe: 'O+', allergies: 'Pénicilline', antecedents: 'Asthme léger' },
    { id: 2, patient: 'Sana Gharbi',     age: 34, groupe: 'A-', allergies: 'Aucune',      antecedents: 'Hypertension' }]
};

const M = {};
Object.keys(SCHEMA).forEach(k => M[k] = new Model(k, SEED[k]));
