@extends('layouts.buyer')

@section('content')
<style>
:root {
  --primary: #2563eb;
  --accent: #f59e0b;
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

<div style="padding:24px 0">
  <a href="{{ route('buyer.products.index') }}" style="font-size:.88rem;color:var(--primary);text-decoration:none">&larr; Back to Products</a>
  <h1 style="font-size:1.5rem;font-weight:700;margin:12px 0 4px">All Categories</h1>
  <p style="color:var(--text-muted);font-size:.92rem;margin:0 0 24px">{{ $allCategories->count() }} categories — select multiple and apply to your product filter</p>

  <form action="{{ route('buyer.products.index') }}" method="GET" id="categoryForm">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:24px">
      @foreach($allCategories as $cat)
        <label style="display:flex;align-items:center;gap:10px;padding:14px;border-radius:var(--radius);border:1px solid var(--border);background:var(--bg);cursor:pointer;transition:border-color var(--transition),box-shadow var(--transition)"
               onmouseover="this.style.borderColor='var(--primary)';this.style.boxShadow='var(--shadow-lg)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow=''">
          <input type="checkbox" name="category[]" value="{{ $cat->slug }}"
                 style="width:18px;height:18px;accent-color:var(--primary);cursor:pointer;flex-shrink:0">
          <div>
            <div style="font-weight:600;font-size:.9rem">{{ $cat->name }}</div>
          </div>
        </label>
      @endforeach
    </div>
    <button type="submit" style="padding:12px 32px;border-radius:50px;background:var(--text);color:#fff;font-weight:600;font-size:.95rem;border:none;cursor:pointer;transition:background var(--transition)"
            onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--text)'">
      Apply Filters
    </button>
  </form>
</div>
@endsection
