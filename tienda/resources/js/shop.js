'use strict';

const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const fmtCLP = n => '$' + Number(n).toLocaleString('es-CL');
const fmtCartPrice = n => Number(n) > 0 ? fmtCLP(n) : 'Se regala';
const safeSlug = value => /^[a-z0-9-]+$/i.test(String(value || '')) ? String(value) : '';
const appUrl = () => document.querySelector('meta[name="app-url"]')?.content?.replace(/\/$/, '') || '';
const appPath = path => `${appUrl()}${path.startsWith('/') ? path : `/${path}`}`;
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
  getAll() {
    try {
      return JSON.parse(localStorage.getItem(this.KEY)) || [];
    } catch {
      return [];
    }
  },
  save(items) {
    try {
      localStorage.setItem(this.KEY, JSON.stringify(items));
    } catch (e) {
      console.error('Error guardando carrito:', e);
    }
  },
  has(id) {
    const items = this.getAll();
    return items.some(i => String(i.id) === String(id));
  },
  add(product, options = { increment: true, notify: true }) {
    const items = this.getAll();
    const idx = items.findIndex(i => String(i.id) === String(product.id));
    const maxStock = (product.stock !== undefined && product.stock !== null && product.stock !== '')
      ? Number(product.stock)
      : null;

    if (maxStock !== null && maxStock <= 0) {
      showToast('Este producto no tiene stock disponible', 'error');
      return;
    }

    if (idx > -1) {
      if (options.increment) {
        const currentQty = Number(items[idx].qty) || 1;
        const addQty = Number(product.qty) || 1;
        let nextQty = currentQty + addQty;
        if (maxStock !== null && nextQty > maxStock) {
          nextQty = maxStock;
          showToast(`Stock máximo disponible: ${maxStock}`, 'error');
        } else if (options.notify !== false) {
          showToast('Producto agregado al carrito ✓');
        }
        items[idx].qty = nextQty;
        if (maxStock !== null) items[idx].stock = maxStock;
      } else {
        let nextQty = Number(product.qty) || 1;
        if (maxStock !== null && nextQty > maxStock) {
          nextQty = maxStock;
          showToast(`Stock máximo disponible: ${maxStock}`, 'error');
        }

        items[idx] = { ...items[idx], ...product, qty: nextQty, stock: maxStock };
      }
    } else {
      let qty = Number(product.qty) || 1;
      if (maxStock !== null && qty > maxStock) {
        qty = maxStock;
        showToast(`Stock máximo disponible: ${maxStock}`, 'error');
      } else if (options.notify !== false) {
        showToast('Producto agregado al carrito ✓');
      }
      items.push({ ...product, qty, stock: maxStock });
    }
    this.save(items);
    this.updateCounter();
  },
  remove(id) {
    const filtered = this.getAll().filter(i => String(i.id) !== String(id));
    this.save(filtered);
    this.updateCounter();
    this.renderPage();
    showToast('Producto eliminado del carrito');
  },
  updateQty(id, qty) {
    const items = this.getAll();
    const idx = items.findIndex(i => String(i.id) === String(id));
    if (idx > -1) {
      const numQty = Number(qty);
      if (numQty < 1) {
        this.remove(id);
        return;
      }
      const maxStock = items[idx].stock ? Number(items[idx].stock) : null;
      if (maxStock !== null && numQty > maxStock) {
        items[idx].qty = maxStock;
        showToast(`Stock máximo alcanzado (${maxStock} unidades)`, 'error');
      } else {
        items[idx].qty = numQty;
      }
      this.save(items);
    }
    this.updateCounter();
    this.renderPage();
  },
  count() {
    return this.getAll().reduce((s, i) => s + (Number(i.qty) || 1), 0);
  },
  total() {
    return this.getAll().reduce((s, i) => s + (Number(i.price) || 0) * (Number(i.qty) || 1), 0);
  },
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
    showToast('Carrito vaciado');
  },
  async syncStock() {
    const items = this.getAll();
    if (!items.length) return true;
    const ids = [...new Set(items.map(i => i.id).filter(Boolean))];
    if (!ids.length) return true;

    try {
      const res = await fetch(appPath(`/carrito/validar-stock?ids=${ids.join(',')}&_=${Date.now()}`), { cache: 'no-store' });
      if (!res.ok) return false;
      const liveData = await res.json();
      let changed = false;
      const updated = items.map(item => {
        const live = liveData[item.id];
        if (live) {
          const liveStock = live.activo ? Number(live.stock) : 0;
          const livePrice = Number(live.precio);
          let newQty = Number(item.qty) || 1;
          if (liveStock > 0 && newQty > liveStock) {
            newQty = liveStock;
            changed = true;
          }
          if (item.stock !== liveStock || item.price !== livePrice || item.qty !== newQty) {
            changed = true;
            return { ...item, stock: liveStock, price: livePrice, qty: newQty };
          }
        } else {
          if (item.stock !== 0) {
            changed = true;
            return { ...item, stock: 0 };
          }
        }
        return item;
      });

      if (changed) {
        this.save(updated);
        this.updateCounter();
        this.renderPage();
      }

      return true;
    } catch (e) {
      console.warn('No se pudo sincronizar stock en vivo:', e);
      return false;
    }
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
      if (empty) empty.hidden = false;
      if (summary) summary.hidden = true;
      clearNode(list);
      if (countText) countText.textContent = 'Sin productos agregados';
      return;
    }

    if (empty) empty.hidden = true;
    if (summary) summary.hidden = false;
    if (summaryCount) summaryCount.textContent = count;
    if (summarySubtotal) summarySubtotal.textContent = fmtCartPrice(total);
    if (summaryTotal) summaryTotal.textContent = fmtCartPrice(total);

    let hasOutOfStock = false;

    clearNode(list);
    items.forEach(item => {
      const id = String(item.id || '');
      const title = String(item.title || 'Producto').slice(0, 120);
      const price = Number(item.price) || 0;
      const stock = (item.stock !== undefined && item.stock !== null) ? Number(item.stock) : null;
      const isOutOfStock = stock !== null && stock <= 0;
      if (isOutOfStock) hasOutOfStock = true;

      const qty = Math.max(1, Number(item.qty) || 1);
      const lineTotal = price * qty;
      const productSlug = safeSlug(item.slug);
      const productUrl = productSlug ? appPath(`/productos/${productSlug}`) : appPath('/productos');
      const imageUrl = safeHttpUrl(item.img);

      const article = document.createElement('article');
      article.className = 'cart-item';
      if (isOutOfStock) {
        article.style.borderColor = '#fca5a5';
        article.style.backgroundColor = '#fffafb';
      }
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
      if (isOutOfStock) {
        meta.textContent = '🚫 Agotado (0 unidades disponibles)';
        meta.style.color = '#dc2626';
        meta.style.fontWeight = '600';
      } else if (stock !== null) {
        meta.textContent = `Stock disponible: ${stock} unid.`;
        meta.style.color = '';
        meta.style.fontWeight = '';
      } else {
        meta.textContent = 'Envío gratis disponible';
      }

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
      if (stock !== null && stock > 0) input.max = String(stock);
      input.value = String(qty);
      input.dataset.cartQty = id;
      input.setAttribute('aria-label', 'Cantidad');
      const inc = document.createElement('button');
      inc.type = 'button';
      inc.dataset.cartInc = id;
      inc.setAttribute('aria-label', 'Aumentar cantidad');
      inc.textContent = '+';

      if (isOutOfStock) {
        inc.disabled = true;
        inc.style.opacity = '0.3';
        inc.style.cursor = 'not-allowed';
        dec.disabled = true;
        dec.style.opacity = '0.3';
        dec.style.cursor = 'not-allowed';
        input.disabled = true;
      }

      qtyWrap.append(dec, input, inc);
      const totalEl = document.createElement('strong');
      totalEl.textContent = isOutOfStock ? 'Sin stock' : fmtCartPrice(lineTotal);
      if (isOutOfStock) totalEl.style.color = '#dc2626';
      actions.append(qtyWrap, totalEl);

      article.append(imageLink, info, actions);
      list.appendChild(article);
    });

    const checkoutBtn = $('[data-cart-checkout]');
    if (checkoutBtn) {
      if (hasOutOfStock) {
        checkoutBtn.disabled = true;
        checkoutBtn.style.opacity = '0.5';
        checkoutBtn.style.cursor = 'not-allowed';
        checkoutBtn.title = 'Elimina los productos sin stock antes de continuar';
      } else {
        checkoutBtn.disabled = false;
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.cursor = 'pointer';
        checkoutBtn.title = '';
      }
    }

    $$('[data-cart-remove]').forEach(btn => btn.addEventListener('click', () => this.remove(btn.dataset.cartRemove)));
    $$('[data-cart-dec]').forEach(btn => btn.addEventListener('click', () => {
      const current = items.find(i => String(i.id) === String(btn.dataset.cartDec));
      const currentQty = Number(current?.qty) || 1;
      this.updateQty(btn.dataset.cartDec, currentQty - 1);
    }));
    $$('[data-cart-inc]').forEach(btn => btn.addEventListener('click', () => {
      const current = items.find(i => String(i.id) === String(btn.dataset.cartInc));
      const currentQty = Number(current?.qty) || 1;
      this.updateQty(btn.dataset.cartInc, currentQty + 1);
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

      fetch(appPath(`/buscar/sugerencias?q=${encodeURIComponent(q)}`))
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
      const stock = btn.dataset.stock ? parseInt(btn.dataset.stock) : null;
      const scope = btn.closest('.product-main-col, .product-side-col, .product-detail-layout') || document;
      const qtyEl = scope.querySelector('.quantity-section .qty-input');
      const isRedirect = btn.dataset.cartRedirect === 'true';
      const cartProduct = { id, title, price, img, slug, stock, qty: qtyEl ? parseInt(qtyEl.value) : 1 };

      if (isRedirect) {
        Cart.add(cartProduct, { increment: false, notify: false });
        window.location.href = btn.dataset.cartUrl || '/carrito';
      } else {
        Cart.add(cartProduct, { increment: true, notify: true });
      }
    });
  });
}

