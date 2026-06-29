@extends('layouts.buyer')

@section('content')
<div style="padding-bottom:20px;font-size:.85rem;color:var(--text-muted)">
  <a href="{{ url('/') }}" style="color:inherit;text-decoration:none">Home</a>
  / <a href="{{ route('buyer.products.index') }}" style="color:inherit;text-decoration:none">Products</a>
  / <span style="color:var(--text)">{{ $product->name }}</span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;padding-bottom:60px">
  <div>
    <div style="background:var(--bg-alt);border-radius:var(--radius);aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:6rem;border:1px solid var(--border);position:relative;overflow:hidden">
      @if($product->image_path)
        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain;padding:24px">
      @else
        <i data-lucide="package" style="width:80px;height:80px;color:#cbd5e1"></i>
      @endif
    </div>
  </div>

  <div>
    <div style="display:flex; gap:8px; margin-bottom:10px;">
      @if($product->isExport())
        <span style="background:#f3e8ff; color:#7c3aed; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:50px;">🌍 Export Item</span>
      @else
        <span style="background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:50px;">📍 Local Item</span>
      @endif
    </div>
    <div style="display:inline-block;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);margin-bottom:8px">
      {{ $product->category->name ?? 'General' }}
    </div>
    <h1 style="font-size:1.75rem;font-weight:700;margin:0 0 8px;line-height:1.2">{{ $product->name }}</h1>
    <div style="font-size:1rem;color:var(--accent);letter-spacing:2px;margin-bottom:12px">
      @for($i = 1; $i <= 5; $i++)
        @if($i <= round($avgRating))&#9733;@else&#9734;@endif
      @endfor
    </div>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
      @if($product->unit_price && $product->unit_price !== 'Request quote')
        <span style="font-size:2rem;font-weight:800" class="current" data-price-ngn="{{ is_numeric($product->unit_price) ? $product->unit_price : '' }}">&#8358;{{ number_format((float) $product->unit_price) }}</span>
      @else
        <span style="font-size:1.2rem;font-weight:600;color:var(--primary)">Request Quote</span>
      @endif
      <span style="font-size:.82rem;color:var(--text-muted);background:var(--bg-alt);padding:4px 12px;border-radius:50px">MOQ: {{ $product->moq }}</span>
    </div>

    @if($product->description)
      <div style="font-size:.95rem;color:var(--text-muted);line-height:1.7;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--border)">
        {{ $product->description }}
      </div>
    @endif

    <div style="display:flex;align-items:center;gap:8px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;margin-bottom:20px;font-size:.88rem;color:#166534">
      <span style="font-size:1.2rem">🚚</span>
      <span>Estimated delivery: <strong>7–14 business days</strong></span>
      <span style="margin-left:auto;font-size:.78rem;color:#4ade80">Free shipping</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px">
      <div style="padding:12px 16px;background:var(--bg-alt);border-radius:8px">
        <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Category</div>
        <div style="font-size:.95rem;font-weight:600;margin-top:2px">{{ $product->category->name ?? 'N/A' }}</div>
      </div>
      <div style="padding:12px 16px;background:var(--bg-alt);border-radius:8px">
        <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Min Order Qty</div>
        <div style="font-size:.95rem;font-weight:600;margin-top:2px">{{ $product->moq }}</div>
      </div>
      <div style="padding:12px 16px;background:var(--bg-alt);border-radius:8px">
        <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Unit Price</div>
        <div style="font-size:.95rem;font-weight:600;margin-top:2px" class="current" data-price-ngn="{{ is_numeric($product->unit_price) ? $product->unit_price : '' }}">{{ is_numeric($product->unit_price) ? '₦' . number_format((float) $product->unit_price) : $product->unit_price }}</div>
      </div>
      <div style="padding:12px 16px;background:var(--bg-alt);border-radius:8px">
        <div style="font-size:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Seller</div>
        <div style="font-size:.95rem;font-weight:600;margin-top:2px">{{ $product->sellerProfile->business_name ?? 'ATEX' }}</div>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
      <label style="font-size:.9rem;font-weight:600">Quantity:</label>
      <div style="display:flex;align-items:center;border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <button onclick="changeQty(-1)" style="width:40px;height:40px;background:var(--bg-alt);font-size:1.1rem;font-weight:600;border:none;cursor:pointer;transition:background var(--transition)" onmouseover="this.style.background='var(--border)'" onmouseout="this.style.background='var(--bg-alt)'">−</button>
        <span id="qtyDisplay" style="width:48px;text-align:center;font-weight:600;font-size:1rem">1</span>
        <button onclick="changeQty(1)" style="width:40px;height:40px;background:var(--bg-alt);font-size:1.1rem;font-weight:600;border:none;cursor:pointer;transition:background var(--transition)" onmouseover="this.style.background='var(--border)'" onmouseout="this.style.background='var(--bg-alt)'">+</button>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap">
      @if($product->isLocal())
        <button class="btn-primary" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ addslashes($product->unit_price) }}')" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:50px;font-weight:600;font-size:.95rem;border:none;cursor:pointer;background:var(--primary);color:#fff;transition:all var(--transition)" onmouseover="this.style.background='var(--primary-dark)';this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 30px rgba(37,99,235,.4)'" onmouseout="this.style.background='var(--primary)';this.style.transform='';this.style.boxShadow=''">
          🛒 Add to Cart
        </button>
      @else
        <button onclick="openRfqModal({{ $product->id }}, '{{ addslashes($product->name) }}')" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:50px;font-weight:700;font-size:.95rem;border:none;cursor:pointer;background:#7c3aed;color:#fff;transition:all 0.2s;" onmouseover="this.style.background='#6d28d9';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#7c3aed';this.style.transform=''">
          📋 Request Quote
        </button>
      @endif
      <a href="{{ route('buyer.products.index') }}" style="display:inline-flex;align-items:center;gap:8px;padding:14px 24px;border-radius:50px;font-weight:600;font-size:.95rem;border:2px solid var(--border);color:var(--text);text-decoration:none;transition:all var(--transition)" onmouseover="this.style.borderColor='var(--text)'" onmouseout="this.style.borderColor='var(--border)'">
        &larr; Back
      </a>
    </div>
  </div>
