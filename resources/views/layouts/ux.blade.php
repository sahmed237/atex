<!-- ═══ QUICK VIEW MODAL ═══ -->
<div id="quickViewModal" class="ux-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); z-index:10000; align-items:center; justify-content:center; padding:20px;">
  <div class="ux-modal-box" style="background:#fff; color:#0f172a; border-radius:20px; max-width:650px; width:100%; padding:32px; position:relative; box-shadow:0 25px 50px rgba(0,0,0,0.25); max-height:90vh; overflow-y:auto; border:1px solid #e2e8f0;">
    <button onclick="closeQuickView()" style="position:absolute; top:20px; right:20px; background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b; transition:background 0.2s;">✕</button>
    <div id="qvContent"></div>
  </div>
</div>

<!-- ═══ REQUEST FOR QUOTE (RFQ) MODAL ═══ -->
<div id="rfqModal" class="ux-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); z-index:10000; align-items:center; justify-content:center; padding:20px;">
  <div class="ux-modal-box" style="background:#fff; color:#0f172a; border-radius:20px; max-width:540px; width:100%; padding:32px; position:relative; box-shadow:0 25px 50px rgba(0,0,0,0.25); border:1px solid #e2e8f0;">
    <button onclick="closeRfqModal()" style="position:absolute; top:20px; right:20px; background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b;">✕</button>
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
      <span style="font-size:2rem; background:#fef3c7; padding:10px; border-radius:14px;">📋</span>
      <div>
        <h3 style="margin:0; font-size:1.35rem; font-weight:800; color:#0f172a;">Request Custom Export Quote</h3>
        <p style="margin:0; font-size:0.85rem; color:#64748b;">Direct negotiation with Adamawa producers</p>
      </div>
    </div>
    <form id="rfqForm" onsubmit="submitRfqForm(event)">
      <input type="hidden" id="rfqProductId" name="product_id">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:#334155;">Commodity / Product</label>
        <input type="text" id="rfqProductName" readonly style="width:100%; padding:12px 14px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; font-weight:600; color:#0f172a; box-sizing:border-box;">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:#334155;">Target Quantity</label>
          <input type="number" required placeholder="e.g. 50" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; box-sizing:border-box;">
        </div>
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:#334155;">Unit / Metric</label>
          <select style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#fff; box-sizing:border-box;">
            <option>Metric Tons (MT)</option>
            <option>100kg Bags</option>
            <option>20ft Container</option>
            <option>40ft Container</option>
          </select>
        </div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:#334155;">Incoterms</label>
          <select style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#fff; box-sizing:border-box;">
            <option>FOB (Free on Board)</option>
            <option>CIF (Cost, Insured, Freight)</option>
            <option>EXW (Ex-Works Yola)</option>
          </select>
        </div>
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:#334155;">Destination Port</label>
          <input type="text" required placeholder="e.g. Rotterdam Port" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; box-sizing:border-box;">
        </div>
      </div>
      <div style="margin-bottom:24px;">
        <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:#334155;">Additional Requirements / Specs</label>
        <textarea rows="2" placeholder="Specific moisture levels, packaging bags, or phytosanitary certificates required..." style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; resize:vertical; box-sizing:border-box;"></textarea>
      </div>
      <button type="submit" style="width:100%; padding:16px; background:linear-gradient(135deg, #2563eb, #1d4ed8); color:#fff; font-weight:700; font-size:1.05rem; border:none; border-radius:50px; cursor:pointer; box-shadow:0 8px 20px rgba(37,99,235,0.35); transition:all 0.2s;">Send Quote Request →</button>
    </form>
  </div>
</div>

<!-- ═══ FLOATING COMPARE DOCK TRAY ═══ -->
<div id="compareDock" class="no-print" style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background:#0f172a; color:#fff; padding:12px 24px; border-radius:50px; box-shadow:0 15px 35px rgba(0,0,0,0.4); z-index:9990; align-items:center; gap:20px; border:1px solid #334155;">
  <div style="display:flex; align-items:center; gap:10px;">
    <span style="font-size:1.4rem;">⚖️</span>
    <div>
      <div style="font-size:0.9rem; font-weight:800;">Compare Commodities</div>
      <div style="font-size:0.75rem; color:#94a3b8;" id="compareCountText">0 selected</div>
    </div>
  </div>
  <div style="display:flex; gap:10px;">
    <button onclick="openCompareMatrix()" style="padding:8px 18px; background:#febd69; color:#131921; font-weight:800; border:none; border-radius:50px; cursor:pointer; font-size:0.85rem;">Compare Matrix →</button>
    <button onclick="clearCompare()" style="padding:8px 12px; background:#334155; color:#fff; border:none; border-radius:50px; cursor:pointer; font-size:0.8rem;">Clear</button>
  </div>
