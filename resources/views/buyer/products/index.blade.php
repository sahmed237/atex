@extends('layouts.buyer')

@section('content')
<style>
:root {
  --primary: #2563eb;
  --accent: #f59e0b;
  --green: #16a34a;
  --bg: #ffffff;
  --bg-alt: #f8fafc;
  --text: #0f172a;
  --text-muted: #64748b;
  --border: #e2e8f0;
  --radius: 12px;
  --shadow-lg: 0 10px 25px rgba(0,0,0,.1);
  --transition: .25s ease;
}
</style>

<div class="page-header" style="padding:24px 0 20px;border-bottom:1px solid var(--border)">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
    <div>
      <h1 style="font-size:1.5rem;font-weight:700;margin:0">All Products <span class="count" style="color:var(--text-muted);font-size:.92rem;font-weight:400">({{ $products->total() }})</span></h1>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
      <label style="font-size:.85rem;color:var(--text-muted)">Sort by</label>
      <form method="GET" action="{{ route('buyer.products.index') }}" id="sortForm">
        <input type="hidden" name="search" value="{{ request('search') }}">
        @foreach((array) request('category', []) as $cat)
          <input type="hidden" name="category[]" value="{{ $cat }}">
        @endforeach
        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
        <select name="sort" onchange="document.getElementById('sortForm').submit()" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:.88rem;background:var(--bg);outline:none;cursor:pointer">
          <option value="">Default</option>
          <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
          <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
          <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
        </select>
      </form>
    </div>
  </div>
</div>

@php
    $activeCategories = (array) request('category', []);
    $catNames = $categories->whereIn('slug', $activeCategories)->pluck('name', 'slug');
    $hasFilters = request('search') || $activeCategories || request('min_price') || request('max_price') || request('sort');
@endphp

@if($hasFilters)
<div style="padding:16px 0 0;display:flex;flex-wrap:wrap;align-items:center;gap:8px">
  <span style="font-size:.82rem;color:var(--text-muted);font-weight:500">Active filters:</span>
  @if(request('search'))
    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:50px;background:var(--bg-alt);border:1px solid var(--border);font-size:.8rem">
      Search: "{{ request('search') }}"
      <a href="{{ route('buyer.products.index', request()->except('search', 'page')) }}" style="text-decoration:none;color:var(--text-muted);font-size:1rem;line-height:1">&times;</a>
    </span>
  @endif
  @foreach($activeCategories as $slug)
    @php $name = $catNames[$slug] ?? ucfirst($slug); @endphp
    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:50px;background:var(--bg-alt);border:1px solid var(--border);font-size:.8rem">
      {{ $name }}
      <a href="{{ route('buyer.products.index', array_merge(request()->except('category', 'page'), ['category' => array_values(array_filter($activeCategories, fn($c) => $c !== $slug))])) }}" style="text-decoration:none;color:var(--text-muted);font-size:1rem;line-height:1">&times;</a>
    </span>
  @endforeach
  @if(request('min_price') || request('max_price'))
    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:50px;background:var(--bg-alt);border:1px solid var(--border);font-size:.8rem">
      Price: {{ request('min_price') ? '₦'.request('min_price') : '₦0' }} – {{ request('max_price') ? '₦'.request('max_price') : '∞' }}
      <a href="{{ route('buyer.products.index', request()->except('min_price', 'max_price', 'page')) }}" style="text-decoration:none;color:var(--text-muted);font-size:1rem;line-height:1">&times;</a>
    </span>
  @endif
</div>
@endif