</div>

<div style="padding:40px 0 60px;border-top:1px solid var(--border)">
  <h2 style="font-size:1.3rem;margin:0 0 24px">You May Also Like</h2>
  @if($related->count() > 0)
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px">
    @foreach($related as $item)
      <a href="{{ route('buyer.products.show', $item->id) }}" style="border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;cursor:pointer;text-decoration:none;color:inherit;transition:transform var(--transition),box-shadow var(--transition)" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
        <div style="aspect-ratio:1;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;font-size:2.5rem;padding:12px">
          @if($item->image_path)
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" style="width:100%;height:100%;object-fit:contain">
          @else
            <i data-lucide="package" style="width:36px;height:36px;color:#cbd5e1"></i>
          @endif
        </div>
        <div style="padding:12px">
          <h4 style="font-size:.88rem;margin:0 0 4px">{{ $item->name }}</h4>
          <div style="font-weight:700;font-size:.95rem">&#8358;{{ $item->unit_price && $item->unit_price !== 'Request quote' ? number_format((float) $item->unit_price) : 'Request Quote' }}</div>
        </div>
      </a>
    @endforeach
  </div>
  @else
  <p style="color:var(--text-muted);font-size:.9rem">No related products found.</p>
  @endif
</div>

<div style="padding:40px 0 60px;border-top:1px solid var(--border)">
  <h2 style="font-size:1.3rem;margin-bottom:24px">Customer Reviews</h2>

  @if($reviews->count() > 0)
  <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
    <span style="font-size:2rem;font-weight:700">{{ number_format($avgRating, 1) }}</span>
    <span style="font-size:1.2rem;color:var(--accent);letter-spacing:2px">
      @for($i = 1; $i <= 5; $i++)
        @if($i <= round($avgRating))&#9733;@else&#9734;@endif
      @endfor
    </span>
    <span style="font-size:.88rem;color:var(--text-muted)">({{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }})</span>
  </div>

  @foreach($reviews as $review)
  <div style="padding:20px 0;border-bottom:1px solid var(--border)">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
      <div style="width:36px;height:36px;border-radius:50%;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:600;color:var(--text-muted)">{{ strtoupper(substr($review->user->name ?? 'A', 0, 2)) }}</div>
      <span style="font-weight:600;font-size:.9rem">{{ $review->user->name ?? 'Anonymous' }}</span>
      <span style="font-size:.8rem;color:var(--text-muted);margin-left:auto">{{ $review->created_at->format('M j, Y') }}</span>
    </div>
    <div style="font-size:.95rem;color:var(--accent);letter-spacing:2px;margin-bottom:4px">
      @for($i = 1; $i <= 5; $i++)
        @if($i <= $review->rating)&#9733;@else&#9734;@endif
      @endfor
    </div>
    @if($review->comment)
    <div style="font-size:.9rem;color:var(--text-muted);line-height:1.6">{{ $review->comment }}</div>
    @endif
  </div>
  @endforeach
  @else
  <p style="color:var(--text-muted);font-size:.9rem">No reviews yet for this product.</p>
  @endif
</div>

<!-- Toast -->
<div class="toast" id="toast" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:#fff;padding:14px 28px;border-radius:50px;font-weight:500;font-size:.92rem;opacity:0;transition:all .35s cubic-bezier(.22,1,.36,1);z-index:300;pointer-events:none"></div>

<script>
let currentQty = 1;

function changeQty(delta) {
  currentQty = Math.max(1, currentQty + delta);
  document.getElementById('qtyDisplay').textContent = currentQty;
}

function addToCart(id, name, price) {
  const existing = cart.find(c => c.id === id);
  if (existing) {
    existing.qty += currentQty;
  } else {
    cart.push({ id: String(id), name, price, qty: currentQty });
  }
  currentQty = 1;
  document.getElementById('qtyDisplay').textContent = 1;
  updateCartUI();
  showToast(name + ' added to cart');
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
</script>
@endsection