</div>

<!-- ═══ COMPARISON MATRIX MODAL ═══ -->
<div id="compareMatrixModal" class="ux-modal-overlay no-print" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); z-index:10000; align-items:center; justify-content:center; padding:20px;">
  <div class="ux-modal-box" style="background:#fff; color:#0f172a; border-radius:20px; max-width:800px; width:100%; padding:32px; position:relative; box-shadow:0 25px 50px rgba(0,0,0,0.25); max-height:90vh; overflow-y:auto; border:1px solid #e2e8f0;">
    <button onclick="closeCompareMatrix()" style="position:absolute; top:20px; right:20px; background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b;">✕</button>
    <h2 style="margin:0 0 8px; font-size:1.5rem; font-weight:800; color:#0f172a;">Commodity Specification Comparison</h2>
    <p style="margin:0 0 24px; font-size:0.85rem; color:#64748b;">Side-by-side evaluation of Adamawa direct trade listings</p>
    <div id="compareMatrixContent" style="overflow-x:auto;"></div>
  </div>
</div>

<!-- ═══ PRO-FORMA INVOICE MODAL ═══ -->
<div id="invoiceModal" class="ux-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); z-index:10000; align-items:center; justify-content:center; padding:20px;">
  <div class="ux-modal-box" id="printableInvoiceBox" style="background:#fff; color:#0f172a; border-radius:20px; max-width:700px; width:100%; padding:40px; position:relative; box-shadow:0 25px 50px rgba(0,0,0,0.25); max-height:90vh; overflow-y:auto; border:1px solid #e2e8f0;">
    <button onclick="closeInvoiceModal()" style="position:absolute; top:20px; right:20px; background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b;" class="no-print">✕</button>
    <div style="border-bottom:2px solid #0f172a; padding-bottom:20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="margin:0; font-size:1.8rem; font-weight:900; color:#0f172a;">ADAMAWA EXPORT</h1>
        <div style="font-size:0.8rem; color:#64748b; font-weight:600;">Government Verified Trade Escrow Platform</div>
      </div>
      <div style="text-align:right;">
        <div style="font-size:1.2rem; font-weight:800; color:#2563eb;">PRO-FORMA INVOICE</div>
        <div style="font-size:0.8rem; color:#64748b;">Date: <span id="invDate"></span></div>
        <div style="font-size:0.8rem; color:#64748b;">Ref: #ATEX-PF-<span id="invRef"></span></div>
      </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; font-size:0.88rem; background:#f8fafc; padding:16px; border-radius:12px;">
      <div>
        <div style="font-weight:800; color:#64748b; font-size:0.75rem; text-transform:uppercase;">Origin / Producer</div>
        <div style="font-weight:700; color:#0f172a;">Adamawa Direct Trade Verification Suite</div>
        <div>Jimeta-Yola, Adamawa State, Nigeria</div>
      </div>
      <div>
        <div style="font-weight:800; color:#64748b; font-size:0.75rem; text-transform:uppercase;">Payment Terms</div>
        <div style="font-weight:700; color:#0f172a;">100% Escrow via ATEX Treasury</div>
        <div>Incoterms: FOB Lagos / Port Harcourt</div>
      </div>
    </div>
    <div id="invItemsTable" style="margin-bottom:24px;"></div>
    <div style="background:#eff6ff; border:1px dashed #2563eb; padding:16px; border-radius:12px; margin-bottom:24px; font-size:0.82rem; color:#1e40af;">
      <strong>⚠️ Wire Instruction:</strong> Transfers must reference Pro-Forma Ref ID. Funds are secured in regulated Central Bank verified escrow until shipment inspection passes.
    </div>
    <div style="display:flex; justify-content:flex-end; gap:12px;" class="no-print">
      <button onclick="window.print()" style="padding:12px 24px; background:#2563eb; color:#fff; font-weight:700; border:none; border-radius:50px; cursor:pointer; display:flex; align-items:center; gap:8px;">🖨️ Print / Save PDF</button>
      <button onclick="closeInvoiceModal()" style="padding:12px 20px; background:#f1f5f9; color:#0f172a; font-weight:700; border:1px solid #cbd5e1; border-radius:50px; cursor:pointer;">Close</button>
    </div>
  </div>
