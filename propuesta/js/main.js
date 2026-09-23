/* ============================================================
   TiendaMV — main.js
   ============================================================ */

'use strict';

/* ── Utilities ── */
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
  requestAnimationFrame(() => {
    requestAnimationFrame(() => toast.classList.add('show'));
  });
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 350);
  }, dur);
}

/* ── Cart (localStorage) ── */
const Cart = {
  KEY: 'tiendaMV_cart',

  getAll() {
    try { return JSON.parse(localStorage.getItem(this.KEY)) || []; }
    catch { return []; }
  },

  save(items) {
    localStorage.setItem(this.KEY, JSON.stringify(items));
  },

  add(product) {
    const items = this.getAll();
    const idx = items.findIndex(i => i.id === product.id);
    if (idx > -1) {
      items[idx].qty = (items[idx].qty || 1) + (product.qty || 1);
    } else {
      items.push({ ...product, qty: product.qty || 1 });
    }
    this.save(items);
    this.updateCounter();
    showToast('Producto agregado al carrito ✓');
  },

  remove(id) {
    const items = this.getAll().filter(i => i.id !== id);
    this.save(items);
    this.updateCounter();
  },

  updateQty(id, qty) {
    const items = this.getAll();
    const idx = items.findIndex(i => i.id === id);
    if (idx > -1) {
      if (qty < 1) { this.remove(id); return; }
      items[idx].qty = qty;
      this.save(items);
    }
    this.updateCounter();
  },

  count() {
    return this.getAll().reduce((s, i) => s + (i.qty || 1), 0);
  },

  total() {
    return this.getAll().reduce((s, i) => s + i.price * (i.qty || 1), 0);
  },

  updateCounter() {
    const badge = $('.cart-count');
    if (!badge) return;
    const n = this.count();
    badge.textContent = n;
    badge.classList.toggle('hidden', n === 0);
  }
};

/* ── Search Autocomplete ── */
const SUGGESTIONS = [
  'iPhone 15 Pro', 'iPhone 14', 'Samsung Galaxy S24', 'Samsung Galaxy A54',
  'Notebook HP', 'Notebook Lenovo', 'MacBook Air', 'MacBook Pro',
  'Zapatillas Nike', 'Zapatillas Adidas', 'Zapatillas New Balance',
  'Smart TV 55 pulgadas', 'Smart TV Samsung 50', 'Televisor LG OLED',
  'Audífonos Sony', 'Audífonos JBL', 'AirPods Pro',
  'Refrigerador Samsung', 'Lavadora LG', 'Microondas Electrolux',
  'Polera algodón', 'Jeans Levis', 'Chaqueta polar',
  'Bicicleta MTB', 'Trotadora eléctrica', 'Mancuernas 10kg',
  'Silla gamer', 'Escritorio madera', 'Sofá 3 cuerpos',
  'Drone DJI Mini', 'Cámara Canon', 'GoPro Hero 12',
  'Consola PS5', 'Nintendo Switch', 'Control Xbox',
  'Neumático 185/65 R15', 'Batería auto', 'Aceite motor',
];

function initSearchAutocomplete() {
  const input = $('.search-input');
  const dropdown = $('.search-autocomplete');
  if (!input || !dropdown) return;

  let debounceTimer;

  input.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const q = input.value.trim().toLowerCase();
      if (!q) { dropdown.classList.remove('active'); return; }
      const matches = SUGGESTIONS.filter(s => s.toLowerCase().includes(q)).slice(0, 7);
      if (!matches.length) { dropdown.classList.remove('active'); return; }
      dropdown.innerHTML = matches.map(s => `
        <div class="autocomplete-item" role="option">
          <span class="icon">🔍</span>
          <span>${s}</span>
        </div>`).join('');
      dropdown.classList.add('active');

      $$('.autocomplete-item', dropdown).forEach(item => {
        item.addEventListener('mousedown', e => {
          e.preventDefault();
          input.value = item.querySelector('span:last-child').textContent;
          dropdown.classList.remove('active');
          redirectToSearch(input.value);
        });
      });
    }, 150);
  });

  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      dropdown.classList.remove('active');
      redirectToSearch(input.value);
    }
    if (e.key === 'Escape') dropdown.classList.remove('active');
  });

  document.addEventListener('click', e => {
    if (!e.target.closest('.search-form')) dropdown.classList.remove('active');
  });
}

function redirectToSearch(query) {
  if (!query.trim()) return;
  window.location.href = `productos.html?q=${encodeURIComponent(query.trim())}`;
}

