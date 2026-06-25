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
  --shadow: 0 1px 3px rgba(0,0,0,.08);
  --shadow-lg: 0 10px 25px rgba(0,0,0,.1);
  --transition: .25s ease;
}

.page-header { padding: 8px 0 24px; }
.page-header h1 { font-size: 1.5rem; font-weight: 700; }
.page-header .sub { color: var(--text-muted); font-size: .9rem; }

.orders-list { padding-bottom: 80px; }
.order-card { background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 16px; overflow: hidden; transition: box-shadow var(--transition); }
.order-card:hover { box-shadow: var(--shadow-lg); }
.order-card .head { padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 8px; }
.order-card .head .id { font-weight: 700; font-size: .95rem; }
.order-card .head .date { font-size: .82rem; color: var(--text-muted); }
.order-card .head .status { font-size: .78rem; font-weight: 600; padding: 4px 12px; border-radius: 50px; }
.order-card .head .status.confirmed { background: #d1fae5; color: #065f46; }
.order-card .head .status.shipped { background: #dbeafe; color: #1e40af; }
.order-card .head .status.delivered { background: #f3e8ff; color: #6b21a8; }
.order-card .body { padding: 16px 20px; }
.order-card .body .item { display: flex; align-items: center; gap: 12px; padding: 8px 0; }
.order-card .body .item + .item { border-top: 1px solid var(--border); }
.order-card .body .item .emoji { font-size: 1.4rem; width: 36px; text-align: center; }
.order-card .body .item .name { flex: 1; font-size: .9rem; font-weight: 500; }
.order-card .body .item .meta { font-size: .85rem; color: var(--text-muted); white-space: nowrap; }
.order-card .foot { padding: 14px 20px; background: var(--bg-alt); border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.order-card .foot .total { font-weight: 700; font-size: 1.05rem; }
.order-card .foot .reorder-btn { padding: 6px 16px; border-radius: 6px; background: var(--text); color: #fff; font-size: .82rem; font-weight: 600; border: none; cursor: pointer; transition: background var(--transition); }
.order-card .foot .reorder-btn:hover { opacity: .9; }
.order-card .foot .receipt-btn { padding: 6px 14px; border-radius: 6px; border: 1px solid var(--border); font-size: .82rem; font-weight: 500; color: var(--text-muted); background: none; cursor: pointer; transition: all var(--transition); margin-right: 8px; }
.order-card .foot .receipt-btn:hover { border-color: var(--text); color: var(--text); }

.receipt-overlay { display: none; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 16px; overflow: hidden; box-shadow: var(--shadow-lg); }
.receipt-overlay.open { display: block; }
.receipt-overlay .inner { padding: 32px; }
.receipt-overlay .close-bar { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
.receipt-overlay .close-bar .close-btn { font-size: .85rem; font-weight: 600; color: var(--text-muted); padding: 4px 12px; border-radius: 4px; border: none; cursor: pointer; background: none; transition: background var(--transition); }
.receipt-overlay .close-bar .close-btn:hover { background: var(--border); color: var(--text); }
.receipt-overlay .status-bar { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; font-size: .9rem; }
.receipt-overlay .status-bar.confirmed { background: #d1fae5; color: #065f46; }
.receipt-overlay .status-bar.shipped { background: #dbeafe; color: #1e40af; }
.receipt-overlay .status-bar.delivered { background: #f3e8ff; color: #6b21a8; }
.receipt-overlay .head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px dashed var(--border); }
.receipt-overlay .head .ref { text-align: right; }
.receipt-overlay .head .ref .id { font-weight: 700; font-size: 1rem; }
.receipt-overlay .head .ref .date { font-size: .82rem; color: var(--text-muted); margin-top: 2px; }
.receipt-overlay .section-title { font-size: .8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 10px; }
.receipt-overlay .item-row { display: flex; align-items: center; gap: 12px; padding: 8px 0; }
.receipt-overlay .item-row + .item-row { border-top: 1px solid var(--border); }
.receipt-overlay .item-row .emoji { font-size: 1.3rem; width: 32px; text-align: center; }
.receipt-overlay .item-row .name { flex: 1; font-size: .9rem; font-weight: 500; }
.receipt-overlay .item-row .qty { font-size: .85rem; color: var(--text-muted); }
.receipt-overlay .item-row .price { font-weight: 600; font-size: .9rem; white-space: nowrap; }
.receipt-overlay .totals { margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border); }
.receipt-overlay .totals .row { display: flex; justify-content: space-between; padding: 3px 0; font-size: .88rem; }
.receipt-overlay .totals .row .label { color: var(--text-muted); }
.receipt-overlay .totals .row.grand { border-top: 2px solid var(--border); margin-top: 6px; padding-top: 10px; font-size: 1.05rem; font-weight: 700; }
.receipt-overlay .totals .row.grand .label { color: var(--text); }
.receipt-overlay .foot-actions { display: flex; gap: 12px; justify-content: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); }
.receipt-overlay .foot-actions .btn { padding: 10px 24px; border-radius: 6px; font-size: .85rem; font-weight: 600; border: none; cursor: pointer; transition: opacity var(--transition); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.receipt-overlay .foot-actions .btn.primary { background: #febd69; color: #131921; }
.receipt-overlay .foot-actions .btn.primary:hover { background: #f3a847; }
.receipt-overlay .foot-actions .btn.outline { border: 1px solid var(--border); color: var(--text); background: none; }
.receipt-overlay .foot-actions .btn.outline:hover { background: var(--bg-alt); }

.receipt-overlay .letterhead { display: none; }

.empty-state { text-align: center; padding: 80px 24px; }
.empty-state .icon { font-size: 3rem; margin-bottom: 16px; }
.empty-state h2 { font-size: 1.3rem; }
.empty-state p { color: var(--text-muted); margin: 8px 0 24px; }
.empty-state .btn { display: inline-block; padding: 12px 28px; background: #febd69; border-radius: 8px; font-weight: 700; color: #131921; text-decoration: none; }
.empty-state .btn:hover { background: #f3a847; }

.toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #131921; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: .9rem; opacity: 0; transition: all .3s; z-index: 999; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }

@media print {
  body { background: #fff; }
  header, .page-header, .order-card, .toast, .receipt-overlay .close-bar, .receipt-overlay .foot-actions, .orders-list { display: none !important; }
  .receipt-overlay { display: block !important; border: none !important; box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; }
  .receipt-overlay .inner { padding: 0 !important; }
  .receipt-overlay .letterhead { display: flex !important; align-items: center; gap: 16px; padding-bottom: 20px; margin-bottom: 24px; border-bottom: 3px solid #131921; }
  .receipt-overlay .letterhead .logo { width: 50px; height: 50px; background: #131921; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; color: #febd69; flex-shrink: 0; }
  .receipt-overlay .letterhead .info h2 { font-size: 1.2rem; font-weight: 800; color: #131921; }
  .receipt-overlay .letterhead .info p { font-size: .75rem; color: #64748b; margin-top: 2px; }
  .receipt-overlay .head { margin-bottom: 16px; padding-bottom: 14px; }
  .receipt-overlay .status-bar { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
  .receipt-overlay .status-bar.confirmed { background: #d1fae5 !important; }
  .receipt-overlay .status-bar.shipped { background: #dbeafe !important; }
  .receipt-overlay .status-bar.delivered { background: #f3e8ff !important; }
  @page { margin: 20mm; }
}
</style>

<div class="page-header">
  <h1>📦 My Orders</h1>
  <p class="sub">View and track your import & export orders</p>
</div>

<div class="orders-list" id="ordersList"></div>

<div class="receipt-overlay" id="receiptOverlay">
  <div class="close-bar">
    <span style="font-weight:600;font-size:.9rem">📄 Order Receipt</span>
    <button class="close-btn" onclick="closeReceipt()">✕ Close</button>
  </div>
  <div class="inner">
    <div class="letterhead">
      <div class="logo">A<span style="color:#febd69">E</span></div>
      <div class="info">
        <h2>Adamawa Export Platform</h2>
        <p>123 Trade Center, Yola, Adamawa State, Nigeria<br>admin@adamawaexport.com · +234 800 123 4567</p>
      </div>
    </div>
    <div class="status-bar confirmed" id="receiptStatus">✅ Order Confirmed</div>
    <div class="head">
      <div><h2 style="font-size:1.1rem;font-weight:700">Order Receipt</h2></div>
      <div class="ref">
        <div class="id" id="receiptId">ORD-XXXXX</div>
        <div class="date" id="receiptDate"></div>
      </div>
    </div>
    <div class="section-title">Items Ordered</div>
    <div id="receiptItems"></div>
    <div class="totals" id="receiptTotals"></div>
    <div class="foot-actions">
      <button class="btn outline" onclick="window.print()">🖨️ Print</button>
      <a href="{{ route('buyer.products.index') }}" class="btn primary">Continue Shopping →</a>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
function showToast(msg) { const t = document.getElementById('toast'); t.textContent = msg; t.classList.add('visible'); setTimeout(() => t.classList.remove('visible'), 2500); }

function formatDate(iso) {
  const d = new Date(iso);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function renderOrders() {
  const list = document.getElementById('ordersList');
  const orders = JSON.parse(localStorage.getItem('atex_orders') || '[]');

  if (orders.length === 0) {
    list.innerHTML = '<div class="empty-state"><div class="icon">📋</div><h2>No orders yet</h2><p>Your import and export orders will appear here once you place one.</p><a href="{{ route('buyer.products.index') }}" class="btn">Browse Products →</a></div>';
    return;
  }

  list.innerHTML = orders.map(o => {
    const itemsHtml = (o.items || []).map(i => `
      <div class="item">
        <span class="emoji">${i.emoji || '📦'}</span>
        <span class="name">${i.name}</span>
        <span class="meta">${i.price ? '₦' + parseFloat(i.price).toLocaleString() + ' × ' + i.qty : ''}</span>
      </div>
    `).join('');

    const statusClass = (o.status || 'Confirmed').toLowerCase();

    return `
      <div class="order-card">
        <div class="head">
          <div>
            <div class="id">${o.id}</div>
            <div class="date">${formatDate(o.date)}</div>
          </div>
          <span class="status ${statusClass}">${o.status || 'Confirmed'}</span>
        </div>
        <div class="body">${itemsHtml}</div>
        <div class="foot">
          <span class="total">₦${parseFloat(o.total || 0).toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2})}</span>
          <span>
            <button class="receipt-btn" onclick="viewReceipt('${o.id}')">📄 Receipt</button>
            <button class="reorder-btn" onclick="reorder('${o.id}')">Reorder</button>
          </span>
        </div>
      </div>
    `;
  }).join('');
}

function viewReceipt(orderId) {
  const orders = JSON.parse(localStorage.getItem('atex_orders') || '[]');
  const order = orders.find(o => o.id === orderId);
  if (!order) return;

  document.getElementById('receiptId').textContent = order.id;
  document.getElementById('receiptDate').textContent = formatDate(order.date);

  const sb = document.getElementById('receiptStatus');
  const s = (order.status || 'Confirmed').toLowerCase();
  sb.innerHTML = s === 'confirmed' ? '✅ Order Confirmed' : s === 'shipped' ? '🚚 Shipped' : s === 'delivered' ? '📦 Delivered' : '✅ ' + order.status;
  sb.className = 'status-bar ' + s;

  document.getElementById('receiptItems').innerHTML = (order.items || []).map(i =>
    '<div class="item-row"><span class="emoji">' + (i.emoji || '📦') + '</span><span class="name">' + i.name + '</span><span class="qty">×' + i.qty + '</span><span class="price">₦' + (parseFloat(i.price) * i.qty).toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2}) + '</span></div>'
  ).join('');

  const sub = (order.items || []).reduce((s, i) => s + parseFloat(i.price || 0) * (i.qty || 0), 0);
  const tax = sub * 0.05;
  document.getElementById('receiptTotals').innerHTML =
    '<div class="row"><span class="label">Subtotal</span><span>₦' + sub.toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2}) + '</span></div>' +
    '<div class="row"><span class="label">Tax (5%)</span><span>₦' + tax.toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2}) + '</span></div>' +
    '<div class="row"><span class="label">Shipping</span><span>Free</span></div>' +
    '<div class="row grand"><span class="label">Total</span><span>₦' + (sub + tax).toLocaleString(undefined, {minimumFractionDigits:2,maximumFractionDigits:2}) + '</span></div>';

  document.getElementById('receiptOverlay').classList.add('open');
  document.getElementById('receiptOverlay').scrollIntoView({ behavior: 'smooth' });
}

function closeReceipt() {
  document.getElementById('receiptOverlay').classList.remove('open');
}

function reorder(orderId) {
  const orders = JSON.parse(localStorage.getItem('atex_orders') || '[]');
  const order = orders.find(o => o.id === orderId);
  if (!order) return;
  const cart = JSON.parse(localStorage.getItem('atex_cart') || '[]');
  (order.items || []).forEach(i => {
    const existing = cart.find(c => c.id === i.id);
    if (existing) { existing.qty += i.qty || 1; }
    else { cart.push({ id: i.id, name: i.name, price: parseFloat(i.price), qty: i.qty || 1 }); }
  });
  localStorage.setItem('atex_cart', JSON.stringify(cart));
  updateCartUI();
  showToast('Items added to cart');
}

renderOrders();

const hash = window.location.hash.slice(1);
if (hash) setTimeout(() => viewReceipt(hash), 100);
</script>
@endsection