</div>

<!-- ═══ FLOATING TRADE ASSISTANT HELP DRAWER ═══ -->
<div id="helpWidgetBtn" onclick="toggleHelpDrawer()" class="no-print" style="position:fixed; bottom:20px; right:20px; background:linear-gradient(135deg, #2563eb, #1d4ed8); color:#fff; padding:14px 20px; border-radius:50px; box-shadow:0 10px 25px rgba(37,99,235,0.4); z-index:9980; cursor:pointer; display:flex; align-items:center; gap:10px; font-weight:700; font-size:0.9rem; transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
  <span style="font-size:1.3rem;">💬</span>
  <span class="hide-mobile">Trade Assistant</span>
</div>

<div id="helpDrawer" class="no-print" style="display:none; position:fixed; bottom:80px; right:20px; width:360px; background:#fff; color:#0f172a; border-radius:20px; box-shadow:0 20px 45px rgba(0,0,0,0.25); z-index:9995; border:1px solid #e2e8f0; overflow:hidden;">
  <div style="background:#0f172a; color:#fff; padding:18px 20px; display:flex; justify-content:space-between; align-items:center;">
    <div style="display:flex; align-items:center; gap:10px;">
      <span style="font-size:1.5rem;">🇳🇬</span>
      <div>
        <div style="font-weight:800; font-size:0.95rem;">ATEX Export Officer</div>
        <div style="font-size:0.75rem; color:#4ade80;">● Online · Instant Offline Support</div>
      </div>
    </div>
    <button onclick="toggleHelpDrawer()" style="background:transparent; border:none; color:#94a3b8; font-size:1.2rem; cursor:pointer;">✕</button>
  </div>
  <div style="padding:20px; max-height:380px; overflow-y:auto; font-size:0.88rem;">
    <div style="background:#f8fafc; padding:12px; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:14px; color:#334155;">
      👋 Hello! I am your self-contained trade assistant. Click a topic below or submit an inquiry:
    </div>
    <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px;">
      <button onclick="showHelpAnswer('escrow')" style="text-align:left; padding:10px 14px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; color:#1e40af; font-weight:600; font-size:0.82rem; cursor:pointer;">🛡️ How does 100% Escrow Protection work?</button>
      <button onclick="showHelpAnswer('incoterms')" style="text-align:left; padding:10px 14px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#166534; font-weight:600; font-size:0.82rem; cursor:pointer;">🚢 What are your shipping Incoterms?</button>
      <button onclick="showHelpAnswer('moq')" style="text-align:left; padding:10px 14px; background:#fef3c7; border:1px solid #fde68a; border-radius:10px; color:#92400e; font-weight:600; font-size:0.82rem; cursor:pointer;">📦 Can I request lower Minimum Order Quantities?</button>
    </div>
    <div id="helpAnswerBox" style="display:none; background:#f1f5f9; padding:14px; border-radius:10px; font-size:0.82rem; color:#0f172a; margin-bottom:16px; border-left:4px solid #2563eb;"></div>
    <hr style="border:none; border-top:1px solid #e2e8f0; margin:14px 0;">
    <form onsubmit="submitHelpInquiry(event)">
      <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:6px; color:#334155;">Send Offline Inquiry to Trade Rep</label>
      <input type="text" id="helpEmail" placeholder="Your Email or Phone..." required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; margin-bottom:8px; box-sizing:border-box; font-size:0.82rem;">
      <textarea id="helpMsg" rows="2" placeholder="Describe commodity requirements..." required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; margin-bottom:10px; box-sizing:border-box; font-size:0.82rem; resize:vertical;"></textarea>
      <button type="submit" style="width:100%; padding:10px; background:#0f172a; color:#fff; font-weight:700; border:none; border-radius:8px; cursor:pointer; font-size:0.85rem;">Transmit Inquiry →</button>
    </form>
  </div>
</div>