<div style="display:grid;grid-template-columns:220px 1fr;gap:28px;padding:24px 0">
  <aside>
    <h3 style="font-size:1rem;font-weight:700;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid var(--border)">Filters</h3>
    <form method="GET" action="{{ route('buyer.products.index') }}" id="filterForm">
      <input type="hidden" name="sort" value="{{ request('sort') }}">
      <input type="hidden" name="search" value="{{ request('search') }}">

      <div style="margin-bottom:24px">
        <h4 style="font-size:.85rem;font-weight:600;margin:0 0 8px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Category</h4>
        @foreach($categories->take(3) as $cat)
          <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;padding:4px 0;cursor:pointer">
            <input type="checkbox" name="category[]" value="{{ $cat->slug }}" onchange="document.getElementById('filterForm').submit()"
                   style="width:16px;height:16px;accent-color:var(--primary);cursor:pointer"
                   {{ in_array($cat->slug, (array) request('category', [])) ? 'checked' : '' }}>
            {{ $cat->name }}
          </label>
        @endforeach
        @if($categories->count() > 5)
          <a href="{{ route('buyer.categories.index') }}" style="display:inline-block;margin-top:6px;font-size:.85rem;font-weight:600;color:var(--primary);text-decoration:none">
            + {{ $categories->count() - 5 }} more &rarr;
          </a>
        @endif
      </div>

      <div style="margin-bottom:24px">
        <h4 style="font-size:.85rem;font-weight:600;margin:0 0 8px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Price Range</h4>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="number" name="min_price" placeholder="Min" min="0" value="{{ request('min_price') }}"
                 onchange="document.getElementById('filterForm').submit()"
                 style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;font-size:.85rem;outline:none">
          <span style="color:var(--text-muted);font-size:.85rem">—</span>
          <input type="number" name="max_price" placeholder="Max" min="0" value="{{ request('max_price') }}"
                 onchange="document.getElementById('filterForm').submit()"
                 style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;font-size:.85rem;outline:none">
        </div>
      </div>

      @if(request('search') || request('category') || request('min_price') || request('max_price') || request('sort'))
        <a href="{{ route('buyer.products.index') }}" style="display:block;text-align:center;padding:10px;border:1px solid var(--border);border-radius:8px;font-size:.85rem;font-weight:500;color:var(--text-muted);transition:all var(--transition);text-decoration:none"
           onmouseover="this.style.borderColor='var(--text)';this.style.color='var(--text)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">Clear All Filters</a>
      @endif
    </form>
  </aside>

  <div>
    @if($products->count() > 0)
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:20px">
        @foreach($products as $product)
          <div class="product-card" style="border-radius:var(--radius);background:var(--bg);border:1px solid var(--border);overflow:hidden;transition:transform var(--transition),box-shadow var(--transition);cursor:pointer"
               onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='var(--shadow-lg)'"
               onmouseout="this.style.transform='';this.style.boxShadow=''"
               onclick="location.href='{{ route('buyer.products.show', $product->id) }}'">
            <div style="aspect-ratio:1;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;font-size:3rem;position:relative">
              @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain;padding:12px">
              @else
                <i data-lucide="package" style="width:48px;height:48px;color:#cbd5e1"></i>
              @endif
            </div>
            <div style="padding:14px">
              <div style="font-size:.78rem;color:var(--text-muted);margin-bottom:4px">{{ $product->brand_name ?: ($product->sellerProfile->business_name ?? 'ATEX') }}</div>
              <h3 style="font-size:.95rem;margin:0 0 4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $product->name }}</h3>
              <div style="font-size:.78rem;color:var(--accent);letter-spacing:1px;margin-bottom:4px">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
              <div style="font-size:.78rem;color:var(--text-muted);margin-bottom:6px">MOQ: {{ $product->moq }}</div>
              <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
                @if($product->unit_price && $product->unit_price !== 'Request quote')
                  <span style="font-size:1.1rem;font-weight:700">&#8358;{{ number_format((float) $product->unit_price) }}</span>
                @else
                  <span style="font-size:.9rem;font-weight:600;color:var(--primary)">Request Quote</span>
                @endif
              </div>
              <button class="add-to-cart-btn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->unit_price }}"
                      onclick="event.stopPropagation();addToCart(this)"
                      style="width:100%;padding:10px;border-radius:50px;background:var(--text);color:#fff;font-weight:600;font-size:.85rem;border:none;cursor:pointer;transition:background var(--transition)"
                      onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--text)'">
                Add to Cart
              </button>
            </div>
          </div>
        @endforeach
      </div>

      <div style="display:flex;justify-content:center;gap:8px;margin-top:40px">
        @if($products->onFirstPage())
          <button disabled style="width:36px;height:36px;border-radius:8px;border:1px solid var(--border);font-size:.88rem;font-weight:500;opacity:.4">&laquo;</button>
        @else
          <a href="{{ $products->previousPageUrl() }}" style="width:36px;height:36px;border-radius:8px;border:1px solid var(--border);font-size:.88rem;font-weight:500;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit">&laquo;</a>
        @endif
        @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
          <a href="{{ $url }}" style="width:36px;height:36px;border-radius:8px;border:1px solid var(--border);font-size:.88rem;font-weight:500;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit;{{ $page == $products->currentPage() ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">{{ $page }}</a>
        @endforeach
        @if($products->hasMorePages())
          <a href="{{ $products->nextPageUrl() }}" style="width:36px;height:36px;border-radius:8px;border:1px solid var(--border);font-size:.88rem;font-weight:500;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit">&raquo;</a>
        @else
          <button disabled style="width:36px;height:36px;border-radius:8px;border:1px solid var(--border);font-size:.88rem;font-weight:500;opacity:.4">&raquo;</button>
        @endif
      </div>
    @else
      <div style="text-align:center;padding:60px 20px;color:var(--text-muted)">
        <div style="font-size:3rem;margin-bottom:12px">🔍</div>
        <p style="font-size:1rem">No products match your filters.</p>
        @if(request('search') || request('category') || request('min_price') || request('max_price'))
          <a href="{{ route('buyer.products.index') }}" style="display:inline-block;margin-top:16px;padding:10px 24px;border-radius:50px;background:var(--text);color:#fff;font-weight:600;font-size:.85rem;text-decoration:none">Clear Filters</a>
        @endif
      </div>
    @endif
  </div>
</div>

