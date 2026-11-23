# Gestion des utilisateurs (MVC PHP)

Le formulaire d'inscription de ce dossier est intégré à l'application Medix principale.
Il est accessible depuis `http://localhost/myapp/index.php?action=register` et enregistre les
identifiants dans la base `session` pour qu'ils fonctionnent sur l'écran de connexion principal.

Si la table `session.users` existe déjà, importez `myapp1122/upgrade.sql` afin d'ajouter le champ
`is_active` et la table `user_profiles` requise pour les informations Client/Médecin.
