ALTER TABLE gallery_media
  ADD COLUMN is_default TINYINT(1) NOT NULL DEFAULT 0 AFTER type,
  ADD UNIQUE KEY uq_gallery_media_file (file);

INSERT IGNORE INTO gallery_media (caption, file, type, is_default) VALUES
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