function initCartPage() {
  const page = $('[data-cart-page]');
  if (!page) return;
  Cart.renderPage();
  Cart.syncStock();
  $('[data-cart-clear]')?.addEventListener('click', () => {
    Cart.clear();
  });
  $('[data-cart-checkout]')?.addEventListener('click', async () => {
    const checkoutBtn = $('[data-cart-checkout]');
    const originalText = checkoutBtn?.textContent || 'Continuar compra';
    if (checkoutBtn) {
      checkoutBtn.disabled = true;
      checkoutBtn.textContent = 'Validando stock...';
    }

    const synced = await Cart.syncStock();
    const items = Cart.getAll();
    const hasStockProblem = items.some(item => {
      const stock = (item.stock !== undefined && item.stock !== null) ? Number(item.stock) : null;
      const qty = Math.max(1, Number(item.qty) || 1);
      return stock !== null && (stock <= 0 || qty > stock);
    });

    if (checkoutBtn) checkoutBtn.textContent = originalText;

    if (!synced) {
      Cart.renderPage();
      showToast('No pudimos validar el stock. Intenta nuevamente.', 'error');
      return;
    }

    if (!items.length) {
      Cart.renderPage();
      showToast('Tu carrito está vacío', 'error');
      return;
    }

    if (hasStockProblem) {
      Cart.renderPage();
      showToast('Revisa los productos sin stock antes de continuar.', 'error');
      return;
    }

    window.location.href = appPath('/checkout');
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

  Cart.syncStock().then(() => {
    const items = Cart.getAll();
    const payload = $('[data-checkout-payload]', page);
    if (payload) payload.value = JSON.stringify(items);
  });

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
  Cart.syncStock();
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
