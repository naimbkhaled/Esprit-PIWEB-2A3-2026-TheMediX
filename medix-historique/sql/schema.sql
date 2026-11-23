-- Schéma du module « Gestion de l'historique » (MySQL 8), à importer dans la base Medix existante.
-- La table users est fournie par l'application principale; les historiques y font référence.
USE session;

CREATE TABLE IF NOT EXISTS articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  categorie VARCHAR(60) NOT NULL,
  date_pub DATE NOT NULL,
  source VARCHAR(150) NOT NULL,
  statut ENUM('pub','draft') NOT NULL DEFAULT 'pub'
);

CREATE TABLE IF NOT EXISTS mesures (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  valeur DECIMAL(6,1) NOT NULL,
  unite ENUM('mg/dL','mmol/L') NOT NULL DEFAULT 'mg/dL',
  ts DATETIME NOT NULL,
  source VARCHAR(60) NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lectures (
  utilisateur_id INT NOT NULL, article_id INT NOT NULL, lu_le DATETIME NOT NULL,
  PRIMARY KEY (utilisateur_id, article_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS favoris (
  utilisateur_id INT NOT NULL, article_id INT NOT NULL, ajoute_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (utilisateur_id, article_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS a1c (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  date_mesure DATE NOT NULL,
  valeur_pct DECIMAL(3,1) NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS prises_medicament (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  medicament VARCHAR(120) NOT NULL,
  statut ENUM('confirmee','manquee') NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS repas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  type ENUM('petit-dejeuner','dejeuner','diner','collation') NOT NULL,
  aliments VARCHAR(255) NOT NULL,
  glucides_g DECIMAL(5,1) NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS insuline (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  dose_ui DECIMAL(4,1) NOT NULL,
  type ENUM('rapide','lente') NOT NULL,
  repas_id INT NULL,
  FOREIGN KEY (repas_id) REFERENCES repas(id) ON DELETE SET NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS activites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  type ENUM('marche','course','velo','natation','sport_collectif','autre') NOT NULL,
  duree_min SMALLINT NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS rendez_vous (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  debut DATETIME NOT NULL,
  medecin_nom VARCHAR(120) NOT NULL,
  motif VARCHAR(200) NULL,
  lieu VARCHAR(200) NULL,
  statut ENUM('a_venir','honore','annule') NOT NULL DEFAULT 'a_venir',
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS teleconsultations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  debut DATETIME NOT NULL,
  medecin_nom VARCHAR(120) NOT NULL,
  duree_min SMALLINT NULL,
  statut ENUM('a_venir','terminee','annulee') NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  medecin_nom VARCHAR(120) NOT NULL,
  auteur ENUM('patient','medecin') NOT NULL,
  contenu TEXT NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS alertes_glycemie (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  type ENUM('hypo','hyper','capteur') NOT NULL,
  valeur DECIMAL(6,1) NULL,
  seuil DECIMAL(6,1) NULL,
  statut ENUM('envoyee','lue') NOT NULL DEFAULT 'envoyee',
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS sos_evenements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL,
  declencheur ENUM('manuel','non_confirmation','capteur') NOT NULL,
  etape VARCHAR(200) NOT NULL,
  contact_prevenu VARCHAR(120) NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS contacts_urgence (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  nom VARCHAR(120) NOT NULL,
  telephone VARCHAR(20) NOT NULL,
  relation ENUM('famille','conjoint','ami','autre') NOT NULL,
  consentement TINYINT(1) NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS publications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  titre VARCHAR(200) NOT NULL,
  categorie VARCHAR(60) NOT NULL,
  contenu TEXT NOT NULL,
  cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS commentaires (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  publication_id INT NOT NULL,
  contenu TEXT NOT NULL,
  cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (publication_id) REFERENCES publications(id) ON DELETE CASCADE,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS chatbot_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  role ENUM('user','assistant') NOT NULL,
  contenu TEXT NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS connexions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  ts DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip VARCHAR(45) NOT NULL,
  appareil VARCHAR(150) NOT NULL,
  INDEX (utilisateur_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS partages (
  utilisateur_id INT NOT NULL,
  categorie VARCHAR(30) NOT NULL,
  autorise TINYINT(1) NOT NULL DEFAULT 0,
  maj_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (utilisateur_id, categorie),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS groupes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(120) NOT NULL,
  description VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS groupes_membres (
  utilisateur_id INT NOT NULL,
  groupe_id INT NOT NULL,
  rejoint_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (utilisateur_id, groupe_id),
  FOREIGN KEY (utilisateur_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (groupe_id) REFERENCES groupes(id) ON DELETE CASCADE
);

INSERT INTO groupes (nom, description) VALUES
('Vivre avec le diabète', 'Échanger sur le quotidien et les astuces'),
('Nutrition et équilibre', 'Repas, recettes et conseils alimentaires'),
('Activité physique', 'Bouger en sécurité avec une maladie chronique');
