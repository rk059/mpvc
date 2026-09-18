const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.querySelector('.site-nav');

const cleanRoutes = {
  'index.html': '/',
  'about.html': '/about',
  'products.html': '/products',
  'gallery.html': '/gallery',
  'articles.html': '/articles',
  'contact.html': '/contact',
};

function cleanRoute(path) {
  const normalizedPath = path.replace(/^\.\//, '');
  return cleanRoutes[normalizedPath] || path;
}

function pageFile(pathname) {
  const route = pathname.replace(/^\//, '') || 'index';
  return `${route}.html`;
}

document.querySelectorAll('a[href]').forEach((link) => {
  const rawHref = link.getAttribute('href');
  if (!rawHref || rawHref.startsWith('#') || rawHref.includes('://') || rawHref.startsWith('tel:') || rawHref.startsWith('mailto:')) return;
  const [path, hash] = rawHref.split('#');
  if (!path.endsWith('.html')) return;
  link.setAttribute('href', `${cleanRoute(path)}${hash ? `#${hash}` : ''}`);
});

const cleanCurrentPath = cleanRoute(window.location.pathname.split('/').pop() || 'index.html');
if (window.location.pathname.endsWith('.html')) {
  window.history.replaceState({}, '', `${cleanCurrentPath}${window.location.hash}`);
}

document.addEventListener('click', (event) => {
  const link = event.target.closest('a[href]');
  if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank') return;
  const url = new URL(link.href, window.location.href);
  if (url.origin !== window.location.origin || !url.pathname.startsWith('/')) return;
  const route = cleanRoute(url.pathname.split('/').pop() || 'index.html');
  const routePath = route.startsWith('/') ? route : `/${route}`;
  if (!Object.values(cleanRoutes).includes(routePath)) return;
  event.preventDefault();
  window.location.href = `${pageFile(routePath)}${url.hash}`;
});

const enquiryModalMarkup = `
  <div class="enquiry-modal" id="enquiryModal" aria-hidden="true">
    <div class="enquiry-dialog" role="dialog" aria-modal="true" aria-labelledby="enquiryTitle">
      <button class="enquiry-close" type="button" aria-label="Close enquiry form">&times;</button>
      <div class="enquiry-dialog-copy">
        <span class="section-kicker accent">Start your project</span>
        <h2 id="enquiryTitle">Tell us about your space.</h2>
        <p>Share a few details and our team will get back to you about the right doors or windows.</p>
      </div>
      <form class="enquiry-form" action="enquiry.php" method="post">
        <div class="form-grid">
          <label>Name<input name="name" type="text" autocomplete="name" required /></label>
          <label>Phone<input name="phone" type="tel" autocomplete="tel" required /></label>
          <label>Email <span>(optional)</span><input name="email" type="email" autocomplete="email" /></label>
          <label>Project type<select name="project"><option value="uPVC doors and windows">uPVC doors and windows</option><option value="Aluminium doors and windows">Aluminium doors and windows</option><option value="Home project">Home project</option><option value="Office or commercial project">Office or commercial project</option></select></label>
        </div>
        <label>Message <span>(optional)</span><textarea name="message" rows="4" placeholder="Tell us what you are planning..."></textarea></label>
        <button class="button primary" type="submit"><i class="fa-solid fa-paper-plane"></i> Send enquiry</button>
        <p class="form-status" role="status" aria-live="polite"></p>
      </form>
    </div>
  </div>`;

document.body.insertAdjacentHTML('beforeend', enquiryModalMarkup);

const enquiryModal = document.getElementById('enquiryModal');
const enquiryForm = enquiryModal?.querySelector('.enquiry-form');

function openEnquiryModal() {
  if (!enquiryModal) return;
  enquiryModal.classList.add('open');
  enquiryModal.setAttribute('aria-hidden', 'false');
  document.body.classList.add('modal-open');
  enquiryModal.querySelector('input')?.focus();
}

function closeEnquiryModal() {
  if (!enquiryModal) return;
  enquiryModal.classList.remove('open');
  enquiryModal.setAttribute('aria-hidden', 'true');
  document.body.classList.remove('modal-open');
}

document.querySelectorAll('.enquiry-trigger').forEach((trigger) => trigger.addEventListener('click', openEnquiryModal));
enquiryModal?.querySelector('.enquiry-close')?.addEventListener('click', closeEnquiryModal);
enquiryModal?.addEventListener('click', (event) => {
  if (event.target === enquiryModal) closeEnquiryModal();
});

if (siteNav && !siteNav.querySelector('.enquiry-trigger')) {
  const trigger = document.createElement('button');
  trigger.className = 'nav-enquiry enquiry-trigger';
  trigger.type = 'button';
  trigger.innerHTML = '<i class="fa-solid fa-paper-plane"></i><span>Enquire</span>';
  siteNav.insertBefore(trigger, siteNav.querySelector('.nav-cta'));
  trigger.addEventListener('click', openEnquiryModal);
}

document.querySelectorAll('.site-footer').forEach((footer) => {
  if (footer.querySelector('.social-links')) return;
  const social = document.createElement('div');
  social.className = 'social-links';
  social.innerHTML = '<a href="https://www.facebook.com/share/1BnhGCAuyf/" target="_blank" rel="noreferrer noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a><a href="https://wa.me/919253131031" target="_blank" rel="noreferrer noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a><a href="https://www.google.com/search?kgmid=/g/11z5xsq13l&amp;hl=en-IN&amp;q=Smart+uPVC+Bhuna" target="_blank" rel="noreferrer noopener" aria-label="Google Business profile"><i class="fa-brands fa-google"></i></a><span title="Instagram profile link to be added" aria-label="Instagram profile link to be added"><i class="fa-brands fa-instagram"></i></span><span title="YouTube profile link to be added" aria-label="YouTube profile link to be added"><i class="fa-brands fa-youtube"></i></span><span title="LinkedIn profile link to be added" aria-label="LinkedIn profile link to be added"><i class="fa-brands fa-linkedin-in"></i></span>';
  footer.querySelector('.footer-brand-block')?.appendChild(social);
});

if (!document.querySelector('.site-nav a[href="articles.html"]') && siteNav) {
  const articlesLink = document.createElement('a');
  articlesLink.href = 'articles.html';
  articlesLink.textContent = 'Articles';
  siteNav.insertBefore(articlesLink, siteNav.querySelector('a[href="contact.html"]'));
}

window.setTimeout(() => {
  if (!sessionStorage.getItem('enquiryPromptShown')) {
    openEnquiryModal();
    sessionStorage.setItem('enquiryPromptShown', 'true');
  }
}, 8000);

let scrollPromptShown = false;
window.addEventListener('scroll', () => {
  if (scrollPromptShown || sessionStorage.getItem('enquiryPromptShown')) return;
  const scrollable = document.documentElement.scrollHeight - window.innerHeight;
  if (scrollable > 0 && window.scrollY / scrollable > 0.42) {
    scrollPromptShown = true;
    openEnquiryModal();
    sessionStorage.setItem('enquiryPromptShown', 'true');
  }
}, { passive: true });

enquiryForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  const status = enquiryForm.querySelector('.form-status');
  const submitButton = enquiryForm.querySelector('button[type="submit"]');
  submitButton.disabled = true;
  status.textContent = 'Sending your enquiry...';
  try {
    const response = await fetch(enquiryForm.action, { method: 'POST', body: new FormData(enquiryForm), headers: { Accept: 'application/json' } });
    const result = await response.json();
    status.textContent = result.message;
    if (result.success) enquiryForm.reset();
  } catch (error) {
    status.textContent = 'Please call us directly on +91 9253131031.';
  } finally {
    submitButton.disabled = false;
  }
});

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
  if (event.key === 'Escape') closeEnquiryModal();
  if (!modal?.classList.contains('open')) return;

  if (event.key === 'Escape') closeModal();
  if (event.key === 'ArrowLeft') changeModal(-1);
  if (event.key === 'ArrowRight') changeModal(1);
});