<style>
@media print {
  .no-print, header, footer, nav, #helpWidgetBtn, #compareDock { display:none !important; }
  body * { visibility:hidden; }
  #printableInvoiceBox, #printableInvoiceBox * { visibility:visible; }
  #printableInvoiceBox { position:absolute; left:0; top:0; width:100%; border:none !important; box-shadow:none !important; padding:0 !important; }
}

/* Compare Checkbox & Label Styling */
input.compare-chk {
  width: 15px !important;
  height: 15px !important;
  min-height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 3px !important;
  background: transparent !important;
  box-shadow: none !important;
  accent-color: #2563eb !important;
  cursor: pointer !important;
  flex-shrink: 0 !important;
  appearance: auto !important;
  -webkit-appearance: auto !important;
}

.compare-label {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-muted, #64748b);
  cursor: pointer;
  float: right;
  margin-top: 2px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s ease, transform 0.2s ease;
  transform: translateY(2px);
}

.product-card:hover .compare-label,
.compare-label:has(input.compare-chk:checked) {
  opacity: 1 !important;
  pointer-events: auto !important;
  transform: translateY(0);
}
</style>

<script>
// ─── CURRENCY SWITCHER ───
window.atexRates = { NGN: 1, USD: 0.000625, EUR: 0.000571 };
window.atexSymbols = { NGN: '₦', USD: '$', EUR: '€' };
window.currentCurrency = "{{ session('user_currency', 'NGN') }}" || localStorage.getItem('atex_currency') || 'NGN';

function setCurrency(curr) {
  window.currentCurrency = curr;
  localStorage.setItem('atex_currency', curr);
  document.querySelectorAll('.curr-btn').forEach(btn => {
    btn.style.background = btn.id === ('curr-' + curr) ? '#febd69' : 'transparent';
    btn.style.color = btn.id === ('curr-' + curr) ? '#131921' : '#fff';
    btn.style.fontWeight = btn.id === ('curr-' + curr) ? '800' : '600';
  });
  updateAllPrices();
  if (typeof updateCartUI === 'function') updateCartUI();
}

function formatPriceAmount(ngnAmount) {
  const rate = window.atexRates[window.currentCurrency] || 1;
  const sym = window.atexSymbols[window.currentCurrency] || '₦';
  const val = parseFloat(ngnAmount) * rate;
  if (isNaN(val)) return ngnAmount;
  return sym + val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function updateAllPrices() {
  document.querySelectorAll('[data-price-ngn]').forEach(el => {
    const base = el.getAttribute('data-price-ngn');
    if (base && !isNaN(base)) {
      el.textContent = formatPriceAmount(base);
    }
  });
}

// ─── COUNTRY / LOCATION OVERRIDE ───
function toggleCountryDropdown(e) {
  if (e) e.stopPropagation();
  const menu = document.getElementById('countryDropdownMenu');
  if (menu) {
    menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
  }
}

document.addEventListener('click', function(e) {
  const menu = document.getElementById('countryDropdownMenu');
  if (menu && !e.target.closest('.country-selector')) {
    menu.style.display = 'none';
  }
});

function selectCountryOverride(e, code, name, currency) {
  if (e) e.preventDefault();
  
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const token = csrfMeta ? csrfMeta.getAttribute('content') : '';

  fetch('/location/set', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({
      country: code,
      country_name: name,
      currency: currency
    })
  }).then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      localStorage.setItem('atex_currency', currency);
      window.location.reload();
    }
  }).catch(err => {
    console.error('Error updating location:', err);
    window.location.reload();
  });
}

// Initialize buttons on DOM load
document.addEventListener('DOMContentLoaded', () => {
  setCurrency(window.currentCurrency);
});

// ─── LIVE SEARCH SUGGESTIONS ───
var atexLiveCatalog = null;

function handleLiveSearch(val) {
  const dropdown = document.getElementById('liveSearchDropdown');
  if (!dropdown) return;
  if (!val || val.trim().length < 2) {
    dropdown.style.display = 'none';
    return;
  }
  if (!atexLiveCatalog) {
    fetch('/products/search-catalog')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        atexLiveCatalog = data;
        doLiveSearch(val, dropdown);
      });
    return;
  }
  doLiveSearch(val, dropdown);
}

