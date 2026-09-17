const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.querySelector('.site-nav');

if (navToggle && siteNav) {
  navToggle.addEventListener('click', () => {
    const isOpen = siteNav.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', String(isOpen));
  });

  siteNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      siteNav.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
    });
  });
}

const revealItems = document.querySelectorAll('.reveal-on-scroll');
const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.15 }
);

revealItems.forEach((item) => revealObserver.observe(item));

const galleryItems = [...document.querySelectorAll('.gallery-item')];
const modal = document.getElementById('galleryModal');
const modalImage = document.getElementById('modalImage');
const modalClose = document.querySelector('.modal-close');
const prevBtn = document.querySelector('.modal-arrow.prev');
const nextBtn = document.querySelector('.modal-arrow.next');

const galleryImages = galleryItems.map((item) => item.querySelector('img').src);
let currentIndex = 0;

function openModal(index) {
  if (!modal || !modalImage || !galleryImages.length) return;
  currentIndex = index;
  modalImage.src = galleryImages[currentIndex];
  modal.classList.add('open');
  modal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  if (!modal) return;
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

function changeModal(step) {
  currentIndex = (currentIndex + step + galleryImages.length) % galleryImages.length;
  modalImage.src = galleryImages[currentIndex];
}

galleryItems.forEach((item, index) => {
  item.addEventListener('click', () => openModal(index));
});

modalClose?.addEventListener('click', closeModal);
prevBtn?.addEventListener('click', () => changeModal(-1));
nextBtn?.addEventListener('click', () => changeModal(1));

modal?.addEventListener('click', (event) => {
  if (event.target === modal) {
    closeModal();
  }
});

document.addEventListener('keydown', (event) => {
  if (!modal?.classList.contains('open')) return;

  if (event.key === 'Escape') closeModal();
  if (event.key === 'ArrowLeft') changeModal(-1);
  if (event.key === 'ArrowRight') changeModal(1);
});
