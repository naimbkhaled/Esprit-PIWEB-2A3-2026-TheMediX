CREATE DATABASE IF NOT EXISTS healthconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE healthconnect;
CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE, identifiant VARCHAR(30) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL, role ENUM('patient','admin') NOT NULL DEFAULT 'patient'
) ENGINE=InnoDB;
CREATE TABLE groupe (
  id INT AUTO_INCREMENT PRIMARY KEY, nom VARCHAR(50) NOT NULL, description VARCHAR(300) NOT NULL, regles VARCHAR(500) NOT NULL,
  statut ENUM('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente', motif_rejet VARCHAR(200) NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, id_createur INT NOT NULL,
  FOREIGN KEY (id_createur) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE membre_groupe (
  id_user INT NOT NULL, id_groupe INT NOT NULL, date_adhesion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_user, id_groupe),
  FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (id_groupe) REFERENCES groupe(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE invitation (
  id INT AUTO_INCREMENT PRIMARY KEY, id_groupe INT NOT NULL, id_invite INT NOT NULL, id_inviteur INT NOT NULL,
  statut ENUM('en_attente','accepte','refuse') NOT NULL DEFAULT 'en_attente', date_invitation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_inv (id_groupe, id_invite),
  FOREIGN KEY (id_groupe) REFERENCES groupe(id) ON DELETE CASCADE,
  FOREIGN KEY (id_invite) REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (id_inviteur) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- Données de démonstration (mot de passe de tous les comptes : admin123)
INSERT INTO user (nom,prenom,email,identifiant,mot_de_passe,role) VALUES
('Admin','HealthConnect','admin@healthconnect.tn','admin','$2b$10$4S1e4JNRsuIJCz1msPXTauwefXsBF13tqHZPkwNjUG/0I7P6Zag0O','admin'),
('Démo','Patient','patient@healthconnect.tn','patient1','$2b$10$4S1e4JNRsuIJCz1msPXTauwefXsBF13tqHZPkwNjUG/0I7P6Zag0O','patient'),
('Ben Ali','Sarra','sarra@healthconnect.tn','sarra','$2b$10$4S1e4JNRsuIJCz1msPXTauwefXsBF13tqHZPkwNjUG/0I7P6Zag0O','patient'),
('Mansour','Karim','karim@healthconnect.tn','karim','$2b$10$4S1e4JNRsuIJCz1msPXTauwefXsBF13tqHZPkwNjUG/0I7P6Zag0O','patient');
INSERT INTO groupe (nom,description,regles,statut,motif_rejet,id_createur) VALUES
('Vivre avec le diabète','Échanges entre patients sur le quotidien et les astuces.','Respecter les autres. Ne pas donner de conseil médical.','approuve',NULL,3),
('Cuisine saine','Recettes et idées de repas équilibrés.','Partager des recettes. Rester bienveillant.','approuve',NULL,2),
('Sport et santé','Se motiver et partager ses séances.','Respect et bienveillance entre membres.','en_attente',NULL,2),
('Soutien après diagnostic','Accompagner les nouveaux patients.','Confidentialité des échanges entre membres.','rejete','Description trop vague, merci de préciser.',2),
('Gestion du stress','Techniques de relaxation partagées.','Pas de publicité. Pas de conseil médical.','approuve',NULL,4);
INSERT INTO membre_groupe (id_user,id_groupe) VALUES (3,1),(2,2),(2,3),(2,4),(4,5),(4,1);
