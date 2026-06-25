CREATE DATABASE IF NOT EXISTS tom_troc
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tom_troc;

CREATE TABLE IF NOT EXISTS users (
  uuid CHAR(36) NOT NULL,
  pseudo VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  profile_picture VARCHAR(255) NOT NULL DEFAULT 'assets/images/Alexlecture.png',
  user_type ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  register_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (uuid),
  UNIQUE KEY users_email_unique (email),
  KEY users_pseudo_index (pseudo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
