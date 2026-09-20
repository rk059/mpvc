<?php
require __DIR__ . '/content.php';
$posts = read_posts();
usort($posts, static function (array $first, array $second): int {
    return strcmp((string)($second['published_at'] ?? ''), (string)($first['published_at'] ?? ''));
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blog | Smart uPVC Bhuna</title>
  <meta name="description" content="Practical ideas, project notes, and product inspiration from Smart uPVC Bhuna." />
  <link rel="icon" type="image/png" href="logo/website/favicon-32.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="styles.css" />
  <script defer src="script.js?v=3"></script>
</head>
<body class="inner-page">
  <header class="site-header"><div class="container nav-wrap"><a href="index.html" class="brand" aria-label="Smart uPVC Bhuna home"><div class="brand-logo-wrap"><img src="logo/website/header-logo-200.png" alt="Smart uPVC Bhuna logo" /></div></a><button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false"><span></span><span></span><span></span></button><nav class="site-nav" aria-label="Main navigation"><a href="index.html">Home</a><a href="about.html">About</a><a href="products.html#upvc">uPVC Doors &amp; Windows</a><a href="products.html#aluminium">Aluminium Doors &amp; Windows</a><a href="gallery.html">Gallery</a><a class="active" href="blog.php">Blog</a><a href="contact.html">Contact</a><a class="nav-cta" href="tel:+919253131031" aria-label="Call Smart uPVC Bhuna"><i class="fa-solid fa-phone"></i></a></nav></div></header>
  <main>
    <section class="page-hero"><div class="page-hero-media"><img src="images/WhatsApp Image 2026-09-19 at 10.04.19.jpeg" alt="Recent Smart uPVC Bhuna project detail" /></div><div class="page-hero-overlay"></div><div class="container page-hero-content"><span class="eyebrow">The Smart uPVC journal</span><h1>Ideas for better spaces.</h1><p>Project notes, practical guidance, and fresh work from the Smart uPVC Bhuna team.</p></div></section>
    <section class="section"><div class="container"><div class="section-heading centered"><span class="section-kicker">Latest from us</span><h2>Useful ideas, beautifully made.</h2></div><div class="article-grid">
      <?php if (!$posts): ?>
        <article class="article-card reveal-on-scroll"><img src="images/WhatsApp Image 2026-09-19 at 10.04.20.jpeg" alt="Modern sliding window detail" /><div><span>Project note</span><h3>Choosing the right opening for your space</h3><p>Start with the way a room is used, then choose the frame, light, and movement that make it work.</p><a href="contact.html">Discuss your space <i class="fa-solid fa-arrow-right"></i></a></div></article>
      <?php else: foreach ($posts as $post): ?>
        <article class="article-card reveal-on-scroll"><?php if (!empty($post['image'])): ?><img src="<?= e((string)$post['image']) ?>" alt="<?= e((string)$post['title']) ?>" /><?php endif; ?><div><span><?= e((string)($post['category'] ?? 'Journal')) ?> · <?= e(public_date((string)($post['published_at'] ?? ''))) ?></span><h3><?= e((string)$post['title']) ?></h3><p><?= nl2br(e((string)$post['body'])) ?></p><a href="contact.html">Discuss your project <i class="fa-solid fa-arrow-right"></i></a></div></article>
      <?php endforeach; endif; ?>
    </div></div></section>
    <section class="section cta-section"><div class="container cta-panel reveal-on-scroll"><div class="cta-text"><span class="section-kicker accent">Have an idea?</span><h2>Bring us the opening. We’ll help with the direction.</h2></div><div class="cta-actions"><a class="button primary large" href="contact.html"><i class="fa-solid fa-paper-plane"></i> Send an enquiry</a></div></div></section>
  </main>
  <footer class="site-footer"><div class="container footer-grid"><div class="footer-brand-block"><div class="footer-brand-logo-wrap"><img src="logo/website/header-logo-200.png" alt="Smart uPVC Bhuna logo" /></div><div><h3>Smart uPVC Bhuna</h3><p>uPVC &amp; Aluminium Sliding Doors and Windows</p></div></div><div><h4>Explore</h4><ul><li><a href="index.html">Home</a></li><li><a href="products.html">Products</a></li><li><a href="gallery.html">Gallery</a></li><li><a href="blog.php">Blog</a></li><li><a href="contact.html">Contact</a></li></ul></div><div><h4>Connect</h4><ul><li><a href="tel:+919253131031">+91 9253131031</a></li><li><a href="https://wa.me/919253131031">WhatsApp us</a></li></ul></div></div><div class="footer-bottom"><p>© 2026 Smart uPVC Bhuna. All Rights Reserved.</p></div></footer>
</body>
</html>
