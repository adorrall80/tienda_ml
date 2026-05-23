'use strict';

const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const fmtCLP = n => '$' + Number(n).toLocaleString('es-CL');
const fmtCartPrice = n => Number(n) > 0 ? fmtCLP(n) : 'Se regala';
const safeSlug = value => /^[a-z0-9-]+$/i.test(String(value || '')) ? String(value) : '';
const safeHttpUrl = value => {
  try {
    const url = new URL(String(value || ''), window.location.origin);
    return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
  } catch {
    return '';
  }
};
const clearNode = node => { if (node) node.replaceChildren(); };

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
  remove(id) { this.save(this.getAll().filter(i => i.id !== id)); this.updateCounter(); this.renderPage(); },
  updateQty(id, qty) {
    const items = this.getAll();
    const idx = items.findIndex(i => i.id === id);
    if (idx > -1) { if (qty < 1) { this.remove(id); return; } items[idx].qty = qty; this.save(items); }
    this.updateCounter(); this.renderPage();
  },
  count() { return this.getAll().reduce((s, i) => s + (i.qty || 1), 0); },
  total() { return this.getAll().reduce((s, i) => s + i.price * (i.qty || 1), 0); },
  updateCounter() {
    const badge = $('.cart-count');
    if (!badge) return;
    const n = this.count();
    badge.textContent = n;
    badge.classList.toggle('hidden', n === 0);
  },
  clear() {
    this.save([]);
    this.updateCounter();
    this.renderPage();
  },
  renderPage() {
    const page = $('[data-cart-page]');
    if (!page) return;

    const items = this.getAll();
    const empty = $('[data-cart-empty]');
    const list = $('[data-cart-list]');
    const summary = $('[data-cart-summary]');
    const countText = $('[data-cart-page-count]');
    const summaryCount = $('[data-cart-summary-count]');
    const summarySubtotal = $('[data-cart-summary-subtotal]');
    const summaryTotal = $('[data-cart-summary-total]');

    const count = this.count();
    const total = this.total();
    if (countText) countText.textContent = count === 1 ? '1 producto agregado' : `${count} productos agregados`;

    if (!items.length) {
      empty.hidden = false;
      summary.hidden = true;
      clearNode(list);
      if (countText) countText.textContent = 'Sin productos agregados';
      return;
    }

    empty.hidden = true;
    summary.hidden = false;
    if (summaryCount) summaryCount.textContent = count;
    if (summarySubtotal) summarySubtotal.textContent = fmtCartPrice(total);
    if (summaryTotal) summaryTotal.textContent = fmtCartPrice(total);

    clearNode(list);
    items.forEach(item => {
      const id = String(item.id || '');
      const title = String(item.title || 'Producto').slice(0, 120);
      const price = Number(item.price) || 0;
      const qty = Math.max(1, Number(item.qty) || 1);
      const lineTotal = price * qty;
      const productSlug = safeSlug(item.slug);
      const productUrl = productSlug ? `/productos/${productSlug}` : '/productos';
      const imageUrl = safeHttpUrl(item.img);

      const article = document.createElement('article');
      article.className = 'cart-item';
      article.dataset.cartItem = id;

      const imageLink = document.createElement('a');
      imageLink.className = 'cart-item-img';
      imageLink.href = productUrl;
      if (imageUrl) {
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = title;
        imageLink.appendChild(img);
      } else {
        const placeholder = document.createElement('span');
        placeholder.textContent = 'Sin imagen';
        imageLink.appendChild(placeholder);
      }

      const info = document.createElement('div');
      info.className = 'cart-item-info';
      const titleLink = document.createElement('a');
      titleLink.className = 'cart-item-title';
      titleLink.href = productUrl;
      titleLink.textContent = title;
      const meta = document.createElement('p');
      meta.className = 'cart-item-meta';
      meta.textContent = 'Envío gratis disponible';
      const remove = document.createElement('button');
      remove.className = 'cart-remove';
      remove.type = 'button';
      remove.dataset.cartRemove = id;
      remove.textContent = 'Eliminar';
      info.append(titleLink, meta, remove);

      const actions = document.createElement('div');
      actions.className = 'cart-item-actions';
      const qtyWrap = document.createElement('div');
      qtyWrap.className = 'cart-qty';
      const dec = document.createElement('button');
      dec.type = 'button';
      dec.dataset.cartDec = id;
      dec.setAttribute('aria-label', 'Disminuir cantidad');
      dec.textContent = '−';
      const input = document.createElement('input');
      input.type = 'number';
      input.min = '1';
      input.value = String(qty);
      input.dataset.cartQty = id;
      input.setAttribute('aria-label', 'Cantidad');
      const inc = document.createElement('button');
      inc.type = 'button';
      inc.dataset.cartInc = id;
      inc.setAttribute('aria-label', 'Aumentar cantidad');
      inc.textContent = '+';
      qtyWrap.append(dec, input, inc);
      const totalEl = document.createElement('strong');
      totalEl.textContent = fmtCartPrice(lineTotal);
      actions.append(qtyWrap, totalEl);

      article.append(imageLink, info, actions);
      list.appendChild(article);
    });

    $$('[data-cart-remove]').forEach(btn => btn.addEventListener('click', () => this.remove(btn.dataset.cartRemove)));
    $$('[data-cart-dec]').forEach(btn => btn.addEventListener('click', () => {
      const current = items.find(i => i.id === btn.dataset.cartDec);
      this.updateQty(btn.dataset.cartDec, (Number(current?.qty) || 1) - 1);
    }));
    $$('[data-cart-inc]').forEach(btn => btn.addEventListener('click', () => {
      const current = items.find(i => i.id === btn.dataset.cartInc);
      this.updateQty(btn.dataset.cartInc, (Number(current?.qty) || 1) + 1);
    }));
    $$('[data-cart-qty]').forEach(input => input.addEventListener('change', () => {
      this.updateQty(input.dataset.cartQty, Math.max(1, Number(input.value) || 1));
    }));
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
          clearNode(dropdown);
          matches.slice(0, 8).forEach(match => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.setAttribute('role', 'option');
            const icon = document.createElement('span');
            icon.className = 'icon';
            icon.textContent = '🔍';
            const text = document.createElement('span');
            text.textContent = String(match || '').slice(0, 120);
            item.append(icon, text);
            dropdown.appendChild(item);
          });
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
      const slug  = btn.dataset.slug  || '';
      const scope = btn.closest('.product-main-col, .product-side-col, .product-detail-layout') || document;
      const qtyEl = scope.querySelector('.quantity-section .qty-input');
      Cart.add({ id, title, price, img, slug, qty: qtyEl ? parseInt(qtyEl.value) : 1 });
      if (btn.dataset.cartRedirect === 'true') {
        window.location.href = btn.dataset.cartUrl || '/carrito';
      }
    });
  });
}