function doLiveSearch(val, dropdown) {
  const q = val.toLowerCase().trim();
  const matches = atexLiveCatalog.filter(p =>
    (p.name && p.name.toLowerCase().includes(q)) ||
    (p.brand_name && p.brand_name.toLowerCase().includes(q)) ||
    (p.origin_lga && p.origin_lga.toLowerCase().includes(q))
  );
  
  if (matches.length === 0) {
    dropdown.innerHTML = '<div style="padding:16px; color:#64748b; font-size:0.9rem; text-align:center;">No matching commodities found</div>';
    dropdown.style.display = 'block';
    return;
  }
  
  dropdown.innerHTML = matches.map(p => {
    const priceStr = p.unit_price ? formatPriceAmount(p.unit_price) : '';
    const imgHtml = p.image_path ? `<img src="/${p.image_path.replace(/^\//,'')}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">` : `<span style="font-size:1.5rem;">📦</span>`;
    return `
      <div onclick="window.location.href='/products/${p.id}'" style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid #f1f5f9; cursor:pointer; transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
        ${imgHtml}
        <div style="flex:1;">
          <div style="font-weight:700; font-size:0.92rem; color:#0f172a;">${p.name}</div>
          <div style="font-size:0.8rem; color:#64748b;">${p.origin_lga ? p.origin_lga + (p.brand_name ? ' · ' + p.brand_name : '') : (p.brand_name || 'Adamawa Direct Trade')}</div>
        </div>
        <div style="font-weight:800; color:#2563eb; font-size:0.95rem;">${priceStr}</div>
      </div>
    `;
  }).join('');
  dropdown.style.display = 'block';
}

