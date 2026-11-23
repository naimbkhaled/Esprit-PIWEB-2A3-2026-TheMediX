# HealthConnect - Gestion des Groupes de Soutien (Architecture MVC PHP & MySQL)

Ce projet implémente le module **Groupes de soutien** pour la plateforme **HealthConnect**, architecturé selon le modèle **MVC (Modèle - Vue - Contrôleur)** en PHP orienté objet avec base de données MySQL via PDO.

---

## 1. Architecture du Projet (MVC)

L'architecture est structurée de manière modulaire et découplée :

```text
healthconnect-groupes/
├── config.php                  # Connexion unique PDO (Singleton) & diagnostic BDD
├── database.sql                # Script de création de la BDD et données de test
├── index.php                   # Routeur principal & Front Controller
├── index.html                  # Redirection transparente vers index.php
├── README.md                   # Guide d'installation et documentation
├── assets/                     # Fichiers statiques (CSS & JavaScript)
│   ├── css/
│   │   └── style.css           # Styles globaux (support Thème Clair / Sombre)
│   └── js/
│       ├── theme.js            # Gestionnaire du switch Thème Clair / Sombre
│       └── validation.js       # Contrôles de saisie formulaires côté client
└── app/
    ├── helpers.php             # Fonctions utilitaires (CSRF, pagination, badges...)
    ├── Models/                 # MODÈLES (Classes entités avec getters & setters)
    │   ├── Groupe.php          # Entité Groupe (id, nom, description, règles...)
    │   ├── User.php            # Entité Utilisateur (id, nom, rôle...)
    │   └── Invitation.php      # Entité Invitation (id, id_groupe, statut...)
    ├── Controllers/            # CONTRÔLEURS (Logique métier et requêtes SQL)
    │   ├── Controller.php      # Contrôleur parent (render, PDO, CSRF, export CSV)
    │   ├── GroupeController.php# Contrôleur patient (espace front-office)
    │   └── AdminController.php # Contrôleur administrateur (espace back-office)
    └── Views/                  # VUES (Templates HTML / PHP)
        ├── layouts/            # Gabarits de page
        │   ├── front.php       # Layout Front-Office (Navbar, Flash, Footer)
        │   ├── back.php        # Layout Back-Office (Sidebar, Header, Modal)
        │   └── plain.php       # Layout minimal (Connexion, Choix d'espace)
        ├── partials/           # Éléments réutilisables
        │   ├── head.php        # Balises <head>, Bootstrap, CSS
        │   ├── header.php      # Barre supérieure administration
        │   ├── navbar.php      # Barre de navigation patient
        │   ├── sidebar.php     # Menu latéral d'administration
        │   ├── flash.php       # Messages flash de succès/erreur
        │   ├── modal-rejet.php # Fenêtre modale de saisie du motif de rejet
        │   ├── footer.php      # Pied de page
        │   └── scripts.php     # Scripts JS Bootstrap et validation
        ├── front/              # Vues patient
        │   ├── home.php        # Écran de sélection d'espace
        │   ├── groupes.php     # Liste, filtres, recherche & pagination
        │   ├── groupe-form.php # Formulaire de création / modification
        │   └── groupe-detail.php# Détails du groupe, membres & invitations
        └── back/               # Vues administrateur
            ├── admin-login.php # Page d'authentification avec hash BDD
            ├── admin-groupes.php# Table d'administration (Approuver / Rejeter)
            └── admin-groupe-detail.php # Vue détaillée groupe pour modération
```

---

## 2. Le fichier `config.php` (Connexion à la Base de Données)

Le fichier `config.php` applique le design pattern **Singleton** avec la classe `Config` et la méthode statique `Config::getConnexion()` :
- Empêche les connexions multiples redondantes vers MySQL.
- Fournit également la fonction globale `getConnexion()` pour une utilisation directe dans les contrôleurs.
- Définit les constantes de configuration : hôte (`DB_HOST`), nom de base (`DB_NAME`), identifiants (`DB_USER`, `DB_PASS`), et taille de pagination (`PER_PAGE`).
- **Test direct intégré** : Si vous ouvrez `config.php` directement dans votre navigateur (`http://localhost/healthconnect-groupes/config.php`), il exécute un test de connexion complet et affiche en direct les tables et le nombre d'enregistrements récupérés depuis MySQL.

