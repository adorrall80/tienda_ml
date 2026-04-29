'use strict';

const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const fmtCLP = n => '$' + Number(n).toLocaleString('es-CL');

/* ── Toast ── */
function showToast(msg, type = 'success', dur = 3000) {
  let container = $('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = msg;
  container.appendChild(toast);
  requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
  setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 350); }, dur);
}

/* ── Cart (localStorage) ── */
const Cart = {
  KEY: 'tiendaMV_cart',
  getAll() { try { return JSON.parse(localStorage.getItem(this.KEY)) || []; } catch { return []; } },
  save(items) { localStorage.setItem(this.KEY, JSON.stringify(items)); },
  add(product) {
    const items = this.getAll();
    const idx = items.findIndex(i => i.id === product.id);
    if (idx > -1) { items[idx].qty = (items[idx].qty || 1) + (product.qty || 1); }
    else { items.push({ ...product, qty: product.qty || 1 }); }
    this.save(items); this.updateCounter();
    showToast('Producto agregado al carrito ✓');
  },
  remove(id) { this.save(this.getAll().filter(i => i.id !== id)); this.updateCounter(); },
  updateQty(id, qty) {
    const items = this.getAll();
    const idx = items.findIndex(i => i.id === id);
    if (idx > -1) { if (qty < 1) { this.remove(id); return; } items[idx].qty = qty; this.save(items); }
    this.updateCounter();
  },
  count() { return this.getAll().reduce((s, i) => s + (i.qty || 1), 0); },
  total() { return this.getAll().reduce((s, i) => s + i.price * (i.qty || 1), 0); },
  updateCounter() {
    const badge = $('.cart-count');
    if (!badge) return;
    const n = this.count();
    badge.textContent = n;
    badge.classList.toggle('hidden', n === 0);
  }
};