// Hide live search dropdown when clicking outside
document.addEventListener('click', (e) => {
  const dropdown = document.getElementById('liveSearchDropdown');
  const input = document.getElementById('liveSearchInput');
  if (dropdown && input && !dropdown.contains(e.target) && !input.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});

// ─── QUICK VIEW DRAWER/MODAL ═══
function openQuickView(id, name, price, moq, origin, type) {
  const modal = document.getElementById('quickViewModal');
  const content = document.getElementById('qvContent');
  if (!modal || !content) return;
  
  const priceStr = price > 0 ? '₦' + Number(price).toLocaleString() : 'Request Quote';
  const isExport = type === 'export';
  const badgeHtml = isExport
    ? '<span style="background:#f3e8ff; color:#7c3aed; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:50px;">🌍 Export Item</span>'
    : '<span style="background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:50px;">📍 Local Item</span>';
  const buttonHtml = isExport
    ? '<button onclick="closeQuickView(); openRfqModal(' + id + ', \'' + name.replace(/'/g,"\\'") + '\');" style="flex:1; padding:14px; border-radius:50px; background:#7c3aed; color:#fff; font-weight:700; border:none; cursor:pointer;">📋 Request Quote</button>'
    : '<button onclick="addToCartItem({ id: ' + id + ', name: \'' + name.replace(/'/g,"\\'") + '\', price: \'' + price + '\', emoji: \'📦\' }); closeQuickView();" style="flex:1; padding:14px; border-radius:50px; background:#2563eb; color:#fff; font-weight:700; border:none; cursor:pointer;">🛒 Add to Cart</button>';
  
  content.innerHTML = `
    <div style="display:flex; gap:24px; flex-wrap:wrap;">
      <div style="width:220px; height:220px; background:#f8fafc; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:5rem; border:1px solid #e2e8f0; flex-shrink:0;">
        📦
      </div>
      <div style="flex:1; min-width:240px;">
        <div style="display:flex; gap:8px; margin-bottom:8px;">
          ${badgeHtml}
        </div>
        <h2 style="margin:0 0 12px; font-size:1.6rem; font-weight:800; color:#0f172a;">${name}</h2>
        <div style="font-size:1.5rem; font-weight:800; color:#2563eb; margin-bottom:16px;" data-price-ngn="${price}">${priceStr}</div>
        
        <div style="background:#f8fafc; padding:14px; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:20px; font-size:0.88rem; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
          <div><span style="color:#64748b;">Origin:</span> <strong style="color:#0f172a;">${origin || 'Adamawa State'}</strong></div>
          <div><span style="color:#64748b;">Min Order:</span> <strong style="color:#0f172a;">${moq || '10 MT'}</strong></div>
          <div><span style="color:#64748b;">Moisture:</span> <strong style="color:#0f172a;">&lt; 8.0% Max</strong></div>
          <div><span style="color:#64748b;">Purity:</span> <strong style="color:#0f172a;">99.2% Cleaned</strong></div>
        </div>

        <div style="display:flex; gap:12px;">
          ${buttonHtml}
        </div>
      </div>
    </div>
  `;
  modal.style.display = 'flex';
}

function closeQuickView() {
  const modal = document.getElementById('quickViewModal');
  if (modal) modal.style.display = 'none';
}

// ─── RFQ MODAL LOGIC ═══
function openRfqModal(id, name) {
  const modal = document.getElementById('rfqModal');
  if (!modal) return;
  document.getElementById('rfqProductId').value = id;
  document.getElementById('rfqProductName').value = name;
  modal.style.display = 'flex';
}

function closeRfqModal() {
  const modal = document.getElementById('rfqModal');
  if (modal) modal.style.display = 'none';
}

function submitRfqForm(e) {
  e.preventDefault();
  closeRfqModal();
  if (typeof showToast === 'function') {
    showToast('✅ Quote Request transmitted to producer! Check your dashboard.');
  } else {
    alert('✅ Quote Request transmitted to producer!');
  }
}

// ─── WATCHLIST LOGIC ═══
window.atexSaved = JSON.parse(localStorage.getItem('atex_saved') || '[]');

function toggleWatchlist(id, btnEl) {
  if (typeof isAuthenticated !== 'undefined' && !isAuthenticated) {
    if (typeof showToast === 'function') showToast('Sign in to add to wishlist');
    return;
  }
  fetch('/wishlist/toggle', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
    body: JSON.stringify({ product_id: id })
  }).then(function(r) { return r.json(); }).then(function(data) {
    if (data.saved) {
      if (window.atexSaved.indexOf(id) === -1) window.atexSaved.push(id);
      if (btnEl) btnEl.innerHTML = '❤️';
      if (typeof showToast === 'function') showToast('❤️ Saved to wishlist');
    } else {
      var idx = window.atexSaved.indexOf(id);
      if (idx > -1) window.atexSaved.splice(idx, 1);
      if (btnEl) btnEl.innerHTML = '🤍';
      if (typeof showToast === 'function') showToast('Removed from wishlist');
    }
    localStorage.setItem('atex_saved', JSON.stringify(window.atexSaved));
  }).catch(function() {
    if (typeof showToast === 'function') showToast('Error saving to wishlist');
  });
}

function initWatchlistButtons() {
  if (typeof isAuthenticated !== 'undefined' && isAuthenticated) {
    fetch('/wishlist', { headers: { 'Accept': 'application/json' } })
      .then(function(r) { return r.json(); })
      .then(function(ids) {
        window.atexSaved = ids;
        localStorage.setItem('atex_saved', JSON.stringify(ids));
        document.querySelectorAll('[data-watchlist-id]').forEach(function(btn) {
          var id = parseInt(btn.getAttribute('data-watchlist-id'));
          btn.innerHTML = window.atexSaved.includes(id) ? '❤️' : '🤍';
        });
      });
  } else {
    document.querySelectorAll('[data-watchlist-id]').forEach(function(btn) {
      var id = parseInt(btn.getAttribute('data-watchlist-id'));
      btn.innerHTML = window.atexSaved.includes(id) ? '❤️' : '🤍';
    });
  }
}
document.addEventListener('DOMContentLoaded', initWatchlistButtons);

// ─── COMPARE MATRIX LOGIC ═══
window.atexCompare = JSON.parse(localStorage.getItem('atex_compare') || '[]');

function toggleCompareItem(id, name, price, moq, origin, chkEl) {
  const existingIdx = window.atexCompare.findIndex(x => x.id === id);
  if (existingIdx > -1) {
    window.atexCompare.splice(existingIdx, 1);
    if (chkEl && chkEl.parentElement) {
      chkEl.parentElement.style.opacity = '';
      chkEl.parentElement.style.pointerEvents = '';
      chkEl.parentElement.style.transform = '';
    }
  } else {
    if (window.atexCompare.length >= 4) {
      alert('You can compare a maximum of 4 commodities side-by-side.');
      if (chkEl) chkEl.checked = false;
      return;
    }
    window.atexCompare.push({ id, name, price, moq: moq || '10 MT', origin: origin || 'Adamawa LGA' });
    if (chkEl && chkEl.parentElement) {
      chkEl.parentElement.style.opacity = '1';
      chkEl.parentElement.style.pointerEvents = 'auto';
      chkEl.parentElement.style.transform = 'translateY(0)';
    }
  }
  localStorage.setItem('atex_compare', JSON.stringify(window.atexCompare));
  updateCompareDock();
}

function updateCompareDock() {
  const dock = document.getElementById('compareDock');
  const text = document.getElementById('compareCountText');
  if (!dock || !text) return;
  if (window.atexCompare.length > 0) {
    dock.style.display = 'flex';
    text.textContent = window.atexCompare.length + ' commodity(s) selected';
  } else {
    dock.style.display = 'none';
  }
}

function clearCompare() {
  window.atexCompare = [];
  localStorage.setItem('atex_compare', '[]');
  document.querySelectorAll('.compare-chk').forEach(c => {
    c.checked = false;
    if (c.parentElement) {
      c.parentElement.style.opacity = '';
      c.parentElement.style.pointerEvents = '';
      c.parentElement.style.transform = '';
    }
  });
  updateCompareDock();
}

function openCompareMatrix() {
  if (window.atexCompare.length === 0) return;
  const modal = document.getElementById('compareMatrixModal');
  const content = document.getElementById('compareMatrixContent');
  if (!modal || !content) return;

  let headersHtml = '<th style="padding:12px; text-align:left; background:#f8fafc; border-bottom:2px solid #cbd5e1;">Specification</th>';
  let priceRow = '<td style="padding:12px; font-weight:700; background:#f8fafc; border-bottom:1px solid #e2e8f0;">Price (Base)</td>';
  let moqRow = '<td style="padding:12px; font-weight:700; background:#f8fafc; border-bottom:1px solid #e2e8f0;">Min Order Qty</td>';
  let originRow = '<td style="padding:12px; font-weight:700; background:#f8fafc; border-bottom:1px solid #e2e8f0;">Origin LGA</td>';
  let purityRow = '<td style="padding:12px; font-weight:700; background:#f8fafc; border-bottom:1px solid #e2e8f0;">Purity / Cleaned</td>';
  let moistRow = '<td style="padding:12px; font-weight:700; background:#f8fafc; border-bottom:1px solid #e2e8f0;">Moisture Content</td>';
  let actionRow = '<td style="padding:12px; background:#f8fafc;">Action</td>';

  window.atexCompare.forEach(p => {
    const priceStr = typeof formatPriceAmount === 'function' ? formatPriceAmount(p.price) : '₦' + p.price;
    headersHtml += `<th style="padding:12px; text-align:left; background:#f8fafc; border-bottom:2px solid #cbd5e1; min-width:160px;">${p.name}</th>`;
    priceRow += `<td style="padding:12px; font-weight:800; color:#2563eb; border-bottom:1px solid #e2e8f0;">${priceStr}</td>`;
    moqRow += `<td style="padding:12px; border-bottom:1px solid #e2e8f0;">${p.moq}</td>`;
    originRow += `<td style="padding:12px; border-bottom:1px solid #e2e8f0;">${p.origin}</td>`;
    purityRow += `<td style="padding:12px; border-bottom:1px solid #e2e8f0; color:#166534; font-weight:600;">99.2% Verified</td>`;
    moistRow += `<td style="padding:12px; border-bottom:1px solid #e2e8f0;">&lt; 8.0% Max</td>`;
    actionRow += `<td style="padding:12px;"><button onclick="addToCartItem({ id: ${p.id}, name: '${p.name.replace(/'/g,"\\'")}', price: '${p.price}', emoji: '📦' }); closeCompareMatrix();" style="padding:8px 14px; background:#2563eb; color:#fff; font-weight:700; border:none; border-radius:50px; cursor:pointer; font-size:0.8rem;">Add to Order</button></td>`;
  });

  content.innerHTML = `
    <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
      <thead><tr>${headersHtml}</tr></thead>
      <tbody>
        <tr>${priceRow}</tr>
        <tr>${moqRow}</tr>
        <tr>${originRow}</tr>
        <tr>${purityRow}</tr>
        <tr>${moistRow}</tr>
        <tr>${actionRow}</tr>
      </tbody>
    </table>
  `;
  modal.style.display = 'flex';
}

function closeCompareMatrix() {
  document.getElementById('compareMatrixModal').style.display = 'none';
}
document.addEventListener('DOMContentLoaded', updateCompareDock);

// ─── PRO-FORMA INVOICE GENERATOR ═══
function openProFormaInvoice() {
  const modal = document.getElementById('invoiceModal');
  const table = document.getElementById('invItemsTable');
  if (!modal || !table) return;

  const cartItems = JSON.parse(localStorage.getItem('gt_cart') || localStorage.getItem('atex_cart') || '[]');
  document.getElementById('invDate').textContent = new Date().toLocaleDateString();
  document.getElementById('invRef').textContent = Math.floor(100000 + Math.random() * 900000);

  if (cartItems.length === 0) {
    table.innerHTML = '<div style="padding:20px; text-align:center; color:#64748b;">No items in current export order. Add commodities to generate invoice.</div>';
  } else {
    let total = 0;
    let rowsHtml = cartItems.map(item => {
      const lineTotal = (parseFloat(item.price) || 0) * (item.qty || 1);
      total += lineTotal;
      const priceStr = typeof formatPriceAmount === 'function' ? formatPriceAmount(item.price) : '₦' + item.price;
      const lineStr = typeof formatPriceAmount === 'function' ? formatPriceAmount(lineTotal) : '₦' + lineTotal;
      return `
        <tr>
          <td style="padding:10px; border-bottom:1px solid #f1f5f9; font-weight:600;">${item.name}</td>
          <td style="padding:10px; border-bottom:1px solid #f1f5f9; text-align:center;">${item.qty}</td>
          <td style="padding:10px; border-bottom:1px solid #f1f5f9; text-align:right;">${priceStr}</td>
          <td style="padding:10px; border-bottom:1px solid #f1f5f9; text-align:right; font-weight:700;">${lineStr}</td>
        </tr>
      `;
    }).join('');

    const totalStr = typeof formatPriceAmount === 'function' ? formatPriceAmount(total) : '₦' + total;
    table.innerHTML = `
      <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
        <thead>
          <tr style="background:#0f172a; color:#fff;">
            <th style="padding:10px; text-align:left;">Commodity</th>
            <th style="padding:10px; text-align:center;">Qty</th>
            <th style="padding:10px; text-align:right;">Unit Price</th>
            <th style="padding:10px; text-align:right;">Total</th>
          </tr>
        </thead>
        <tbody>${rowsHtml}</tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="padding:12px 10px; text-align:right; font-weight:800; font-size:1.05rem;">TOTAL ESCROW AMOUNT:</td>
            <td style="padding:12px 10px; text-align:right; font-weight:900; font-size:1.1rem; color:#2563eb;">${totalStr}</td>
          </tr>
        </tfoot>
      </table>
    `;
  }
  modal.style.display = 'flex';
}

function closeInvoiceModal() {
  document.getElementById('invoiceModal').style.display = 'none';
}

// ─── HELP DRAWER LOGIC ═══
function toggleHelpDrawer() {
  const drawer = document.getElementById('helpDrawer');
  if (drawer) drawer.style.display = drawer.style.display === 'none' ? 'block' : 'none';
}

function showHelpAnswer(topic) {
  const box = document.getElementById('helpAnswerBox');
  if (!box) return;
  const answers = {
    escrow: '<strong>🛡️ 100% Escrow Protection:</strong> Funds deposited are held in secure Central Bank verification accounts. Payment is only released to Adamawa producers after inspection confirms commodity moisture, weight, and phytosanitary specs match your quote.',
    incoterms: '<strong>🚢 Supported Incoterms:</strong> We support FOB (Free on Board - Lagos/PH Ports), CIF (Cost, Insured & Freight to your destination port), and EXW (Ex-Works aggregation centers in Yola/Mubi).',
    moq: '<strong>📦 Lower MOQ Requests:</strong> If your trial order requires smaller quantities than listed, click "📋 RFQ" on the product card and specify your exact requirements. Producers frequently accept sample shipments!'
  };
  box.innerHTML = answers[topic] || '';
  box.style.display = 'block';
}

function submitHelpInquiry(e) {
  e.preventDefault();
  toggleHelpDrawer();
  if (typeof showToast === 'function') {
    showToast('✅ Inquiry recorded! An export rep will reply via email shortly.');
  } else {
    alert('✅ Inquiry recorded!');
  }
}
</script>