<!-- Cart Sidebar -->
<div class="cart-overlay" id="cartOverlay" onclick="toggleCart()" style="position:fixed;inset:0;background:rgba(0,0,0,.4);opacity:0;pointer-events:none;transition:opacity var(--transition);z-index:200"></div>
<aside class="cart-sidebar" id="cartSidebar" style="position:fixed;top:0;right:0;width:420px;max-width:90vw;height:100vh;background:var(--bg);z-index:201;transform:translateX(100%);transition:transform .35s cubic-bezier(.22,1,.36,1);display:flex;flex-direction:column">
  <div style="display:flex;justify-content:space-between;align-items:center;padding:24px;border-bottom:1px solid var(--border)">
    <h2 style="font-size:1.25rem;margin:0">Cart</h2>
    <button onclick="toggleCart()" style="width:36px;height:36px;border-radius:50%;background:var(--bg-alt);font-size:1.2rem;border:none;cursor:pointer">✕</button>
  </div>
  <div class="cart-items" id="cartItems" style="flex:1;overflow-y:auto;padding:16px 24px">
    <div class="cart-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--text-muted);text-align:center;padding:40px">
      <p>Your cart is empty</p>
    </div>
  </div>
  <div style="padding:24px;border-top:1px solid var(--border)">
    <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;margin-bottom:16px"><span>Total</span><span id="cartTotal">₦0.00</span></div>
    <button onclick="handleCheckout()" style="width:100%;padding:16px;border-radius:50px;background:var(--text);color:#fff;font-weight:600;font-size:1rem;border:none;cursor:pointer;transition:background var(--transition)"
            onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--text)'">Checkout</button>
  </div>
</aside>

<div class="toast" id="toast" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:#fff;padding:14px 28px;border-radius:50px;font-weight:500;font-size:.92rem;opacity:0;transition:all .35s cubic-bezier(.22,1,.36,1);z-index:300;pointer-events:none"></div>

<script>
let cart = JSON.parse(localStorage.getItem('atex_cart') || '[]');

function toggleCart() {
  document.getElementById('cartOverlay').style.opacity = document.getElementById('cartOverlay').style.opacity === '1' ? '0' : '1';
  document.getElementById('cartOverlay').style.pointerEvents = document.getElementById('cartOverlay').style.pointerEvents === 'auto' ? 'none' : 'auto';
  const sidebar = document.getElementById('cartSidebar');
  sidebar.style.transform = sidebar.style.transform === 'translateX(0px)' ? 'translateX(100%)' : 'translateX(0px)';
}

function addToCart(btn) {
  const id = btn.dataset.productId;
  const name = btn.dataset.productName;
  const price = parseFloat(btn.dataset.productPrice) || 0;
  const existing = cart.find(c => c.id === id);
  if (existing) { existing.qty++; } else { cart.push({ id, name, price, qty: 1 }); }
  updateCartUI();
  showToast(name + ' added to cart');
}

function removeFromCart(id) { cart = cart.filter(c => c.id !== id); updateCartUI(); }

function changeQty(id, delta) {
  const item = cart.find(c => c.id === id);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) { removeFromCart(id); return; }
  updateCartUI();
}

function updateCartUI() {
  const count = cart.reduce((s, c) => s + c.qty, 0);
  localStorage.setItem('atex_cart', JSON.stringify(cart));
  const container = document.getElementById('cartItems');
  const totalEl = document.getElementById('cartTotal');
  if (!container) return;
  if (cart.length === 0) {
    container.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--text-muted);text-align:center;padding:40px"><p>Your cart is empty</p></div>';
    if (totalEl) totalEl.textContent = '₦0.00';
    return;
  }
  container.innerHTML = cart.map(c => `
    <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0">📦</div>
      <div style="flex:1">
        <div style="font-size:.85rem;font-weight:600">${c.name}</div>
        <div style="font-size:.8rem;color:var(--text-muted)">₦${c.price.toFixed(2)}</div>
        <div style="display:flex;align-items:center;gap:6px;margin-top:4px">
          <button onclick="changeQty('${c.id}',-1)" style="width:24px;height:24px;border-radius:50%;background:var(--bg-alt);font-weight:600;font-size:.85rem;border:none;cursor:pointer">−</button>
          <span style="font-weight:600;font-size:.85rem;min-width:16px;text-align:center">${c.qty}</span>
          <button onclick="changeQty('${c.id}',1)" style="width:24px;height:24px;border-radius:50%;background:var(--bg-alt);font-weight:600;font-size:.85rem;border:none;cursor:pointer">+</button>
          <button onclick="removeFromCart('${c.id}')" style="font-size:.78rem;color:var(--text-muted);padding:2px 6px;border:none;cursor:pointer;background:none">✕</button>
        </div>
      </div>
    </div>
  `).join('');
  const total = cart.reduce((s, c) => s + c.price * c.qty, 0);
  if (totalEl) totalEl.textContent = '₦' + total.toFixed(2);
}

function handleCheckout() {
  if (cart.length === 0) { showToast('Cart is empty'); return; }
  showToast('Checkout coming soon!');
}

function showToast(msg) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.style.opacity = '1';
  el.style.transform = 'translateX(-50%) translateY(0)';
  clearTimeout(el._timeout);
  el._timeout = setTimeout(() => {
    el.style.opacity = '0';
    el.style.transform = 'translateX(-50%) translateY(80px)';
  }, 3000);
}

updateCartUI();
</script>
@endsection
