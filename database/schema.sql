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

CREATE TABLE IF NOT EXISTS conversations (
  uuid CHAR(36) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  last_message_at DATETIME DEFAULT NULL,
  PRIMARY KEY (uuid),
  KEY conversations_last_message_at_index (last_message_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conversation_participants (
  conversation_uuid CHAR(36) NOT NULL,
  user_uuid CHAR(36) NOT NULL,
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_read_at DATETIME DEFAULT NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (conversation_uuid, user_uuid),
  KEY conversation_participants_user_uuid_index (user_uuid),
  KEY conversation_participants_last_read_at_index (last_read_at),
  CONSTRAINT conversation_participants_conversation_uuid_foreign
    FOREIGN KEY (conversation_uuid) REFERENCES conversations(uuid)
    ON DELETE CASCADE,
  CONSTRAINT conversation_participants_user_uuid_foreign
    FOREIGN KEY (user_uuid) REFERENCES users(uuid)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  uuid CHAR(36) NOT NULL,
  conversation_uuid CHAR(36) NOT NULL,
  sender_uuid CHAR(36) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (uuid),
  KEY messages_conversation_created_at_index (conversation_uuid, created_at),
  KEY messages_sender_uuid_index (sender_uuid),
  CONSTRAINT messages_conversation_uuid_foreign
    FOREIGN KEY (conversation_uuid) REFERENCES conversations(uuid)
    ON DELETE CASCADE,
  CONSTRAINT messages_sender_uuid_foreign
    FOREIGN KEY (sender_uuid) REFERENCES users(uuid)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
