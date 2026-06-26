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

CREATE TABLE IF NOT EXISTS books (
  uuid CHAR(36) NOT NULL,
  title VARCHAR(190) NOT NULL,
  author VARCHAR(190) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  owner_uuid CHAR(36) NOT NULL,
  status ENUM('available', 'reserved', 'exchanged', 'removed') NOT NULL DEFAULT 'available',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  exchanged_at DATETIME DEFAULT NULL,
  PRIMARY KEY (uuid),
  KEY books_owner_uuid_index (owner_uuid),
  KEY books_status_index (status),
  CONSTRAINT books_owner_uuid_foreign
    FOREIGN KEY (owner_uuid) REFERENCES users(uuid)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
