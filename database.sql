CREATE TABLE enquiries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NULL,
  project VARCHAR(120) NULL,
  message TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_enquiries_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
  id VARCHAR(32) NOT NULL,
  title VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  category VARCHAR(60) NOT NULL DEFAULT 'Journal',
  image VARCHAR(255) NULL,
  published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_posts_published_at (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gallery_media (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  caption VARCHAR(160) NOT NULL,
  file VARCHAR(255) NOT NULL,
  type ENUM('image', 'video') NOT NULL DEFAULT 'image',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_gallery_media_file (file),
  INDEX idx_gallery_media_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO gallery_media (caption, file, type, is_default) VALUES
('uPVC door profile design', 'images/WhatsApp Image 2026-09-17 at 17.00.31.jpeg', 'image', 1),
('Modern window frame detail', 'images/WhatsApp Image 2026-09-17 at 17.09.13 (1).jpeg', 'image', 1),
('Sliding window installation', 'images/WhatsApp Image 2026-09-17 at 17.09.13 (2).jpeg', 'image', 1),
('White aluminium window detail', 'images/WhatsApp Image 2026-09-17 at 17.09.13 (3).jpeg', 'image', 1),
('Sliding aluminium frame design', 'images/WhatsApp Image 2026-09-17 at 17.09.13 (4).jpeg', 'image', 1),
('Window glass panel installation', 'images/WhatsApp Image 2026-09-17 at 17.09.14 (1).jpeg', 'image', 1),
('Premium sliding door with aluminium profile', 'images/WhatsApp Image 2026-09-17 at 17.09.15 (1).jpeg', 'image', 1),
('Architectural window frame', 'images/WhatsApp Image 2026-09-17 at 17.09.16.jpeg', 'image', 1),
('Recent Smart uPVC Bhuna project', 'images/IMG-20260919-WA0000.jpg', 'image', 1),
('Recent sliding window detail', 'images/IMG-20260919-WA0004.jpg', 'image', 1),
('Recent glass window installation', 'images/WhatsApp Image 2026-09-19 at 10.04.20.jpeg', 'image', 1),
('Recent window frame detail', 'images/WhatsApp Image 2026-09-19 at 10.04.21.jpeg', 'image', 1),
('Glass gate handle detail', 'images/glass gate handle.jpeg', 'image', 1),
('Glass shop windows', 'images/shop glass windows.jpeg', 'image', 1);
