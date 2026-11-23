<?php
// Description de chaque historique gérable par l'utilisateur.
// Ajouter un historique = ajouter une entrée ici + une table SQL. Aucun autre code à écrire.
//
// Clés : groupe, titre, table, date (colonne de date), perm (add|edit|delete), champs, note,
//        effacer_tout, recherche, cle, from/select (jointures), valider/badge (fonctions),
//        special + url (pages dédiées), where, unite (libellé du compteur du hub)

$full  = ['add', 'edit', 'delete'];
$champ = fn(string $nom, string $label, string $type, array $x = []) => ['nom' => $nom, 'label' => $label, 'type' => $type] + $x;
$dt    = fn(string $nom = 'ts', string $label = 'Date et heure', array $x = []) => $champ($nom, $label, 'datetime', ['req' => 1, 'defaut' => 'now'] + $x);
$sources = ['relais mobile' => 'relais mobile', 'API fabricant' => 'API fabricant', 'saisie manuelle' => 'saisie manuelle'];

return [

  // ---------------- Santé & suivi ----------------
  'glycemie' => ['groupe' => 'Santé & suivi', 'titre' => 'Mesures de glycémie', 'table' => 'mesures', 'date' => 'ts', 'perm' => $full,
    'note' => "Valeurs saisies par vous ou reçues du capteur ; ne remplace pas un diagnostic ou un avis médical.",
    'champs' => [
      $dt(),
      $champ('valeur', 'Valeur', 'number', ['req' => 1, 'min' => 1, 'max' => 600, 'step' => '0.1']),
      $champ('unite', 'Unité', 'select', ['req' => 1, 'options' => ['mg/dL' => 'mg/dL', 'mmol/L' => 'mmol/L'], 'defaut' => 'mg/dL']),
      $champ('source', 'Source', 'select', ['req' => 1, 'options' => $sources, 'defaut' => 'saisie manuelle']),
    ],
    'valider' => function (array $v): ?string {
        [$min, $max] = $v['unite'] === 'mmol/L' ? [1, 35] : [20, 600];
        return ($v['valeur'] < $min || $v['valeur'] > $max) ? "Valeur hors limites pour l'unité choisie ($min – $max)." : null;
    },
    'badge_titre' => 'Statut',
    // Seuils de démonstration : à faire valider par un médecin
    'badge' => function (array $r): array {
        $mg = $r['unite'] === 'mmol/L' ? $r['valeur'] * 18 : (float)$r['valeur'];
        return $mg < 70 ? ['lo', 'bas'] : ($mg > 180 ? ['hi', 'élevé'] : ['ok', 'normal']);
    },
  ],
  'a1c' => ['groupe' => 'Santé & suivi', 'titre' => 'HbA1c', 'table' => 'a1c', 'date' => 'date_mesure', 'perm' => $full,
    'champs' => [
      $champ('date_mesure', 'Date', 'date', ['req' => 1, 'defaut' => 'now']),
      $champ('valeur_pct', 'HbA1c (%)', 'number', ['req' => 1, 'min' => 3, 'max' => 20, 'step' => '0.1']),
    ]],
  'medicaments' => ['groupe' => 'Santé & suivi', 'titre' => 'Prises de médicaments', 'table' => 'prises_medicament', 'date' => 'ts', 'perm' => $full,
    'note' => 'Journal des prises confirmées ou manquées. Votre traitement reste défini par votre médecin.',
    'champs' => [
      $dt(),
      $champ('medicament', 'Médicament', 'text', ['req' => 1, 'max_len' => 120]),
      $champ('statut', 'Statut', 'select', ['req' => 1, 'options' => ['confirmee' => 'Confirmée', 'manquee' => 'Manquée'], 'defaut' => 'confirmee']),
    ]],

  // ---------------- Journal diabète ----------------
  'repas' => ['groupe' => 'Journal diabète', 'titre' => 'Journal des repas', 'table' => 'repas', 'date' => 'ts', 'perm' => $full,
    'champs' => [
      $dt(),
      $champ('type', 'Repas', 'select', ['req' => 1, 'options' => ['petit-dejeuner' => 'Petit-déjeuner', 'dejeuner' => 'Déjeuner', 'diner' => 'Dîner', 'collation' => 'Collation']]),
      $champ('aliments', 'Aliments', 'text', ['req' => 1]),
      $champ('glucides_g', 'Glucides (g)', 'number', ['min' => 0, 'max' => 500, 'step' => '0.1']),
    ]],
  'insuline' => ['groupe' => 'Journal diabète', 'titre' => "Journal de l'insuline", 'table' => 'insuline', 'date' => 'ts', 'perm' => $full,
    'note' => "Outil d'enregistrement : les doses d'insuline restent définies selon la prescription médicale.",
    'champs' => [
      $dt(),
      $champ('dose_ui', 'Dose (UI)', 'number', ['req' => 1, 'min' => 0.5, 'max' => 100, 'step' => '0.5']),
      $champ('type', "Type d'insuline", 'select', ['req' => 1, 'options' => ['rapide' => 'Rapide', 'lente' => 'Lente']]),
      $champ('repas_id', 'Repas lié (facultatif)', 'select', ['options_sql' =>
        "SELECT id, CONCAT(DATE_FORMAT(ts, '%d/%m %H:%i'), ' – ', aliments) FROM repas WHERE utilisateur_id = ? ORDER BY ts DESC LIMIT 50"]),
    ]],
  'activites' => ['groupe' => 'Journal diabète', 'titre' => 'Activité physique', 'table' => 'activites', 'date' => 'ts', 'perm' => $full,
    'champs' => [
      $dt(),
      $champ('type', "Type d'activité", 'select', ['req' => 1, 'options' => ['marche' => 'Marche', 'course' => 'Course', 'velo' => 'Vélo', 'natation' => 'Natation', 'sport_collectif' => 'Sport collectif', 'autre' => 'Autre']]),
      $champ('duree_min', 'Durée (min)', 'number', ['req' => 1, 'min' => 1, 'max' => 600, 'step' => '1']),
    ]],

  // ---------------- Rendez-vous & consultations ----------------
  'rendez_vous' => ['groupe' => 'Rendez-vous & consultations', 'titre' => 'Rendez-vous médicaux', 'table' => 'rendez_vous', 'date' => 'debut', 'perm' => $full,
    'champs' => [
      $champ('debut', 'Date et heure', 'datetime', ['req' => 1, 'futur_ok' => 1, 'defaut' => 'now']),
      $champ('medecin_nom', 'Médecin', 'text', ['req' => 1, 'max_len' => 120]),
      $champ('motif', 'Motif', 'text', ['max_len' => 200]),
      $champ('lieu', 'Lieu', 'text', ['max_len' => 200]),
      $champ('statut', 'Statut', 'select', ['req' => 1, 'options' => ['a_venir' => 'À venir', 'honore' => 'Honoré', 'annule' => 'Annulé'], 'defaut' => 'a_venir']),
    ]],
  'teleconsultations' => ['groupe' => 'Rendez-vous & consultations', 'titre' => 'Téléconsultations', 'table' => 'teleconsultations', 'date' => 'debut', 'perm' => [],
    'champs' => [
      $champ('debut', 'Début', 'datetime'), $champ('medecin_nom', 'Médecin', 'text'), $champ('duree_min', 'Durée (min)', 'number'),
      $champ('statut', 'Statut', 'select', ['options' => ['a_venir' => 'À venir', 'terminee' => 'Terminée', 'annulee' => 'Annulée']]),
    ]],
  'messages' => ['groupe' => 'Rendez-vous & consultations', 'titre' => 'Messagerie avec mon médecin', 'table' => 'messages', 'date' => 'ts', 'perm' => [],
    'note' => "L'historique reste conservé dans la conversation.",
    'champs' => [
      $champ('ts', 'Date', 'datetime'), $champ('medecin_nom', 'Médecin', 'text'),
      $champ('auteur', 'De', 'select', ['options' => ['patient' => 'Moi', 'medecin' => 'Médecin']]), $champ('contenu', 'Message', 'textarea'),
    ]],

  // ---------------- Alertes & sécurité ----------------
  'alertes' => ['groupe' => 'Alertes & sécurité', 'titre' => 'Alertes de glycémie', 'table' => 'alertes_glycemie', 'date' => 'ts', 'perm' => [],
    'note' => 'Historique généré automatiquement : consultable, non modifiable (traçabilité).',
    'champs' => [
      $champ('ts', 'Date', 'datetime'),
      $champ('type', 'Type', 'select', ['options' => ['hypo' => 'Hypoglycémie', 'hyper' => 'Hyperglycémie', 'capteur' => 'Capteur sans données']]),
      $champ('valeur', 'Valeur', 'number'), $champ('seuil', 'Seuil', 'number'),
      $champ('statut', 'Statut', 'select', ['options' => ['envoyee' => 'Envoyée', 'lue' => 'Lue']]),
    ]],
  'sos' => ['groupe' => 'Alertes & sécurité', 'titre' => 'Historique SOS', 'table' => 'sos_evenements', 'date' => 'ts', 'perm' => [],
    'note' => "Historique de chaque étape, consultable (non modifiable) : il peut servir au médecin et aux secours.",
    'champs' => [
      $champ('ts', 'Date', 'datetime'),
      $champ('declencheur', 'Déclencheur', 'select', ['options' => ['manuel' => 'Bouton SOS', 'non_confirmation' => 'Rappel non confirmé', 'capteur' => 'Capteur sans données']]),
      $champ('etape', 'Étape', 'text'), $champ('contact_prevenu', 'Proche prévenu', 'text'),
    ]],
  'contacts' => ['groupe' => 'Alertes & sécurité', 'titre' => "Contacts d'urgence", 'table' => 'contacts_urgence', 'date' => null, 'perm' => $full,
    'note' => "Votre consentement est explicite et l'accès est retirable : décochez « Alertes actives » ou supprimez le contact.",
    'champs' => [
      $champ('nom', 'Nom', 'text', ['req' => 1, 'max_len' => 120]),
      $champ('telephone', 'Téléphone', 'text', ['req' => 1, 'max_len' => 20, 'regex' => '/^\+?[0-9 ]{6,20}$/']),
      $champ('relation', 'Relation', 'select', ['req' => 1, 'options' => ['famille' => 'Famille', 'conjoint' => 'Conjoint(e)', 'ami' => 'Ami(e)', 'autre' => 'Autre']]),
      $champ('consentement', 'Consentement explicite donné', 'bool', ['req' => 1]),
      $champ('actif', "Alertes actives (décochez pour retirer l'accès)", 'bool', ['defaut' => true]),
    ]],
  'partage' => ['groupe' => 'Alertes & sécurité', 'titre' => 'Partage avec mon médecin', 'table' => 'partages', 'date' => 'maj_le',
    'special' => 'partage', 'url' => '/partage', 'perm' => ['edit'], 'where' => 'autorise = 1', 'unite' => ['catégorie partagée', 'catégories partagées']],

  // ---------------- Communauté ----------------
  'publications' => ['groupe' => 'Communauté', 'titre' => 'Mes publications', 'table' => 'publications', 'date' => 'cree_le', 'perm' => $full,
    'champs' => [
      $champ('titre', 'Titre', 'text', ['req' => 1, 'max_len' => 200]),
      $champ('categorie', 'Catégorie', 'select', ['req' => 1, 'options' => ['Nutrition' => 'Nutrition', 'Activité' => 'Activité', 'Diabète' => 'Diabète', 'Soutien' => 'Soutien', 'Autre' => 'Autre']]),
      $champ('contenu', 'Contenu', 'textarea', ['req' => 1]),
      $champ('cree_le', 'Publié le', 'datetime', ['form' => false]),
    ]],
  'commentaires' => ['groupe' => 'Communauté', 'titre' => 'Mes commentaires', 'table' => 'commentaires', 'date' => 'cree_le', 'perm' => ['edit', 'delete'],
    'champs' => [
      $champ('publication_id', 'Publication', 'select', ['form' => false, 'options_sql' => 'SELECT id, titre FROM publications']),
      $champ('contenu', 'Commentaire', 'textarea', ['req' => 1]),
      $champ('cree_le', 'Publié le', 'datetime', ['form' => false]),
    ]],
  'groupes' => ['groupe' => 'Communauté', 'titre' => 'Mes groupes de soutien', 'table' => 'groupes_membres', 'date' => 'rejoint_le',
    'special' => 'groupes', 'url' => '/groupes', 'perm' => ['add', 'delete'], 'unite' => ['groupe rejoint', 'groupes rejoints']],

  // ---------------- Contenus ----------------
  'lus' => ['groupe' => 'Contenus', 'titre' => 'Articles lus', 'table' => 'lectures', 'cle' => 'article_id', 'date' => 'lu_le', 'perm' => ['delete'], 'effacer_tout' => true, 'recherche' => false,
    'from'   => "`lectures` t JOIN `articles` a ON a.id = t.article_id AND a.statut = 'pub'",
    'select' => 't.article_id AS id, a.titre, a.categorie, t.lu_le',
    'champs' => [$champ('titre', 'Article', 'text'), $champ('categorie', 'Catégorie', 'text'), $champ('lu_le', 'Lu le', 'datetime')]],
  'favoris' => ['groupe' => 'Contenus', 'titre' => 'Articles favoris', 'table' => 'favoris', 'cle' => 'article_id', 'date' => 'ajoute_le', 'perm' => ['delete'], 'recherche' => false,
    'from'   => "`favoris` t JOIN `articles` a ON a.id = t.article_id AND a.statut = 'pub'",
    'select' => 't.article_id AS id, a.titre, a.categorie, t.ajoute_le',
    'champs' => [$champ('titre', 'Article', 'text'), $champ('categorie', 'Catégorie', 'text'), $champ('ajoute_le', 'Ajouté le', 'datetime')]],

  // ---------------- IA & compte ----------------
  'chatbot' => ['groupe' => 'IA & compte', 'titre' => 'Conversations avec le chatbot', 'table' => 'chatbot_messages', 'date' => 'ts', 'perm' => ['delete'], 'effacer_tout' => true,
    'note' => 'Information uniquement, jamais de diagnostic.',
    'champs' => [
      $champ('ts', 'Date', 'datetime'),
      $champ('role', 'De', 'select', ['options' => ['user' => 'Moi', 'assistant' => 'Assistant']]),
      $champ('contenu', 'Message', 'textarea'),
    ]],
  'connexions' => ['groupe' => 'IA & compte', 'titre' => 'Historique de connexion', 'table' => 'connexions', 'date' => 'ts', 'perm' => [],
    'note' => 'Utile pour repérer une connexion inhabituelle.',
    'champs' => [$champ('ts', 'Date', 'datetime'), $champ('ip', 'Adresse IP', 'text'), $champ('appareil', 'Appareil', 'text')]],
];
