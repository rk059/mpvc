-- Smart uPVC Bhuna blog posts
-- Import this file into the same MySQL database used by the website.
-- The current PHP code reads this table when the database connection is available.

CREATE TABLE IF NOT EXISTS posts (
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

INSERT IGNORE INTO posts (id, title, body, category, image, published_at) VALUES
('blog-001', 'How to Choose the Right uPVC Windows', 'uPVC windows offer durability, low maintenance, energy efficiency, and a clean modern appearance for homes and offices. Before choosing a design, consider the room size, ventilation needs, opening direction, glass type, and frame finish.', 'uPVC Windows', 'images/WhatsApp Image 2026-09-17 at 17.09.14.jpeg', '2026-09-20 10:00:00'),
('blog-002', 'uPVC or Aluminium: Which Is Right for Your Project?', 'uPVC is a practical choice for insulation and everyday comfort, while aluminium provides slim profiles and a contemporary look for larger openings. The right option depends on the project style, dimensions, performance needs, and budget.', 'Product Guide', 'images/WhatsApp Image 2026-09-17 at 17.09.15.jpeg', '2026-09-19 10:00:00'),
('blog-003', 'Simple Ways to Bring More Natural Light Indoors', 'Well-planned windows and sliding doors can make interiors feel brighter and more spacious. Larger glazed areas, lighter frame finishes, and carefully selected opening positions can improve daylight while keeping the design balanced.', 'Home Design', 'images/IMG-20260919-WA0000.jpg', '2026-09-18 10:00:00'),
('blog-004', 'Why Professional Window Installation Matters', 'Good installation helps doors and windows operate smoothly and maintain their performance over time. Accurate measurements, level frames, secure fixing, and careful finishing are essential parts of a reliable installation.', 'Installation', 'images/IMG-20260919-WA0004.jpg', '2026-09-17 10:00:00'),
('blog-005', 'How to Maintain Sliding Doors and Windows', 'Regularly clean the frames, keep tracks free of dust, check hardware, and avoid forcing stiff panels. Small maintenance steps help sliding doors and windows remain smooth, attractive, and dependable for longer.', 'Maintenance', 'images/glass gate handle.jpeg', '2026-09-16 10:00:00');
