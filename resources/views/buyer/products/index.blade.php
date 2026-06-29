@extends('layouts.buyer')

@section('full_width_content')
<style>
  /* ─── PAGE HEADER ─── */
  .page-header {
    padding: 32px 0 24px;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
  }
  .page-header .container { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
  .page-header h1 { font-size: 1.75rem; font-weight: 700; color: var(--text); margin: 0; }
  .page-header .count { color: var(--text-muted); font-size: .92rem; font-weight: 400; }
  .page-header .sort-group { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
  .page-header .sort-group label { font-size: .85rem; color: var(--text-muted); margin: 0; }
  .page-header .sort-group select {
    padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px;
    font-size: .88rem; background: var(--bg); color: var(--text); outline: none; cursor: pointer;
  }

  /* ─── LAYOUT ─── */
  .layout { display: grid; grid-template-columns: 240px 1fr; gap: 32px; padding: 32px 0; }
  @media (max-width: 820px) { .layout { grid-template-columns: 1fr; } }

  /* ─── SIDEBAR FILTERS ─── */
  .sidebar { background: transparent !important; color: var(--text) !important; padding: 0 !important; border: none !important; box-shadow: none !important; position: static !important; }
  .sidebar h3 { font-size: 1rem; font-weight: 700; margin: 0 0 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border); color: var(--text); }
  .filter-group { margin-bottom: 24px; background: transparent !important; }
  .filter-group h4 { font-size: .85rem; font-weight: 600; margin: 0 0 8px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
  .filter-group label { display: flex !important; align-items: center !important; gap: 8px !important; font-size: .9rem !important; padding: 4px 0 !important; cursor: pointer !important; color: var(--text) !important; margin: 0 !important; font-weight: 400 !important; background: transparent !important; border: none !important; box-shadow: none !important; }
  .filter-group input[type="checkbox"] { width: 16px !important; height: 16px !important; min-height: 0 !important; padding: 0 !important; border: none !important; background: transparent !important; box-shadow: none !important; accent-color: var(--primary) !important; cursor: pointer !important; margin: 0 !important; flex-shrink: 0 !important; appearance: auto !important; -webkit-appearance: auto !important; }
  .filter-group .price-inputs { display: flex; gap: 8px; align-items: center; }
  .filter-group .price-inputs input { width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 6px; font-size: .85rem; outline: none; color: var(--text); background: var(--bg); }
  .filter-group .price-inputs span { color: var(--text-muted); font-size: .85rem; }
  .clear-filters {
    width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px;
    font-size: .85rem; font-weight: 500; color: var(--text-muted); transition: all var(--transition); background: none; cursor: pointer; text-align: center; display: block; text-decoration: none;
  }
  .clear-filters:hover { border-color: var(--text); color: var(--text); }

  /* ─── PRODUCTS GRID ─── */
  .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
  .product-card { border-radius: var(--radius); background: var(--bg); border: 1px solid var(--border); overflow: hidden; transition: transform var(--transition), box-shadow var(--transition); display: flex; flex-direction: column; }
  .product-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
  .product-img { aspect-ratio: 1; background: var(--bg-alt); display: flex; align-items: center; justify-content: center; font-size: 3rem; position: relative; flex-shrink: 0; }
  .product-tag { position: absolute; top: 10px; left: 10px; padding: 3px 10px; border-radius: 50px; font-size: .7rem; font-weight: 600; background: var(--accent); color: #fff; }
  .product-tag.sale { background: #ef4444; }
  .product-tag.green { background: var(--green); }
  .product-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
  
  .product-body h3 { font-size: .95rem; margin: 4px 0 4px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .product-stars { font-size: .78rem; color: var(--accent); letter-spacing: 1px; margin-bottom: 4px; }
  .product-meta { font-size: .78rem; color: var(--text-muted); margin-bottom: 6px; }
  .product-price { display: flex; align-items: center; gap: 6px; margin-bottom: 12px; margin-top: auto; }
  .product-price .current { font-size: 1.1rem; font-weight: 700; color: var(--text); }
  .product-price .old { font-size: .85rem; color: var(--text-muted); text-decoration: line-through; }
  .add-to-cart { width: 100%; padding: 10px; border-radius: 50px; background: var(--text); color: #fff; font-weight: 600; font-size: .85rem; transition: background var(--transition); border: none; cursor: pointer; }
  .add-to-cart:hover { background: var(--primary); }

  /* ─── PAGINATION ─── */
  .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 40px; }
  .pagination button, .pagination a {
    width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border);
    font-size: .88rem; font-weight: 500; transition: all var(--transition); display: flex; align-items: center; justify-content: center; text-decoration: none; color: inherit; background: none; cursor: pointer;
  }
  .pagination button:hover, .pagination a:hover { border-color: var(--primary); color: var(--primary); }
  .pagination .active { background: var(--primary); color: #fff; border-color: var(--primary); }

  .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); grid-column: 1 / -1; }
  .empty-state .icon { font-size: 3rem; margin-bottom: 12px; }
  .empty-state p { font-size: 1rem; margin: 0; }
</style>

<!-- ═══ PAGE HEADER ═══ -->
<div class="page-header">
  <div class="container">
    <div>
      <h1>All Products <span class="count" id="resultCount">({{ $products->total() }} products)</span></h1>
    </div>
    <div class="sort-group">
      <label>Sort by</label>
      <select id="sortSelect" onchange="applySort(this.value)">
        <option value="default" {{ !request('sort') || request('sort') == 'default' ? 'selected' : '' }}>Default</option>
        <option value="price_asc" {{ in_array(request('sort'), ['price_asc', 'price-asc']) ? 'selected' : '' }}>Price: Low to High</option>
        <option value="price_desc" {{ in_array(request('sort'), ['price_desc', 'price-desc']) ? 'selected' : '' }}>Price: High to Low</option>
        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
      </select>
    </div>
  </div>
</div>

<!-- ═══ LAYOUT ═══ -->
<div class="container">
  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <h3>Filters</h3>

      <form method="GET" action="{{ route('buyer.products.index') }}" id="filterForm">
        <input type="hidden" name="sort" id="formSortInput" value="{{ request('sort') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">

        <div class="filter-group">
          <h4>Category</h4>
          <div id="categoryList" style="max-height:28px; overflow:hidden; transition:max-height 0.3s ease;">
            @foreach($sharedCategories as $cat)
              <label>
                <input type="checkbox" name="category[]" value="{{ $cat->slug }}" onchange="document.getElementById('filterForm').submit()" {{ in_array($cat->slug, (array)request('category', [])) ? 'checked' : '' }}>
                {{ $cat->name }}
              </label>
            @endforeach
          </div>
          <button type="button" id="catToggle" onclick="toggleCategories()" style="background:none; border:none; color:var(--primary); font-size:0.8rem; font-weight:600; cursor:pointer; padding:4px 0;">Show more ▾</button>
        </div>

        <div class="filter-group">
          <h4>Price Range</h4>
          <div class="price-inputs">
            <input type="number" name="min_price" id="priceMin" placeholder="Min" min="0" value="{{ request('min_price') }}" onchange="document.getElementById('filterForm').submit()">
            <span>—</span>
            <input type="number" name="max_price" id="priceMax" placeholder="Max" min="0" value="{{ request('max_price') }}" onchange="document.getElementById('filterForm').submit()">
          </div>
        </div>

        <div class="filter-group">
          <h4>Tags</h4>
          <label><input type="checkbox" name="tags[]" value="Sale" onchange="document.getElementById('filterForm').submit()" {{ in_array('Sale', (array)request('tags', [])) ? 'checked' : '' }}> Sale</label>
          <label><input type="checkbox" name="tags[]" value="New" onchange="document.getElementById('filterForm').submit()" {{ in_array('New', (array)request('tags', [])) ? 'checked' : '' }}> New</label>
          <label><input type="checkbox" name="tags[]" value="Bestseller" onchange="document.getElementById('filterForm').submit()" {{ in_array('Bestseller', (array)request('tags', [])) ? 'checked' : '' }}> Bestseller</label>
        </div>

        <button type="button" class="clear-filters" onclick="window.location.href='{{ route('buyer.products.index') }}'">Clear All Filters</button>
      </form>
    </aside>

    <div>
      <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; align-items:center; background:var(--bg-alt); padding:12px 16px; border-radius:12px; border:1px solid var(--border);">
        <span style="font-size:0.8rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-right:4px;">📍 Origin LGA:</span>
        <a href="{{ route('buyer.products.index') }}" class="origin-pill active" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:{{ !request('search') ? 'var(--primary)' : 'var(--card-bg)' }}; color:{{ !request('search') ? '#fff' : 'var(--text)' }}; font-weight:700; font-size:0.8rem; text-decoration:none;">All Adamawa</a>
        <a href="{{ route('buyer.products.index', ['search' => 'Mubi']) }}" class="origin-pill" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:{{ request('search') == 'Mubi' ? 'var(--primary)' : 'var(--card-bg)' }}; color:{{ request('search') == 'Mubi' ? '#fff' : 'var(--text)' }}; font-weight:700; font-size:0.8rem; text-decoration:none;">🌾 Mubi North</a>
        <a href="{{ route('buyer.products.index', ['search' => 'Yola']) }}" class="origin-pill" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:{{ request('search') == 'Yola' ? 'var(--primary)' : 'var(--card-bg)' }}; color:{{ request('search') == 'Yola' ? '#fff' : 'var(--text)' }}; font-weight:700; font-size:0.8rem; text-decoration:none;">🥜 Yola South</a>
        <a href="{{ route('buyer.products.index', ['search' => 'Numan']) }}" class="origin-pill" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:{{ request('search') == 'Numan' ? 'var(--primary)' : 'var(--card-bg)' }}; color:{{ request('search') == 'Numan' ? '#fff' : 'var(--text)' }}; font-weight:700; font-size:0.8rem; text-decoration:none;">🍯 Numan</a>
        <a href="{{ route('buyer.products.index', ['search' => 'Guyuk']) }}" class="origin-pill" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:{{ request('search') == 'Guyuk' ? 'var(--primary)' : 'var(--card-bg)' }}; color:{{ request('search') == 'Guyuk' ? '#fff' : 'var(--text)' }}; font-weight:700; font-size:0.8rem; text-decoration:none;">🪨 Guyuk</a>
      </div>
      @php
        $displayedProducts = 0;
      @endphp
      <div class="products-grid" id="productsGrid">
        @foreach($products as $product)
          @php
            $emojis = ['🎧', '👜', '☕', '🔊', '🧵', '🥭', '🔋', '🧺', '🔌', '🧣', '🍵', '🌾', '📦'];
            $emoji = $emojis[$product->id % count($emojis)];
            
            $tagsList = [null, 'Bestseller', 'Sale', 'New', null, 'Sale', 'Bestseller'];
            $tag = $tagsList[$product->id % count($tagsList)];
            $tagClass = $tag === 'Sale' ? 'sale' : ($tag === 'Bestseller' ? 'green' : '');
            
            if (!empty(request('tags')) && !in_array($tag, (array)request('tags', []))) {
                continue;
            }
            $displayedProducts++;

            $rating = 4.5;
            $stars = '★★★★☆';
          @endphp
          <div class="product-card" onclick="location.href='{{ route('buyer.products.show', $product->id) }}'" style="cursor:pointer; position:relative;">
            <div class="product-img" style="position:relative;">
              <button onclick="event.stopPropagation(); toggleWatchlist({{ $product->id }}, this);" data-watchlist-id="{{ $product->id }}" style="position:absolute; top:8px; right:8px; background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; width:32px; height:32px; font-size:1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.15); z-index:2;" title="Save to Watchlist">🤍</button>
              @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain;padding:12px">
              @else
                {{ $emoji }}
              @endif
              @if($tag)
                <span class="product-tag {{ $tagClass }}">{{ $tag }}</span>
              @endif
               <button onclick="event.stopPropagation(); openQuickView({{ $product->id }}, '{{ addslashes($product->name) }}', {{ is_numeric($product->unit_price) ? $product->unit_price : 0 }}, '{{ addslashes($product->moq ?? '10 MT') }}', '{{ addslashes($product->origin_lga ?? 'Adamawa') }}', '{{ $product->isExport() ? 'export' : 'local' }}')" style="position:absolute; bottom:8px; right:8px; background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50px; padding:6px 12px; font-size:0.75rem; font-weight:700; cursor:pointer; color:#0f172a; box-shadow:0 2px 6px rgba(0,0,0,0.12); z-index:2;">👁️ Quick View</button>
            </div>
            <div class="product-body">
                <div style="overflow:hidden; margin-bottom:6px;">
                @if($product->isExport())
                  <div style="font-size:0.7rem; font-weight:800; color:#7c3aed; background:#f3e8ff; padding:3px 8px; border-radius:4px; display:inline-block;">🌍 Export</div>
                @else
                  <div style="font-size:0.7rem; font-weight:800; color:#166534; background:#dcfce7; padding:3px 8px; border-radius:4px; display:inline-block;">📍 Local</div>
                @endif
                <label class="compare-label" onclick="event.stopPropagation();"><input type="checkbox" class="compare-chk" onclick="toggleCompareItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ is_numeric($product->unit_price) ? $product->unit_price : 0 }}, '{{ addslashes($product->moq ?? '10 MT') }}', '{{ addslashes($product->origin_lga ?? 'Adamawa') }}', this)"> ⚖️ Compare</label>
              </div>
              <h3>{{ $product->name }}</h3>
              <div class="product-stars">{{ $stars }}</div>
              <div class="product-meta">MOQ: {{ $product->moq ?? 'N/A' }} • {{ $product->origin_lga ?? ($product->brand_name ?? 'Adamawa') }}</div>
              <div class="product-price">
                <span class="current" data-price-ngn="{{ is_numeric($product->unit_price) ? $product->unit_price : '' }}">{{ is_numeric($product->unit_price) ? '₦' . number_format((float) $product->unit_price, 2) : $product->unit_price }}</span>
                @if($tag === 'Sale' && is_numeric($product->unit_price))
                  <span class="old" data-price-ngn="{{ $product->unit_price * 1.3 }}">₦{{ number_format((float) $product->unit_price * 1.3, 2) }}</span>
                @endif
              </div>
              <div style="display:flex; gap:6px; margin-top:10px;">
                @if($product->isLocal())
                  <button class="add-to-cart" onclick="event.stopPropagation(); addToCartItem({ id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: '{{ addslashes($product->unit_price) }}', emoji: '{{ $emoji }}' })" style="flex:1;">Add to Cart</button>
                @else
                  <button class="add-to-cart" onclick="event.stopPropagation(); openRfqModal({{ $product->id }}, '{{ addslashes($product->name) }}')" style="flex:1; background:#7c3aed;">📋 Request Quote</button>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      @if($displayedProducts === 0)
        <div class="empty-state">
          <div class="icon">🔍</div>
          <p>No products match your filters.</p>
        </div>
      @endif

      <!-- Pagination -->
      <div class="pagination" id="pagination">
        @if($products->lastPage() > 1)
          @for($i = 1; $i <= $products->lastPage(); $i++)
            <a href="{{ $products->url($i) }}" class="{{ $i == $products->currentPage() ? 'active' : '' }}">{{ $i }}</a>
          @endfor
        @endif
      </div>
    </div>
  </div>
</div>

<script>
  function applySort(val) {
    document.getElementById('formSortInput').value = val === 'default' ? '' : val;
    document.getElementById('filterForm').submit();
  }
  function toggleCategories() {
    var list = document.getElementById('categoryList');
    var btn = document.getElementById('catToggle');
    var expanded = list.style.maxHeight !== '28px';
    list.style.maxHeight = expanded ? '28px' : list.scrollHeight + 'px';
    btn.textContent = expanded ? 'Show more ▾' : 'Show less ▴';
  }
</script>
@endsection
