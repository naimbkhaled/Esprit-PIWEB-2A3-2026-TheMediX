-- Run this ONCE if your `users` table already exists.
USE session;

SET @has_is_active = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_active'
);
SET @add_is_active_sql = IF(
  @has_is_active = 0,
  'ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1',
  'SELECT 1'
);
PREPARE add_is_active_stmt FROM @add_is_active_sql;
EXECUTE add_is_active_stmt;
DEALLOCATE PREPARE add_is_active_stmt;

SET @has_provider = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'provider'
);
SET @add_provider_sql = IF(
  @has_provider = 0,
  'ALTER TABLE users ADD COLUMN provider VARCHAR(20) NULL',
  'SELECT 1'
);
PREPARE add_provider_stmt FROM @add_provider_sql;
EXECUTE add_provider_stmt;
DEALLOCATE PREPARE add_provider_stmt;

SET @has_provider_id = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'provider_id'
);
SET @add_provider_id_sql = IF(
  @has_provider_id = 0,
  'ALTER TABLE users ADD COLUMN provider_id VARCHAR(100) NULL',
  'SELECT 1'
);
PREPARE add_provider_id_stmt FROM @add_provider_id_sql;
EXECUTE add_provider_id_stmt;
DEALLOCATE PREPARE add_provider_id_stmt;

SET @has_provider_index = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'uq_provider'
);
SET @add_provider_index_sql = IF(
  @has_provider_index = 0,
  'ALTER TABLE users ADD UNIQUE KEY uq_provider (provider, provider_id)',
  'SELECT 1'
);
PREPARE add_provider_index_stmt FROM @add_provider_index_sql;
EXECUTE add_provider_index_stmt;
DEALLOCATE PREPARE add_provider_index_stmt;

CREATE TABLE IF NOT EXISTS remember_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  selector CHAR(24) NOT NULL UNIQUE,
  validator_hash CHAR(64) NOT NULL,
  expires_at INT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at INT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_profiles (
  user_id INT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  date_naissance DATE NOT NULL,
  genre ENUM('homme','femme') NOT NULL,
  telephone VARCHAR(15) NOT NULL,
  hebergement VARCHAR(150) NULL,
  account_type ENUM('client','medecin') NOT NULL DEFAULT 'client',
  specialite VARCHAR(80) NULL,
  preuve_identite VARCHAR(255) NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
