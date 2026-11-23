CREATE DATABASE IF NOT EXISTS gestion_users CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_users;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  date_naissance DATE NOT NULL,
  genre ENUM('homme','femme') NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  telephone VARCHAR(15) NOT NULL,
  hebergement VARCHAR(150) NULL,
  role ENUM('client','medecin') NOT NULL DEFAULT 'client',
  specialite VARCHAR(80) NULL,
  preuve_identite VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