---

## 3. Guide d'Installation et d'Exécution avec XAMPP

1. **Démarrer les services XAMPP** :
   - Ouvrez le **XAMPP Control Panel**.
   - Démarrez **Apache** et **MySQL**.

2. **Importer la base de données** :
   - Rendez-vous sur [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
   - Cliquez sur l'onglet **Importer**.
   - Sélectionnez le fichier `database.sql` présent à la racine du projet et cliquez sur **Exécuter**.
   - La base de données `healthconnect` est créée avec toutes les tables, contraintes et données initiales.

3. **Placer le projet dans le serveur web** :
   - Copiez le dossier du projet dans le répertoire `htdocs` de XAMPP, par exemple : `C:\xampp\htdocs\healthconnect-groupes`.

4. **Tester la connexion à la base de données** :
   - Accédez à : [http://localhost/healthconnect-groupes/config.php](http://localhost/healthconnect-groupes/config.php)
   - Vous devriez voir un message vert confirmant la connexion et la liste des tables détectées.

5. **Lancer l'application** :
   - Accédez à l'URL principale : [http://localhost/healthconnect-groupes/index.php](http://localhost/healthconnect-groupes/index.php) (ou directement [http://localhost/healthconnect-groupes/](http://localhost/healthconnect-groupes/)).

---

## 4. Comptes de Démonstration (Test)

Le mot de passe pour tous les comptes de test est : **`admin123`**

| Identifiant | Rôle | Email | Espace |
| :--- | :--- | :--- | :--- |
| **`admin`** | Administrateur | `admin@healthconnect.tn` | Back-Office (`?page=admin-login`) |
| **`patient1`** | Patient (Démo) | `patient@healthconnect.tn` | Front-Office (`?page=groupes`) |
| **`sarra`** | Patient | `sarra@healthconnect.tn` | Front-Office |
| **`karim`** | Patient | `karim@healthconnect.tn` | Front-Office |

---

## 5. Fonctionnalités Implémentées

- **Front-Office (Patients)** :
  - Consultation des groupes approuvés et de ses propres groupes (onglets "Tous les groupes" et "Mes groupes").
  - Recherche par mot-clé et filtrage par statut (`approuve`, `en_attente`, `rejete`).
  - Pagination dynamique.
  - Création d'un groupe avec passage automatique au statut `en_attente`.
  - Modification et suppression de ses propres groupes (si un groupe rejeté est modifié, son statut repasse en attente d'approbation).
  - Adhésion à un groupe approuvé en un clic.
  - Consultation détaillée d'un groupe (règles, créateur, nombre et liste des membres).
  - Invitation d'autres membres par email ou identifiant.
  - Export de la liste des groupes filtrée au format CSV (compatible Excel).
- **Back-Office (Administrateurs)** :
  - Authentification sécurisée par session et vérification de mot de passe haché (`password_verify`).
  - Tableau de bord de modération des groupes avec pagination et recherche.
  - Approbation directe d'un groupe en attente.
  - Rejet d'un groupe avec saisie obligatoire d'un motif explicatif (fenêtre modale).
  - Consultation des détails et de la liste des membres.
  - Export CSV des groupes.
  - Déconnexion sécurisée.
- **Sécurité et Bonnes Pratiques** :
  - Protection contre les injections SQL (requêtes préparées PDO systématiques).
  - Protection contre les failles XSS (échappement automatique avec `htmlspecialchars`).
  - Protection CSRF (jeton de session vérifié sur toutes les requêtes POST).
  - Double validation : validation JavaScript interactive côté client + validation stricte côté serveur dans les contrôleurs PHP.
  - Switch thème Sombre / Clair persistant via `localStorage`.
