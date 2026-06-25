@extends('layouts.buyer')

@section('content')
<style>
:root {
  --primary: #2563eb;
  --primary-dark: #1d4ed8;
  --accent: #f59e0b;
  --green: #16a34a;
  --bg: #ffffff;
  --bg-alt: #f8fafc;
  --text: #0f172a;
  --text-muted: #64748b;
  --border: #e2e8f0;
  --radius: 12px;
  --shadow: 0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.06);
  --shadow-lg: 0 10px 25px rgba(0,0,0,.1);
  --transition: .25s ease;
}

/* ─── CART LAYOUT ─── */
.cart-page { display: grid; grid-template-columns: 1fr 360px; gap: 40px; padding-bottom: 80px; }
@media (max-width:820px) { .cart-page { grid-template-columns: 1fr; } }

.cart-page h1 { font-size: 1.6rem; font-weight: 700; margin-bottom: 8px; }
.cart-page .cart-count-label { font-size: .9rem; color: var(--text-muted); margin-bottom: 24px; }

/* ─── CART TABLE ─── */
.cart-table { width: 100%; border-collapse: collapse; }
.cart-table thead th {
  text-align: left; font-size: .78rem; font-weight: 600; text-transform: uppercase;
  letter-spacing: .06em; color: var(--text-muted); padding: 12px 16px;
  border-bottom: 2px solid var(--border);
}
.cart-table tbody td {
  padding: 20px 16px; border-bottom: 1px solid var(--border); vertical-align: middle;
}
.cart-table .product-cell { display: flex; align-items: center; gap: 16px; }
.cart-table .product-cell .emoji {
  width: 56px; height: 56px; border-radius: 8px; background: var(--bg-alt);
  display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;
}
.cart-table .product-cell .info .name { font-weight: 600; font-size: .95rem; }
.cart-table .qty-group {
  display: inline-flex; align-items: center; border: 1px solid var(--border);
  border-radius: 8px; overflow: hidden;
}
.cart-table .qty-group button {
  width: 36px; height: 36px; background: var(--bg-alt); font-size: 1rem; font-weight: 600;
  border: none; cursor: pointer; transition: background var(--transition);
}
.cart-table .qty-group button:hover { background: var(--border); }
.cart-table .qty-group span {
  width: 40px; text-align: center; font-weight: 600; font-size: .95rem;
}
.cart-table .price, .cart-table .subtotal { font-weight: 600; }
.cart-table .remove-btn {
  color: var(--text-muted); font-size: .8rem; padding: 6px 12px; border-radius: 6px;
  border: none; cursor: pointer; background: none; transition: all var(--transition);
}
.cart-table .remove-btn:hover { color: #ef4444; background: #fef2f2; }

/* ─── EMPTY ─── */
.cart-empty {
  grid-column: 1 / -1; text-align: center; padding: 80px 0; color: var(--text-muted);
}
.cart-empty .icon { font-size: 3.5rem; margin-bottom: 16px; }
.cart-empty h2 { font-size: 1.4rem; color: var(--text); margin-bottom: 8px; }
.cart-empty p { margin-bottom: 24px; }
.cart-empty .btn {
  display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px;
  border-radius: 50px; font-weight: 600; font-size: .95rem; background: var(--primary);
  color: #fff; text-decoration: none; transition: all var(--transition);
}
.cart-empty .btn:hover { background: var(--primary-dark); transform: translateY(-2px); }

/* ─── ORDER SUMMARY ─── */
.order-summary {
  background: var(--bg-alt); border-radius: var(--radius); padding: 28px;
  height: fit-content; position: sticky; top: 120px;
}
.order-summary h2 {
  font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 16px;
  border-bottom: 1px solid var(--border);
}
.summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: .92rem; }
.summary-row.sub { color: var(--text-muted); }
.summary-row.total {
  font-size: 1.1rem; font-weight: 700; padding-top: 16px; margin-top: 8px;
  border-top: 1px solid var(--border);
}
.checkout-btn {
  width: 100%; padding: 16px; border-radius: 50px; background: var(--text); color: #fff;
  font-weight: 600; font-size: 1rem; margin-top: 24px; border: none; cursor: pointer;
  transition: background var(--transition);
}
.checkout-btn:hover { background: #1e293b; }
.continue-link {
  display: block; text-align: center; margin-top: 16px; font-size: .88rem; color: var(--primary);
  text-decoration: none;
}
.continue-link:hover { text-decoration: underline; }

/* ─── COUPON ─── */
.coupon { display: flex; gap: 8px; margin-top: 20px; }
.coupon input {
  flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;
  font-size: .9rem; outline: none;
}
.coupon input:focus { border-color: var(--primary); }
.coupon button {
  padding: 10px 18px; border-radius: 8px; background: var(--text); color: #fff;
  font-weight: 600; font-size: .85rem; border: none; cursor: pointer; white-space: nowrap;
  transition: background var(--transition);
}
.coupon button:hover { background: #1e293b; }

/* ─── SHIPPING ESTIMATE ─── */
.shipping-estimate { margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border); }
.shipping-estimate h4 { font-size: .85rem; font-weight: 600; margin-bottom: 8px; }
.shipping-estimate select {
  width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;
  font-size: .9rem; outline: none; background: var(--bg);
}

/* ─── TOAST ─── */
.toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: var(--text); color: #fff; padding: 14px 28px; border-radius: 50px; font-weight: 500; font-size: .92rem; opacity: 0; transition: all .35s cubic-bezier(.22,1,.36,1); z-index: 300; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>

<div class="breadcrumb" style="padding-bottom:20px;font-size:.85rem;color:var(--text-muted)">
  <a href="{{ url('/') }}" style="color:inherit;text-decoration:none">Home</a>
  / <span style="color:var(--text)">Cart</span>
</div>

<div class="cart-page" id="cartPage"></div>
<div class="toast" id="toast"></div>

<script>
let cart = JSON.parse(localStorage.getItem('atex_cart') || '[]');

function renderCart() {
  const container = document.getElementById('cartPage');
  if (cart.length === 0) {
    container.innerHTML = `
      <div class="cart-empty">
        <div class="icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet.</p>
        <a href="{{ route('buyer.products.index') }}" class="btn">Browse Products</a>
      </div>
    `;
    return;
  }

  const subtotal = cart.reduce((s, c) => s + parseFloat(c.price) * c.qty, 0);
  const shipping = subtotal >= 100000 ? 0 : 15000;
  const tax = subtotal * 0.075;
  const total = subtotal + shipping + tax;

  container.innerHTML = `
    <div>
      <h1>Shopping Cart</h1>
      <div class="cart-count-label">${cart.reduce((s, c) => s + c.qty, 0)} item${cart.reduce((s, c) => s + c.qty, 0) !== 1 ? 's' : ''}</div>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          ${cart.map(c => `
            <tr>
              <td>
                <div class="product-cell">
                  <div class="emoji">📦</div>
                  <div class="info">
                    <div class="name"><a href="{{ url('/products') }}/${c.id}" style="color:inherit;text-decoration:none">${c.name}</a></div>
                  </div>
                </div>
              </td>
              <td class="price">₦${parseFloat(c.price).toLocaleString()}</td>
              <td>
                <div class="qty-group">
                  <button onclick="changeQty('${c.id}', -1)">−</button>
                  <span>${c.qty}</span>
                  <button onclick="changeQty('${c.id}', 1)">+</button>
                </div>
              </td>
              <td class="subtotal">₦${(parseFloat(c.price) * c.qty).toLocaleString()}</td>
              <td><button class="remove-btn" onclick="removeFromCart('${c.id}')">Remove</button></td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
    <div>
      <div class="order-summary">
        <h2>Order Summary</h2>
        <div class="summary-row sub"><span>Subtotal</span><span>₦${subtotal.toLocaleString()}</span></div>
        <div class="summary-row sub"><span>Shipping</span><span>${shipping === 0 ? 'FREE' : '₦' + shipping.toLocaleString()}</span></div>
        <div class="summary-row sub"><span>Estimated Tax</span><span>₦${tax.toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2})}</span></div>
        <div class="summary-row total"><span>Total</span><span>₦${total.toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2})}</span></div>
        <button class="checkout-btn" onclick="handleCheckout()">Proceed to Checkout</button>
        <a href="{{ route('buyer.products.index') }}" class="continue-link">Continue Shopping</a>
        <div class="shipping-estimate">
          <h4>Shipping to</h4>
          <select id="shippingCountry" onchange="renderCart()">
            <option value="NG">Nigeria</option>
            <option value="US">United States</option>
            <option value="UK">United Kingdom</option>
            <option value="EU">European Union</option>
          </select>
        </div>
        <div class="coupon">
          <input type="text" placeholder="Coupon code" id="couponInput">
          <button onclick="applyCoupon()">Apply</button>
        </div>
      </div>
    </div>
  `;
}

function changeQty(id, delta) {
  const item = cart.find(c => c.id === id);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) { removeFromCart(id); return; }
  updateCartUI();
}

function removeFromCart(id) { cart = cart.filter(c => c.id !== id); updateCartUI(); }

function updateCartUI() {
  const count = cart.reduce((s, c) => s + c.qty, 0);
  localStorage.setItem('atex_cart', JSON.stringify(cart));
  const ce = document.getElementById('cartCount');
  if (ce) { ce.textContent = count; ce.style.opacity = count > 0 ? '1' : '0'; ce.style.transform = count > 0 ? 'scale(1)' : 'scale(.5)'; }
  const label = document.getElementById('cartLabel');
  if (label) label.textContent = count;
  renderCart();
}

function handleCheckout() {
  if (cart.length === 0) { showToast('Cart is empty'); return; }
  const total = cart.reduce((sum, c) => sum + parseFloat(c.price) * c.qty, 0);
  const orderId = 'ORD-' + Date.now().toString(36).toUpperCase();
  const order = {
    id: orderId,
    items: cart.map(c => ({ id: c.id, name: c.name, price: parseFloat(c.price), qty: c.qty })),
    total: parseFloat(total.toFixed(2)),
    status: 'Confirmed',
    date: new Date().toISOString()
  };

  // Save to localStorage
  const orders = JSON.parse(localStorage.getItem('atex_orders') || '[]');
  orders.unshift(order);
  localStorage.setItem('atex_orders', JSON.stringify(orders));

  // Save to backend if logged in
  const items = cart.map(c => ({ product_id: c.id, quantity: c.qty }));
  fetch('{{ route("checkout.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ items, total })
  }).catch(() => {});

  showToast('Order ' + orderId + ' placed — ₦' + total.toLocaleString());
  cart = []; updateCartUI();
  window.location.href = '{{ route("buyer.orders.index") }}#' + orderId;
}

function applyCoupon() {
  const input = document.getElementById('couponInput');
  const code = input.value.trim().toUpperCase();
  if (!code) { showToast('Enter a coupon code'); return; }
  showToast('Coupon applied — 10% discount');
}

function showToast(msg) {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = msg;
  el.classList.add('visible');
  clearTimeout(el._timeout);
  el._timeout = setTimeout(() => el.classList.remove('visible'), 3000);
}

updateCartUI();
</script>
@endsection