function initCartPage() {
  Cart.renderPage();
  $('[data-cart-clear]')?.addEventListener('click', () => {
    Cart.clear();
    showToast('Carrito vaciado');
  });
  $('[data-cart-checkout]')?.addEventListener('click', () => {
    window.location.href = '/checkout';
  });
}

function initCheckoutPage() {
  const page = $('[data-checkout-page]');
  const success = $('[data-order-success]');

  if (success) {
    Cart.clear();
    return;
  }

  if (!page) return;

  const items = Cart.getAll();
  const payload = $('[data-checkout-payload]', page);
  const list = $('[data-checkout-list]', page);
  const countEl = $('[data-checkout-count]', page);
  const subtotalEl = $('[data-checkout-subtotal]', page);
  const totalEl = $('[data-checkout-total]', page);
  const submit = $('[data-checkout-submit]', page);
  const total = Cart.total();
  const count = Cart.count();

  if (payload) payload.value = JSON.stringify(items);
  if (countEl) countEl.textContent = count;
  if (subtotalEl) subtotalEl.textContent = fmtCartPrice(total);
  if (totalEl) totalEl.textContent = fmtCartPrice(total);

  if (!items.length) {
    if (list) {
      clearNode(list);
      const emptyBox = document.createElement('div');
      emptyBox.className = 'checkout-empty';
      const title = document.createElement('strong');
      title.textContent = 'Tu carrito esta vacio';
      const text = document.createElement('span');
      text.textContent = 'Agrega productos antes de crear una orden.';
      emptyBox.append(title, text);
      list.appendChild(emptyBox);
    }
    if (submit) submit.disabled = true;
    return;
  }

  if (list) {
    clearNode(list);
    items.forEach(item => {
      const qty = Number(item.qty) || 1;
      const price = Number(item.price) || 0;
      const article = document.createElement('article');
      article.className = 'checkout-item';
      const title = document.createElement('span');
      title.textContent = String(item.title || 'Producto').slice(0, 120);
      const total = document.createElement('strong');
      total.textContent = `${qty} x ${fmtCartPrice(price)}`;
      article.append(title, total);
      list.appendChild(article);
    });
  }

  $('form.checkout-form', page)?.addEventListener('submit', () => {
    if (payload) payload.value = JSON.stringify(Cart.getAll());
  });
}

function initStoreContactModals() {
  const openers = $$('[data-store-modal-open]');
  if (!openers.length) return;

  const closeModal = modal => {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
  };

  openers.forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = document.getElementById(btn.dataset.storeModalOpen);
      if (!modal) return;
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
      modal.querySelector('[data-store-modal-close]')?.focus();
    });
  });

  $$('[data-store-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.closest('.checkout-modal')));
  });

  document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    closeModal($('.checkout-modal:not([hidden])'));
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

  const close = () => {
    sidebar.classList.remove('mobile-open');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
  };

  toggleBtn.addEventListener('click', () => {
    const isOpen = sidebar.classList.toggle('mobile-open');
    overlay?.classList.toggle('active', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });

  overlay?.addEventListener('click', close);
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
  initCartPage();
  initCheckoutPage();
  initStoreContactModals();
});