/* ── Search Autocomplete ── */
function initSearchAutocomplete() {
  const input = $('.search-input');
  const dropdown = $('.search-autocomplete');
  if (!input || !dropdown) return;

  let timer;
  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const q = input.value.trim().toLowerCase();
      if (!q) { dropdown.classList.remove('active'); return; }

      fetch(`/buscar/sugerencias?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(matches => {
          if (!matches.length) { dropdown.classList.remove('active'); return; }
          dropdown.innerHTML = matches.map(s => `
            <div class="autocomplete-item" role="option">
              <span class="icon">🔍</span><span>${s}</span>
            </div>`).join('');
          dropdown.classList.add('active');
          $$('.autocomplete-item', dropdown).forEach(item => {
            item.addEventListener('mousedown', e => {
              e.preventDefault();
              input.value = item.querySelector('span:last-child').textContent;
              dropdown.classList.remove('active');
              input.closest('form').submit();
            });
          });
        })
        .catch(() => dropdown.classList.remove('active'));
    }, 150);
  });

  input.addEventListener('keydown', e => {
    if (e.key === 'Escape') dropdown.classList.remove('active');
  });
  document.addEventListener('click', e => {
    if (!e.target.closest('.search-form')) dropdown.classList.remove('active');
  });
}

/* ── Carousel ── */
function initCarousel() {
  const track = $('.carousel-track');
  if (!track) return;
  const slides = $$('.carousel-slide', track);
  const dots = $$('.dot');
  const total = slides.length;
  let current = 0, autoTimer;

  function goTo(idx) {
    current = ((idx % total) + total) % total;
    track.style.transform = `translateX(-${current * 100}%)`;
    dots.forEach((d, i) => d.classList.toggle('active', i === current));
  }
  function startAuto() { clearInterval(autoTimer); autoTimer = setInterval(() => goTo(current + 1), 5000); }

  const btnNext = $('.carousel-next');
  const btnPrev = $('.carousel-prev');
  if (btnNext) btnNext.addEventListener('click', () => { goTo(current + 1); startAuto(); });
  if (btnPrev) btnPrev.addEventListener('click', () => { goTo(current - 1); startAuto(); });
  dots.forEach((d, i) => d.addEventListener('click', () => { goTo(i); startAuto(); }));

  let touchStartX = 0;
  track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) { diff > 0 ? goTo(current + 1) : goTo(current - 1); startAuto(); }
  }, { passive: true });

  goTo(0); startAuto();
}

/* ── Mobile Menu ── */
function initMobileMenu() {
  const btn = $('.mobile-menu-btn');
  const overlay = $('.mobile-nav-overlay');
  const nav = $('.mobile-nav');
  const closeBtn = $('.mobile-nav-close');
  if (!btn) return;

  const open  = () => { overlay?.classList.add('active'); nav?.classList.add('active'); document.body.style.overflow = 'hidden'; };
  const close = () => { overlay?.classList.remove('active'); nav?.classList.remove('active'); document.querySelector('.sidebar')?.classList.remove('mobile-open'); document.body.style.overflow = ''; };

  btn.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  overlay?.addEventListener('click', close);
}

/* ── Quantity Selector ── */
function initQuantityControl() {
  $$('.quantity-control').forEach(ctrl => {
    const inp = ctrl.querySelector('.qty-input');
    const dec = ctrl.querySelector('.qty-btn.dec');
    const inc = ctrl.querySelector('.qty-btn.inc');
    if (!inp) return;
    const max = parseInt(inp.dataset.max || 99);
    const min = parseInt(inp.dataset.min || 1);
    dec?.addEventListener('click', () => { const v = parseInt(inp.value)||1; if (v > min) inp.value = v - 1; });
    inc?.addEventListener('click', () => { const v = parseInt(inp.value)||1; if (v < max) inp.value = v + 1; });
    inp.addEventListener('change', () => { inp.value = Math.max(min, Math.min(max, parseInt(inp.value)||min)); });
  });
}

/* ── Add to Cart ── */
function initAddToCart() {
  $$('[data-add-cart]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id    = btn.dataset.id    || String(Date.now());
      const title = btn.dataset.title || 'Producto';
      const price = parseInt(btn.dataset.price) || 0;
      const img   = btn.dataset.img   || '';
      const qtyEl = btn.closest('.product-actions')?.querySelector('.qty-input');
      Cart.add({ id, title, price, img, qty: qtyEl ? parseInt(qtyEl.value) : 1 });
    });
  });
}

/* ── Gallery ── */
function initGallery() {
  const thumbs  = $$('.thumbnail');
  const mainImg = document.getElementById('main-product-img');
  if (!thumbs.length || !mainImg) return;
  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
      mainImg.src = thumb.dataset.full;
      thumbs.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
    });
  });
}

/* ── Listing Filters ── */
function initListingFilters() {
  const toggleBtn = document.getElementById('toggle-filters');
  const sidebar   = document.querySelector('.sidebar');
  const overlay   = document.querySelector('.mobile-nav-overlay');
  if (!toggleBtn || !sidebar) return;

  toggleBtn.addEventListener('click', () => {
    const isOpen = sidebar.classList.toggle('mobile-open');
    overlay?.classList.toggle('active', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });
}

/* ── Tabs ── */
function initTabs() {
  const tabs = $$('.tab-btn');
  if (!tabs.length) return;
  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.tab;
      tabs.forEach(t => t.classList.remove('active'));
      $$('.tab-content').forEach(c => c.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById(`tab-${target}`)?.classList.add('active');
    });
  });
}

/* ── User menu dropdown ── */
function initUserMenu() {
  const menu = document.getElementById('userMenu');
  const btn  = document.getElementById('userMenuBtn');
  if (!menu || !btn) return;

  btn.addEventListener('click', e => {
    e.stopPropagation();
    const isOpen = menu.classList.toggle('open');
    btn.setAttribute('aria-expanded', isOpen);
  });

  document.addEventListener('click', e => {
    if (!menu.contains(e.target)) {
      menu.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    }
  });
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
  Cart.updateCounter();
  initSearchAutocomplete();
  initCarousel();
  initMobileMenu();
  initQuantityControl();
  initAddToCart();
  initTabs();
  initListingFilters();
  initGallery();
  initUserMenu();
});