/* ── Carousel ── */
function initCarousel() {
  const track = $('.carousel-track');
  if (!track) return;

  const slides = $$('.carousel-slide', track);
  const dots = $$('.dot');
  const total = slides.length;
  let current = 0;
  let autoTimer;

  function goTo(idx) {
    current = ((idx % total) + total) % total;
    track.style.transform = `translateX(-${current * 100}%)`;
    dots.forEach((d, i) => d.classList.toggle('active', i === current));
  }

  function next() { goTo(current + 1); }
  function prev() { goTo(current - 1); }

  function startAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(next, 5000);
  }

  // Controls
  const btnNext = $('.carousel-next');
  const btnPrev = $('.carousel-prev');
  if (btnNext) btnNext.addEventListener('click', () => { next(); startAuto(); });
  if (btnPrev) btnPrev.addEventListener('click', () => { prev(); startAuto(); });

  dots.forEach((d, i) => d.addEventListener('click', () => { goTo(i); startAuto(); }));

  // Touch / swipe
  let touchStartX = 0;
  track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) { diff > 0 ? next() : prev(); startAuto(); }
  }, { passive: true });

  goTo(0);
  startAuto();
}

/* ── Product Image Gallery ── */
function initGallery() {
  const mainImg = $('.gallery-main');
  if (!mainImg) return;

  $$('.thumbnail').forEach(thumb => {
    thumb.addEventListener('click', () => {
      mainImg.src = thumb.dataset.full || thumb.querySelector('img').src;
      $$('.thumbnail').forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
    });
  });
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

    dec && dec.addEventListener('click', () => {
      const v = parseInt(inp.value) || 1;
      if (v > min) inp.value = v - 1;
    });
    inc && inc.addEventListener('click', () => {
      const v = parseInt(inp.value) || 1;
      if (v < max) inp.value = v + 1;
    });
    inp.addEventListener('change', () => {
      let v = parseInt(inp.value) || min;
      inp.value = Math.max(min, Math.min(max, v));
    });
  });
}

/* ── Add to Cart buttons ── */
function initAddToCart() {
  $$('[data-add-cart]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id    = btn.dataset.id    || btn.closest('[data-product-id]')?.dataset.productId || String(Date.now());
      const title = btn.dataset.title || btn.closest('[data-product-id]')?.dataset.title || 'Producto';
      const price = parseInt(btn.dataset.price) || 0;
      const img   = btn.dataset.img   || '';
      const qtyEl = btn.closest('.product-actions')?.querySelector('.qty-input');
      const qty   = qtyEl ? parseInt(qtyEl.value) : 1;

      Cart.add({ id, title, price, img, qty });
    });
  });
}

/* ── Mobile Menu ── */
function initMobileMenu() {
  const btn     = $('.mobile-menu-btn');
  const overlay = $('.mobile-nav-overlay');
  const nav     = $('.mobile-nav');
  const closeBtn = $('.mobile-nav-close');

  if (!btn) return;

  function open()  { overlay?.classList.add('active'); nav?.classList.add('active'); document.body.style.overflow = 'hidden'; }
  function close() { overlay?.classList.remove('active'); nav?.classList.remove('active'); document.body.style.overflow = ''; }

  btn.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  overlay?.addEventListener('click', close);
}

