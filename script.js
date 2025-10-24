// Configuration
const META_URL = 'uploads/metadata.json';
const BATCH_SIZE = 24; // images loaded per batch

// State
let allPhotos = [];
let filtered = [];
let currentFilter = 'all';
let cursor = 0;
let lightboxIndex = -1;

// Utils
const shuffle = (arr) => {
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
};

// DOM
const gallery = document.getElementById('gallery');
const sentinel = document.getElementById('sentinel');

// Lightbox elements
const lb = document.getElementById('lightbox');
const lbImg = document.getElementById('lightboxImg');
const lbClose = document.getElementById('lbClose');
const lbPrev = document.getElementById('lbPrev');
const lbNext = document.getElementById('lbNext');

function openLightbox(index) {
  lightboxIndex = index;
  const item = filtered[lightboxIndex];
  if (!item) return;
  lbImg.src = item.src;
  lbImg.alt = item.tag || '';
  lb.classList.add('open');
  lb.setAttribute('aria-hidden', 'false');
}
function closeLightbox() {
  lb.classList.remove('open');
  lb.setAttribute('aria-hidden', 'true');
  lightboxIndex = -1;
}
function navLightbox(dir) {
  if (lightboxIndex < 0) return;
  lightboxIndex += dir;
  if (lightboxIndex < 0) lightboxIndex = filtered.length - 1;
  if (lightboxIndex >= filtered.length) lightboxIndex = 0;
  const item = filtered[lightboxIndex];
  lbImg.src = item.src;
  lbImg.alt = item.tag || '';
}

// Render a batch
function renderBatch() {
  const next = filtered.slice(cursor, cursor + BATCH_SIZE);
  const frag = document.createDocumentFragment();
  next.forEach((p, idx) => {
    const fig = document.createElement('figure');
    const img = document.createElement('img');
    img.loading = 'lazy';
    img.decoding = 'async';
    img.src = p.src;
    img.alt = p.tag || '';
    img.addEventListener('click', () => openLightbox(cursor + idx));
    const tag = document.createElement('div');
    tag.className = 'figure-tag';
    tag.textContent = p.tag;
    fig.appendChild(img);
    if (p.tag && p.tag !== 'all') fig.appendChild(tag);
    frag.appendChild(fig);
  });
  gallery.appendChild(frag);
  cursor += next.length;
}

// Apply filter
function applyFilter(tag) {
  currentFilter = tag;
  cursor = 0;
  gallery.innerHTML = '';
  if (tag === 'all') filtered = [...allPhotos];
  else filtered = allPhotos.filter(p => p.tag === tag);
  renderBatch(); // first batch
}

// Load metadata and init
async function init() {
  const res = await fetch(META_URL, { cache: 'no-store' });
  const data = await res.json();
  // Normalize {src, tag}
  allPhotos = data.map(d => ({
    src: d.src || d.url || d.path,
    tag: d.tag || d.category || 'all'
  })).filter(d => !!d.src);
  shuffle(allPhotos);
  filtered = [...allPhotos];
  renderBatch();

  // Infinite scroll
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        if (cursor < filtered.length) renderBatch();
      }
    });
  }, { rootMargin: '200px' });
  io.observe(sentinel);

  // Filter buttons
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      applyFilter(btn.dataset.filter);
    });
  });

  // Shuffle button
  document.getElementById('shuffleBtn').addEventListener('click', () => {
    shuffle(filtered);
    cursor = 0;
    gallery.innerHTML = '';
    renderBatch();
  });
}

// Lightbox events
lbClose.addEventListener('click', closeLightbox);
lbPrev.addEventListener('click', () => navLightbox(-1));
lbNext.addEventListener('click', () => navLightbox(1));
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowLeft') navLightbox(-1);
  if (e.key === 'ArrowRight') navLightbox(1);
});

init();
// ======== POP-UP CALL TO ACTION ========
document.addEventListener('DOMContentLoaded', () => {
  const popup = document.getElementById('ctaPopup');
  const closeBtn = document.querySelector('.cta-close');

  // Affiche le pop-up après 6 secondes
  setTimeout(() => {
    popup.classList.add('show');
  }, 6000);

  // Ferme le pop-up quand on clique sur la croix ou à l’extérieur
  closeBtn.addEventListener('click', () => popup.classList.remove('show'));
  popup.addEventListener('click', (e) => {
    if (e.target === popup) popup.classList.remove('show');
  });
});