/* ── Cart Page ── */
function initCartPage() {
  const cartMain = $('.cart-main-items');
  if (!cartMain) return;

  function render() {
    const items = Cart.getAll();
    const empty = $('.empty-cart');

    if (!items.length) {
      cartMain.innerHTML = '';
      if (empty) empty.classList.remove('hidden');
      updateSummary();
      return;
    }
    if (empty) empty.classList.add('hidden');

    cartMain.innerHTML = items.map(item => `
      <div class="cart-item" data-id="${item.id}">
        <img class="cart-item-img"
             src="${item.img || `https://picsum.photos/seed/${item.id}/200/200`}"
             alt="${item.title}" loading="lazy">
        <div class="cart-item-info">
          <a href="producto.html" class="cart-item-title">${item.title}</a>
          <p class="cart-item-condition">Nuevo | +50 vendidos</p>
          <p class="cart-item-shipping">🚚 Envío gratis</p>
          <div class="quantity-control mt-8" style="transform:scale(.9);transform-origin:left">
            <button class="qty-btn dec" data-id="${item.id}">−</button>
            <input class="qty-input" type="number" value="${item.qty}" min="1" max="99" data-id="${item.id}">
            <button class="qty-btn inc" data-id="${item.id}">+</button>
          </div>
        </div>
        <div class="cart-item-actions">
          <div class="cart-item-price">
            <small>${fmtCLP(Math.round(item.price / 0.85))}</small>
            ${fmtCLP(item.price * item.qty)}
          </div>
          <button class="cart-remove-btn" data-id="${item.id}">Eliminar</button>
        </div>
      </div>`).join('');

    // Bind remove
    $$('.cart-remove-btn', cartMain).forEach(btn => {
      btn.addEventListener('click', () => {
        Cart.remove(btn.dataset.id);
        render();
      });
    });

    // Bind qty
    $$('.qty-btn.dec', cartMain).forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const item = Cart.getAll().find(i => i.id === id);
        if (item) { Cart.updateQty(id, item.qty - 1); render(); }
      });
    });
    $$('.qty-btn.inc', cartMain).forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const item = Cart.getAll().find(i => i.id === id);
        if (item) { Cart.updateQty(id, item.qty + 1); render(); }
      });
    });
    $$('.qty-input', cartMain).forEach(inp => {
      inp.addEventListener('change', () => {
        Cart.updateQty(inp.dataset.id, parseInt(inp.value) || 1);
        render();
      });
    });

    updateSummary();
  }

  function updateSummary() {
    const items = Cart.getAll();
    const subtotal = Cart.total();
    const originalTotal = items.reduce((s, i) => s + Math.round(i.price / 0.85) * i.qty, 0);
    const savings = originalTotal - subtotal;

    const el = id => document.getElementById(id);
    if (el('summary-items'))    el('summary-items').textContent    = `${items.reduce((s,i)=>s+i.qty,0)} producto(s)`;
    if (el('summary-subtotal')) el('summary-subtotal').textContent = fmtCLP(subtotal);
    if (el('summary-shipping')) el('summary-shipping').textContent = 'Gratis';
    if (el('summary-total'))    el('summary-total').textContent    = fmtCLP(subtotal);
    if (el('summary-savings') && savings > 0) {
      el('summary-savings').textContent = `Ahorrás ${fmtCLP(savings)} en esta compra`;
    }
  }

  render();

  // Checkout
  const checkoutBtn = $('.checkout-btn');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', () => {
      if (!Cart.count()) { showToast('Tu carrito está vacío', 'error'); return; }
      showToast('Procesando pago… ¡Gracias por tu compra!', 'success', 4000);
    });
  }

  // Coupon
  const couponBtn = $('.coupon-btn');
  if (couponBtn) {
    couponBtn.addEventListener('click', () => {
      const code = $('.coupon-input')?.value.trim().toUpperCase();
      if (!code) return;
      if (['DESCUENTO10', 'TIENDAMV', 'BIENVENIDO'].includes(code)) {
        showToast(`Cupón ${code} aplicado — 10% de descuento`, 'success');
      } else {
        showToast('Cupón inválido o expirado', 'error');
      }
    });
  }
}

/* ── Range Slider (productos.html) ── */
function initRangeSlider() {
  const slider = $('.range-slider');
  const minInput = document.getElementById('price-min');
  const maxInput = document.getElementById('price-max');
  if (!slider || !minInput || !maxInput) return;

  slider.addEventListener('input', () => {
    maxInput.value = parseInt(slider.value).toLocaleString('es-CL');
  });
}

/* ── Search query on productos.html ── */
function initSearchQuery() {
  const h1 = document.getElementById('search-heading');
  if (!h1) return;
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q');
  if (q) h1.innerHTML = `Resultados para <strong>"${q}"</strong>`;
}

/* ── Product star rating highlight ── */
function renderStars(rating) {
  const full = Math.floor(rating);
  const half = rating % 1 >= 0.5;
  let s = '★'.repeat(full);
  if (half) s += '½';
  s += '☆'.repeat(5 - full - (half ? 1 : 0));
  return s;
}

/* ── Tabs (producto.html) ── */
function initTabs() {
  const tabs = $$('.tab-btn');
  if (!tabs.length) return;

  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.tab;
      tabs.forEach(t => t.classList.remove('active'));
      $$('.tab-content').forEach(c => c.classList.remove('active'));
      btn.classList.add('active');
      const panel = document.getElementById(`tab-${target}`);
      if (panel) panel.classList.add('active');
    });
  });
}

/* ── Pagination (productos.html) ── */
function initPagination() {
  const btns = $$('.page-btn:not(.disabled)');
  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.classList.contains('active')) return;
      $$('.page-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
  Cart.updateCounter();
  initSearchAutocomplete();
  initCarousel();
  initGallery();
  initQuantityControl();
  initAddToCart();
  initMobileMenu();
  initCartPage();
  initRangeSlider();
  initSearchQuery();
  initTabs();
  initPagination();
});
