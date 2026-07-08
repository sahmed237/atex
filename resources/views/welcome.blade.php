@extends('layouts.landing')

@section('styles')
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
  --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
  --shadow-lg: 0 10px 25px rgba(0,0,0,.1);
  --transition: .25s ease;
}
html { scroll-behavior: smooth; }
body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; color: var(--text); background: var(--bg); line-height: 1.6; }
a { color: inherit; text-decoration: none; }
img { max-width: 100%; display: block; }
button { cursor: pointer; font: inherit; border: none; background: none; }
.container { max-width: 1286px; margin: 0 auto; padding: 0 24px; }
.section { padding: 80px 0; }
.section-label { display: inline-block; font-size: .8rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--primary); margin-bottom: 8px; }
.section-title { font-size: 2.25rem; font-weight: 700; margin-bottom: 12px; letter-spacing: -.02em; }
.section-sub { color: var(--text-muted); font-size: 1.1rem; max-width: 560px; }

/* ─── HERO CAROUSEL ─── */
.hero-carousel { position: relative; overflow: hidden; }
.hero-carousel .hero-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 5; width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,.15); border: 1px solid rgba(255,255,255,.12); color: #fff; font-size: 1.3rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all var(--transition); backdrop-filter: blur(4px); }
.hero-carousel .hero-arrow:hover { background: rgba(0,0,0,.3); }
.hero-carousel .hero-arrow.prev { left: 16px; }
.hero-carousel .hero-arrow.next { right: 16px; }
.hero-carousel .hero-arrow.light { color: var(--text); background: rgba(255,255,255,.7); }
.hero-carousel .hero-arrow.light:hover { background: rgba(255,255,255,.9); }
.hero-track { display: flex; transition: transform .6s cubic-bezier(.22,1,.36,1); }
.hero-track > * { min-width: 100%; }
.hero-controls { position: absolute; bottom: 24px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 5; }
.hero-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,.35); border: none; cursor: pointer; transition: all var(--transition); }
.hero-dot.active { background: #fff; width: 28px; border-radius: 5px; }
.hero-dot.dark { background: rgba(0,0,0,.2); }
.hero-dot.dark.active { background: var(--primary); }
@media (max-width: 820px) { .hero-carousel .hero-arrow { display: none; } .hero-controls { bottom: 16px; } }

/* ─── HERO ─── */
.hero { padding: 120px 0 100px; color: var(--text); overflow: hidden; position: relative; }
.hero::before { content: ''; position: absolute; top: -50%; right: -20%; width: 700px; height: 700px; background: radial-gradient(circle, rgba(37,99,235,.08) 0%, transparent 70%); pointer-events: none; }
.hero::after { content: ''; position: absolute; bottom: -30%; left: -10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(22,163,74,.06) 0%, transparent 70%); pointer-events: none; }
.hero .container { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; position: relative; z-index: 1; }
.hero h1 { font-size: 3.5rem; font-weight: 800; line-height: 1.1; letter-spacing: -.03em; margin-bottom: 20px; }
.hero h1 span { background: linear-gradient(135deg, #2563eb, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero p { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 32px; max-width: 500px; line-height: 1.7; }
.hero-stats { display: flex; gap: 40px; margin-bottom: 36px; }
.hero-stats div { text-align: left; }
.hero-stats strong { font-size: 1.8rem; font-weight: 800; display: block; line-height: 1.2; }
.hero-stats strong em { font-style: normal; color: #16a34a; }
.hero-stats span { font-size: .82rem; color: var(--text-muted); }
.hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; border-radius: 50px; font-weight: 600; font-size: .95rem; transition: all var(--transition); }
.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 8px 30px rgba(37,99,235,.4); }
.btn-outline { border: 2px solid rgba(0,0,0,.15); color: var(--text); }
.btn-outline:hover { border-color: rgba(0,0,0,.4); background: rgba(0,0,0,.04); }
.btn-green { background: var(--green); color: #fff; }
.btn-green:hover { background: #15803d; transform: translateY(-2px); box-shadow: 0 8px 30px rgba(22,163,74,.4); }
.hero-image { background: rgba(255,255,255,.6); backdrop-filter: blur(4px); border: 1px solid rgba(0,0,0,.06); border-radius: 20px; overflow: hidden; aspect-ratio: 4/3; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; }
.hero-image-icon { font-size: 4rem; margin-bottom: 20px; }
.hero-image h3 { font-size: 1rem; color: var(--text); margin-bottom: 8px; }
.hero-image p { font-size: .8rem; color: var(--text-muted); margin: 0; max-width: 280px; }
.hero-image-routes { display: flex; gap: 8px; margin-top: 20px; flex-wrap: wrap; justify-content: center; }
.hero-image-routes span { padding: 4px 14px; border-radius: 50px; background: rgba(255,255,255,.5); border: 1px solid rgba(0,0,0,.06); font-size: .75rem; color: var(--text-muted); }
@media (max-width: 820px) { .hero { padding: 80px 0 60px; } .hero .container { grid-template-columns: 1fr; text-align: center; } .hero p { margin: 0 auto 32px; } .hero-stats { justify-content: center; } .hero-stats div { text-align: center; } .hero-btns { justify-content: center; } .hero h1 { font-size: 2.5rem; } }

/* ─── LOCAL HERO ─── */
.local-hero { padding: 120px 0 100px; background: linear-gradient(135deg, #fefce8 0%, #ecfdf5 50%, #eff6ff 100%); position: relative; overflow: hidden; }
.local-hero .container { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
.local-hero-label { display: inline-block; font-size: .8rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--green); margin-bottom: 8px; }
.local-hero h2 { font-size: 3.5rem; font-weight: 800; line-height: 1.1; letter-spacing: -.03em; margin-bottom: 20px; color: var(--text); }
.local-hero h2 span { color: var(--primary); }
.local-hero p { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 32px; max-width: 500px; line-height: 1.7; }
.local-hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 36px; }
.local-hero-grid .item { display: flex; align-items: center; gap: 10px; font-size: .92rem; font-weight: 500; }
.local-hero-grid .item .icon { width: 36px; height: 36px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.local-hero-image { background: rgba(255,255,255,.7); backdrop-filter: blur(4px); border: 1px solid rgba(0,0,0,.06); border-radius: 20px; overflow: hidden; aspect-ratio: 4/3; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; box-shadow: var(--shadow-lg); }
.local-hero-image .big-icon { font-size: 4rem; margin-bottom: 16px; }
.local-hero-image h3 { font-size: 1.05rem; margin-bottom: 6px; color: var(--text); }
.local-hero-image p { font-size: .82rem; color: var(--text-muted); margin: 0; max-width: 260px; }
.local-hero-impacts { display: flex; gap: 24px; margin-top: 20px; }
.local-hero-impacts span { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.local-hero-impacts strong { font-size: 1.4rem; font-weight: 800; color: var(--green); }
.local-hero-impacts small { font-size: .7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
@media (max-width: 820px) { .local-hero { padding: 60px 0; } .local-hero .container { grid-template-columns: 1fr; text-align: center; } .local-hero p { margin: 0 auto 28px; } .local-hero-grid { justify-content: center; } .local-hero-grid .item { justify-content: center; } .local-hero-btns { justify-content: center; } .local-hero h2 { font-size: 2rem; } .local-hero-impacts { justify-content: center; } }

/* ─── HOW IT WORKS ─── */
.how-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; margin-top: 48px; }
.how-step { text-align: center; padding: 40px 24px; border-radius: var(--radius); background: var(--bg-alt); border: 1px solid var(--border); position: relative; }
.how-step .step-num { width: 48px; height: 48px; margin: 0 auto 16px; background: var(--primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; }
.how-step h3 { font-size: 1.1rem; margin-bottom: 8px; }
.how-step p { font-size: .9rem; color: var(--text-muted); }
@media (max-width: 720px) { .how-grid { grid-template-columns: 1fr; } }

/* ─── PRODUCTS ─── */
.products-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 40px; }
.filter-btns { display: flex; gap: 8px; flex-wrap: wrap; overflow: hidden; max-height: 88px; transition: max-height 0.3s ease; align-items: center; }
.filter-btns.expanded { max-height: 2000px; }
.filter-btn { padding: 8px 20px; border-radius: 50px; font-size: .88rem; font-weight: 500; border: 1px solid var(--border); color: var(--text-muted); transition: all var(--transition); }
.filter-btn.active, .filter-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 24px; }
.product-card { border-radius: var(--radius); background: var(--bg); border: 1px solid var(--border); overflow: hidden; transition: transform var(--transition), box-shadow var(--transition); display: flex; flex-direction: column; }
.product-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.product-img { aspect-ratio: 1; background: var(--bg-alt); display: flex; align-items: center; justify-content: center; font-size: 3.5rem; position: relative; flex-shrink: 0; }
.product-tag { position: absolute; top: 12px; left: 12px; padding: 4px 12px; border-radius: 50px; font-size: .75rem; font-weight: 600; background: var(--accent); color: #fff; }
.product-tag.sale { background: #ef4444; }
.product-tag.green { background: var(--green); }
.product-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
.product-body h3 { font-size: 1.05rem; margin: 4px 0 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.product-stars { font-size: .82rem; color: var(--accent); letter-spacing: 1px; margin-bottom: 6px; }
.product-moq { font-size: .8rem; color: var(--text-muted); margin-bottom: 8px; }
.product-price { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; margin-top: auto; }
.product-price .current { font-size: 1.2rem; font-weight: 700; }
.product-price .old { font-size: .92rem; color: var(--text-muted); text-decoration: line-through; }
.add-to-cart { width: 100%; padding: 12px; border-radius: 50px; background: var(--text); color: #fff; font-weight: 600; font-size: .9rem; transition: background var(--transition); }
.add-to-cart:hover { background: var(--primary); }
@media (max-width: 820px) { .section-title { font-size: 1.75rem; } }

/* ─── IMPORT SECTION ─── */
.import-section { background: var(--bg-alt); }
.import-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; margin-top: 48px; }
.import-layout h3 { font-size: 1.3rem; margin-bottom: 12px; }
.import-layout > div p { color: var(--text-muted); margin-bottom: 24px; }
.import-preview-item span:last-child { color: var(--green); font-weight: 600; }
@media (max-width: 820px) { .import-layout { grid-template-columns: 1fr; } }

/* ─── TESTIMONIALS ─── */
.testimonials { background: var(--bg-alt); }
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 48px; }
.testimonial-card { background: var(--bg); padding: 32px; border-radius: var(--radius); border: 1px solid var(--border); }
.testimonial-company { font-size: .78rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; }
.testimonial-card blockquote { font-size: .95rem; color: var(--text-muted); margin-bottom: 20px; font-style: italic; }
.testimonial-author { display: flex; align-items: center; gap: 12px; }
.testimonial-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--border); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-muted); }
.testimonial-author strong { font-size: .92rem; }
.testimonial-author span { font-size: .82rem; color: var(--text-muted); }
@media (max-width: 820px) { .testimonials-grid { grid-template-columns: 1fr; } }

/* ─── NEWSLETTER ─── */
.newsletter { text-align: center; background: linear-gradient(135deg, #1e3a5f, #0f172a); color: #fff; }
.newsletter .section-sub { color: rgba(255,255,255,.7); margin: 0 auto 32px; }
.newsletter .section-title { color: #fff; }
.newsletter-form { display: flex; max-width: 480px; margin: 0 auto; gap: 0; }
.newsletter-form input { flex: 1; padding: 16px 20px; border: none; border-radius: 50px 0 0 50px; font-size: .95rem; outline: none; }
.newsletter-form button { padding: 16px 32px; background: var(--primary); color: #fff; font-weight: 600; font-size: .95rem; border-radius: 0 50px 50px 0; transition: background var(--transition); }
.newsletter-form button:hover { background: var(--primary-dark); }
@media (max-width: 500px) { .newsletter-form { flex-direction: column; gap: 8px; } .newsletter-form input, .newsletter-form button { border-radius: 50px; } }

/* ─── FOOTER ─── */
footer { background: var(--text); color: rgba(255,255,255,.6); padding: 60px 0 32px; }
footer .container { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; }
footer h4 { color: #fff; font-size: 1rem; margin-bottom: 16px; }
footer p { font-size: .9rem; max-width: 320px; }
footer ul { list-style: none; }
footer ul li { margin-bottom: 10px; }
footer ul a { font-size: .9rem; transition: color var(--transition); }
footer ul a:hover { color: #fff; }
.footer-bottom { grid-column: 1 / -1; border-top: 1px solid rgba(255,255,255,.1); padding-top: 24px; margin-top: 16px; font-size: .85rem; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
@media (max-width: 720px) { footer .container { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { footer .container { grid-template-columns: 1fr; } }

/* ─── CART SIDEBAR ─── */
.cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.4); opacity: 0; pointer-events: none; transition: opacity var(--transition); z-index: 200; }
.cart-overlay.open { opacity: 1; pointer-events: auto; }
.cart-sidebar { position: fixed; top: 0; right: 0; width: 420px; max-width: 90vw; height: 100vh; background: var(--bg); z-index: 201; transform: translateX(100%); transition: transform .35s cubic-bezier(.22,1,.36,1); display: flex; flex-direction: column; }
.cart-sidebar.open { transform: translateX(0); }
.cart-header { display: flex; justify-content: space-between; align-items: center; padding: 24px; border-bottom: 1px solid var(--border); }
.cart-header h2 { font-size: 1.25rem; }
.cart-close { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--bg-alt); font-size: 1.2rem; transition: background var(--transition); }
.cart-close:hover { background: var(--border); }
.cart-items { flex: 1; overflow-y: auto; padding: 16px 24px; }
.cart-item { display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--border); }
.cart-item-img { width: 64px; height: 64px; border-radius: 8px; background: var(--bg-alt); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.cart-item-info { flex: 1; }
.cart-item-info h4 { font-size: .92rem; }
.cart-item-info p { font-size: .88rem; color: var(--text-muted); }
.cart-item-qty { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.cart-item-qty button { width: 28px; height: 28px; border-radius: 50%; background: var(--bg-alt); font-weight: 600; transition: background var(--transition); }
.cart-item-qty button:hover { background: var(--border); }
.cart-item-qty span { font-weight: 600; min-width: 20px; text-align: center; }
.cart-item-remove { color: var(--text-muted); font-size: .82rem; align-self: flex-start; padding: 4px; }
.cart-item-remove:hover { color: #ef4444; }
.cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); text-align: center; padding: 40px; }
.cart-empty svg { width: 64px; height: 64px; margin-bottom: 16px; opacity: .3; }
.cart-footer { padding: 24px; border-top: 1px solid var(--border); position: relative; }
.cart-total { display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; }
.checkout-btn { width: 100%; padding: 16px; border-radius: 50px; background: var(--text); color: #fff; font-weight: 600; font-size: 1rem; transition: background var(--transition); }
.checkout-btn:hover { background: var(--primary); }
.view-cart-link { display: block; text-align: center; padding: 12px; font-size: .88rem; color: var(--primary); font-weight: 500; }
.view-cart-link:hover { text-decoration: underline; }
.export-btn { width: 100%; padding: 12px; border-radius: 50px; background: var(--green); color: #fff; font-weight: 600; font-size: .88rem; border: none; transition: all var(--transition); margin-bottom: 8px; }
.export-btn:hover { background: #15803d; }
.export-popup { display: none; position: absolute; bottom: 100%; left: 0; right: 0; margin-bottom: 8px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-lg); overflow: hidden; }
.export-popup.open { display: block; }
.export-popup button { width: 100%; padding: 12px 20px; text-align: left; font-size: .88rem; background: none; transition: background var(--transition); border-bottom: 1px solid var(--border); }
.export-popup button:last-child { border-bottom: none; }
.export-popup button:hover { background: var(--bg-alt); }
.export-popup .popup-label { display: block; font-size: .68rem; color: var(--text-muted); font-weight: 400; margin-top: 2px; }

/* ─── TOAST ─── */
.toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: var(--text); color: #fff; padding: 14px 28px; border-radius: 50px; font-weight: 500; font-size: .92rem; opacity: 0; transition: all .35s cubic-bezier(.22,1,.36,1); z-index: 300; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')

<!-- ═══ GEOIP / LOCALIZATION BANNER ═══ -->
<div style="background: @if(session('user_country', 'NG') != 'NG') #1e3a8a @else #14532d @endif; color: #fff; padding: 10px 24px; text-align: center; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;">
  @if(session('user_country', 'NG') != 'NG')
    <span>🌍 International Visitor Detected: Showing <strong>Export-Ready Catalog</strong> for <strong>{{ session('user_country_name', 'International') }}</strong>. Prices displayed in <strong>{{ session('user_currency', 'USD') }}</strong>.</span>
  @else
    <span>🇳🇬 Welcome to Adamawa Export! Showing <strong>Domestic & Export Marketplace</strong> for <strong>Nigeria</strong>. Prices displayed in <strong>₦ NGN</strong>.</span>
  @endif
</div>

<!-- ═══ HERO CAROUSEL ═══ -->
<section class="hero-carousel">
  <button class="hero-arrow prev" onclick="slideTo(current - 1)">‹</button>
  <button class="hero-arrow next" onclick="slideTo(current + 1)">›</button>
  <div class="hero-track" id="heroTrack">

    <section class="local-hero" id="local">
      <div class="container">
        <div>
          <span class="local-hero-label">Local Empowerment</span>
          <h2>Import & Sell.<br><span>Empower Your Community.</span></h2>
          <p>Bridge the gap between global supply chains and local markets. Source quality products from around the world and sell them right in your neighborhood — creating jobs, opportunity, and economic independence.</p>
          <div class="local-hero-grid">
            <div class="item"><div class="icon">🌱</div> Support Local Vendors</div>
            <div class="item"><div class="icon">🤝</div> Fair Trade Sourcing</div>
            <div class="item"><div class="icon">🏘️</div> Community First</div>
            <div class="item"><div class="icon">♻️</div> Sustainable Practices</div>
          </div>
          <div class="hero-btns local-hero-btns">
            @auth
              <a href="{{ auth()->user()->hasRole('seller') ? route('kyc.onboarding') : route('seller.onboarding') }}" class="btn btn-primary">Start Selling →</a>
            @else
              <a href="{{ route('register') }}" class="btn btn-primary">Start Selling →</a>
            @endauth
            <a href="#how" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">Learn More</a>
          </div>
        </div>
        <div class="local-hero-image">
          <div class="big-icon">🏪</div>
          <h3>Local Shops, Global Reach</h3>
          <p>Businesses using Adamawa Export have grown their revenue by an average of 340% by reaching international buyers directly.</p>
        </div>
      </div>
    </section>

    <section class="hero" id="hero" style="background: linear-gradient(135deg, #fefce8 0%, #ecfdf5 50%, #eff6ff 100%);">
      <div class="container">
        <div>
          <span class="section-label" style="color:var(--accent)">Adamawa Export Platform</span>
          <h1>Source Products.<br>Export <span>Worldwide.</span></h1>
          <p>Connect with verified suppliers across Adamawa, import quality goods, and export to buyers in 60+ countries. All from one dashboard.</p>
          <div class="hero-stats">
            <div><strong>{{ number_format($productCount) }}<em>+</em></strong><span>Products Listed</span></div>
            <div><strong>60<em>+</em></strong><span>Export Countries</span></div>
            <div><strong>2.4K<em>+</em></strong><span>Trade Partners</span></div>
          </div>
          <div class="hero-btns">
            <a href="{{ route('buyer.products.index') }}" class="btn btn-primary">Browse Catalog →</a>
            <a href="#import" class="btn btn-outline">Import Products</a>
          </div>
        </div>
        <div class="hero-image">
          <div class="hero-image-icon">🌍</div>
          <h3>End-to-End Trade Platform</h3>
          <p>Import from suppliers, manage inventory, and export orders with automated trade documentation.</p>
          <div class="hero-image-routes">
            <span>🇨🇳 China</span>
            <span>🇮🇳 India</span>
            <span>🇩🇪 Germany</span>
            <span>🇺🇸 USA</span>
            <span>🇻🇳 Vietnam</span>
          </div>
        </div>
      </div>
    </section>

  </div>
  <div class="hero-controls" id="heroControls"></div>
</section>

<!-- ═══ PRODUCTS / CATALOG ═══ -->
<section class="section" id="catalog">
  <div class="container">
    <div class="products-header">
      <div>
        <span class="section-label">Featured Products</span>
        <h2 class="section-title">Browse Our Collection</h2>
      </div>
      <div class="filter-btns">
        <button class="filter-btn active" data-category="all">All</button>
        <div id="catFilterExtra" style="display:contents;">
          @foreach($sharedCategories as $cat)
            <button class="filter-btn" data-category="{{ $cat->slug }}">{{ $cat->name }}</button>
          @endforeach
        </div>
      </div>
      <button type="button" id="catFilterToggle" onclick="toggleCatFilters()" style="background:none; border:none; color:var(--primary); font-size:0.8rem; font-weight:600; cursor:pointer; padding:4px 0; white-space:nowrap;">Show more ▾</button>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:24px; align-items:center; background:var(--bg-alt); padding:12px 16px; border-radius:12px; border:1px solid var(--border);">
      <span style="font-size:0.8rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-right:4px;">📍 Origin LGA:</span>
      <button class="origin-pill active" onclick="filterOrigin('all', this)" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:var(--primary); color:#fff; font-weight:700; font-size:0.8rem; cursor:pointer;">All Adamawa</button>
      <button class="origin-pill" onclick="filterOrigin('Mubi', this)" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:var(--card-bg); color:var(--text); font-weight:700; font-size:0.8rem; cursor:pointer;">🌾 Mubi North</button>
      <button class="origin-pill" onclick="filterOrigin('Yola', this)" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:var(--card-bg); color:var(--text); font-weight:700; font-size:0.8rem; cursor:pointer;">🥜 Yola South</button>
      <button class="origin-pill" onclick="filterOrigin('Numan', this)" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:var(--card-bg); color:var(--text); font-weight:700; font-size:0.8rem; cursor:pointer;">🍯 Numan</button>
      <button class="origin-pill" onclick="filterOrigin('Guyuk', this)" style="padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:var(--card-bg); color:var(--text); font-weight:700; font-size:0.8rem; cursor:pointer;">🪨 Guyuk</button>
    </div>
    <div class="products-grid" id="productsGrid"></div>
    <div style="text-align:center;margin-top:40px">
      <a href="{{ route('buyer.products.index') }}" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">View All Products →</a>
    </div>
  </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="section" id="how">
  <div class="container">
    <div style="text-align:center">
      <span class="section-label">How It Works</span>
      <h2 class="section-title">From Sourcing to Shipping</h2>
      <p class="section-sub" style="margin:0 auto">A simple three-step workflow for global trade.</p>
    </div>
    <div class="how-grid">
      <div class="how-step">
        <div class="step-num">1</div>
        <h3>📦 Import Products</h3>
        <p>Upload supplier catalogs via CSV or add products manually. Quality-check and set your pricing.</p>
      </div>
      <div class="how-step">
        <div class="step-num">2</div>
        <h3>🛒 Build Your Order</h3>
        <p>Browse the catalog, select quantities, and build an export order. Real-time pricing and MOQ validation.</p>
      </div>
      <div class="how-step">
        <div class="step-num">3</div>
        <h3>📄 Export &amp; Ship</h3>
        <p>Generate commercial invoices and packing lists from the cart. Download order docs for customs clearance.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ IMPORT SECTION ═══ -->
<section class="section import-section" id="import">
  <div class="container">
    <div style="text-align:center">
      <span class="section-label">Overseas Import</span>
      <h2 class="section-title">Source From Global Markets</h2>
      <p class="section-sub" style="margin:0 auto">Import goods directly from manufacturers and suppliers across Asia, Europe, the Americas, and Africa.</p>
    </div>
    <div class="import-layout">
      <div>
        <h3>What We Cover</h3>
        <div style="display:flex;flex-direction:column;gap:20px;margin-top:20px">
          <div style="display:flex;gap:16px;align-items:flex-start">
            <div style="width:44px;height:44px;border-radius:10px;background:#eff6ff;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0">🏭</div>
            <div>
              <strong>Manufacturer Sourcing</strong>
              <p style="color:var(--text-muted);font-size:.88rem;margin-top:2px">Connect directly with vetted factories and producers in China, India, Vietnam, Turkey, and more. No middlemen.</p>
            </div>
          </div>
          <div style="display:flex;gap:16px;align-items:flex-start">
            <div style="width:44px;height:44px;border-radius:10px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0">📋</div>
            <div>
              <strong>Customs & Documentation</strong>
              <p style="color:var(--text-muted);font-size:.88rem;margin-top:2px">Automatically generate commercial invoices, packing lists, and certificates of origin for smooth customs clearance.</p>
            </div>
          </div>
          <div style="display:flex;gap:16px;align-items:flex-start">
            <div style="width:44px;height:44px;border-radius:10px;background:#f0fdf4;color:var(--green);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0">🚢</div>
            <div>
              <strong>Shipping & Logistics</strong>
              <p style="color:var(--text-muted);font-size:.88rem;margin-top:2px">Access consolidated freight options — sea, air, or rail. Real-time tracking from factory to your warehouse.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIALS ═══ -->
<section class="section testimonials" id="testimonials">
  <div class="container">
    <div style="text-align:center">
      <span class="section-label">Trade Partners</span>
      <h2 class="section-title">Trusted by Global Businesses</h2>
      <p class="section-sub" style="margin:0 auto">Join thousands of companies that trade on our platform.</p>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="testimonial-company">Nova Imports Ltd.</div>
        <blockquote>"We import raw materials from 12 countries. This platform cut our documentation time by 70%. The export docs from the cart are a game changer."</blockquote>
        <div class="testimonial-author">
          <div class="testimonial-avatar">RK</div>
          <div><strong>Ravi Kapoor</strong><br><span>Procurement Director</span></div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-company">Bergen Exports AS</div>
        <blockquote>"Exporting Scandinavian furniture to Asia requires precise paperwork. The built-in invoice and packing list generation saves us hours per shipment."</blockquote>
        <div class="testimonial-author">
          <div class="testimonial-avatar">LH</div>
          <div><strong>Lars Hansen</strong><br><span>Export Manager</span></div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-company">Sahara Traders</div>
        <blockquote>"From sourcing textiles in India to exporting finished goods to Europe — this platform replaced three separate tools. Highly recommended."</blockquote>
        <div class="testimonial-author">
          <div class="testimonial-avatar">FA</div>
          <div><strong>Fatima Al-Rashid</strong><br><span>CEO, Sahara Traders</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ NEWSLETTER ═══ -->
<section class="section newsletter" id="contact">
  <div class="container">
    <span class="section-label" style="color:var(--accent)">Stay Informed</span>
    <h2 class="section-title">Get Trade Alerts & Market Insights</h2>
    <p class="section-sub">Weekly updates on commodity prices, trade routes, and new supplier listings.</p>
    <form class="newsletter-form" onsubmit="handleNewsletter(event)">
      <input type="email" placeholder="Enter your business email" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</section>



<!-- ═══ CART OVERLAY & SIDEBAR ═══ -->
<div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
<aside class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <h2>Export Order</h2>
    <button class="cart-close" onclick="toggleCart()">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <p>Your export order is empty</p>
      <p style="font-size:.82rem;margin-top:4px">Add products from the catalog to start building an export order.</p>
    </div>
  </div>
  <div class="cart-footer">
    <div class="cart-total"><span>Total (FOB)</span><span id="cartTotal">$0.00</span></div>
    <button class="export-btn" onclick="toggleExport()">📄 Export Documents ▾</button>
    <div class="export-popup" id="exportPopup">
      <button onclick="exportOrder('invoice')">🧾 Commercial Invoice<span class="popup-label">For customs clearance and payment</span></button>
      <button onclick="exportOrder('packing')">📦 Packing List<span class="popup-label">Itemized shipment manifest</span></button>
      <button onclick="exportOrder('summary')">📋 Order Summary<span class="popup-label">Full order details for your records</span></button>
    </div>
    <a href="{{ route('buyer.cart.index') }}" class="view-cart-link">View Full Cart →</a>
    <button class="checkout-btn" onclick="handleCheckout()">Confirm Export Order</button>
  </div>
</aside>

<!-- ═══ TOAST ═══ -->
<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
// ─── HERO CAROUSEL ───
var track = document.getElementById('heroTrack');
var slides = track ? track.children : [];
var controls = document.getElementById('heroControls');
var current = 0;
var autoplay;

function buildDots() {
  if (!controls) return;
  controls.innerHTML = '';
  for (var i = 0; i < slides.length; i++) {
    var dot = document.createElement('button');
    dot.className = 'hero-dot' + (i === 0 ? ' active' : '');
    dot.onclick = (function(idx) { return function() { slideTo(idx); }; })(i);
    controls.appendChild(dot);
  }
}

function slideTo(index) {
  if (!track || slides.length === 0) return;
  var total = slides.length;
  current = ((index % total) + total) % total;
  track.style.transform = 'translateX(-' + (current * 100) + '%)';
  if (controls) {
    Array.from(controls.children).forEach(function(d, i) { d.classList.toggle('active', i === current); });
  }
  resetAutoplay();
}

function resetAutoplay() {
  clearInterval(autoplay);
  autoplay = setInterval(function() { slideTo(current + 1); }, 5000);
}

buildDots();
resetAutoplay();

// ─── PRODUCT DATA ───
var isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
var products = typeof window.marketplaceProducts !== 'undefined' && window.marketplaceProducts.length
  ? window.marketplaceProducts
  : [];

var catalog = products.slice();
var cart = JSON.parse(localStorage.getItem('gt_cart') || localStorage.getItem('atex_cart') || '[]');
var currentFilter = 'all';
var currentOrigin = 'all';

function filterOrigin(orig, btnEl) {
  currentOrigin = orig;
  document.querySelectorAll('.origin-pill').forEach(function(b) {
    b.style.background = 'var(--card-bg)';
    b.style.color = 'var(--text)';
  });
  if (btnEl) {
    btnEl.style.background = 'var(--primary)';
    btnEl.style.color = '#fff';
  }
  renderProducts();
}

function toggleCatFilters() {
  var el = document.querySelector('.filter-btns');
  var btn = document.getElementById('catFilterToggle');
  el.classList.toggle('expanded');
  btn.textContent = el.classList.contains('expanded') ? 'Show less ▴' : 'Show more ▾';
}
function renderProducts() {
  var grid = document.getElementById('productsGrid');
  if (!grid) return;
  var filtered = catalog.filter(function(p) {
    var catMatch = currentFilter === 'all' || p.category === currentFilter;
    var origMatch = currentOrigin === 'all' || (p.origin && p.origin.indexOf(currentOrigin) > -1);
    return catMatch && origMatch;
  });
  var saved = window.atexSaved || JSON.parse(localStorage.getItem('atex_saved') || '[]');
  var compare = JSON.parse(localStorage.getItem('atex_compare') || '[]');
  
  grid.innerHTML = filtered.map(function(p) {
    var stars = '';
    for (var s = 0; s < 5; s++) { stars += s < p.rating ? '★' : '☆'; }
    var tagHtml = p.tag ? '<span class="product-tag ' + (p.tag === 'Sale' ? 'sale' : '') + '">' + p.tag + '</span>' : '';
    var priceNum = parseFloat(p.price) || 0;
    var currentPriceStr = priceNum > 0 ? '₦' + priceNum.toLocaleString() : 'Request Quote';
    var oldPriceHtml = p.oldPrice ? '<span class="old">₦' + parseFloat(p.oldPrice).toLocaleString() + '</span>' : '';
    var badgeHtml = p.type === 'export'
      ? '<div style="font-size:0.7rem; font-weight:800; color:#7c3aed; background:#f3e8ff; padding:2px 6px; border-radius:4px; display:inline-block; margin-bottom:4px;">🌍 Export</div>'
      : '<div style="font-size:0.7rem; font-weight:800; color:#166534; background:#dcfce7; padding:2px 6px; border-radius:4px; display:inline-block; margin-bottom:4px;">📍 Local</div>';
    
    var isSaved = saved.includes(p.id);
    var heartHtml = '<button onclick="event.stopPropagation(); toggleWatchlist(' + p.id + ', this)" data-watchlist-id="' + p.id + '" style="position:absolute; top:8px; right:8px; background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; width:32px; height:32px; font-size:1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.15); z-index:2;" title="Save to Watchlist">' + (isSaved ? '❤️' : '🤍') + '</button>';
    
    var isCompared = compare.some(function(c) { return c.id === p.id; });
    var compareHtml = '<label class="compare-label" ' + (isCompared ? 'style="opacity:1 !important; pointer-events:auto !important; transform:translateY(0);"' : '') + '><input type="checkbox" class="compare-chk" ' + (isCompared ? 'checked' : '') + ' onclick="toggleCompareItem(' + p.id + ', \'' + p.name.replace(/'/g,"\\'") + '\', ' + priceNum + ', \'' + p.moq + '\', \'' + p.origin + '\', this)"> ⚖️ Compare</label>';

    return '<div class="product-card" style="position:relative; cursor:pointer;" onclick="location.href=\'/products/' + p.id + '\'">' +
      '<div class="product-img" style="position:relative;">' + heartHtml + (p.emoji || '📦') + tagHtml +
      '<button onclick="openQuickView(' + p.id + ', \'' + p.name.replace(/'/g,"\\'") + '\', ' + priceNum + ', \'' + p.moq + '\', \'' + p.origin + '\', \'' + (p.type || 'local') + '\')" style="position:absolute; bottom:8px; right:8px; background:rgba(255,255,255,0.9); border:1px solid #cbd5e1; border-radius:50px; padding:4px 10px; font-size:0.75rem; font-weight:700; cursor:pointer; color:#0f172a; box-shadow:0 2px 5px rgba(0,0,0,0.1);">👁️ Quick View</button>' +
      '</div>' +
      '<div class="product-body">' +
      '<div style="overflow:hidden; margin-bottom:4px;">' + badgeHtml + compareHtml + '</div>' +
      '<div class="product-stars">' + stars + '</div>' +
      '<h3>' + p.name + '</h3>' +
      '<div class="product-moq">MOQ: ' + p.moq + ' · ' + p.origin + '</div>' +
      '<div class="product-price"><span class="current" data-price-ngn="' + priceNum + '">' + currentPriceStr + '</span>' + oldPriceHtml + '</div>' +
      '<div style="display:flex; gap:6px; margin-top:8px;">' +
      (p.type === 'local'
        ? '<button class="add-to-cart" onclick="addToCart(' + p.id + ')" style="flex:1;">Add to Cart</button>'
        : '<button class="add-to-cart" onclick="openRfqModal(' + p.id + ', \'' + p.name.replace(/'/g,"\\'") + '\')" style="flex:1; background:#7c3aed;">📋 Request Quote</button>') +
      '</div>' +
      '</div></div>';
  }).join('');
}

function addToCart(id) {
  var p = catalog.find(function(x) { return x.id === id; });
  if (!p) return;
  var existing = cart.find(function(x) { return x.id === id; });
  if (existing) { existing.qty++; }
  else { cart.push({ id: p.id, name: p.name, price: p.price, emoji: p.emoji, qty: 1 }); }
  localStorage.setItem('gt_cart', JSON.stringify(cart));
  localStorage.setItem('atex_cart', JSON.stringify(cart));
  updateCartUI();
  showToast(p.emoji + ' ' + p.name + ' added to export order');
}

function updateCartUI() {
  localStorage.setItem('gt_cart', JSON.stringify(cart));
  localStorage.setItem('atex_cart', JSON.stringify(cart));
  var count = cart.reduce(function(s, i) { return s + i.qty; }, 0);
  var cartCount = document.getElementById('cartCount');
  if (cartCount) { cartCount.textContent = count; cartCount.style.display = count > 0 ? 'flex' : 'none'; }
}

document.querySelectorAll('.filter-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    currentFilter = btn.dataset.category;
    renderProducts();
  });
});

renderProducts();

// ─── CART ───
function toggleCart() {
  document.getElementById('cartOverlay').classList.toggle('open');
  document.getElementById('cartSidebar').classList.toggle('open');
}

function toggleExport() { document.getElementById('exportPopup').classList.toggle('open'); }

function exportOrder(type) {
  toggleExport();
  showToast('📄 ' + type.charAt(0).toUpperCase() + type.slice(1) + ' document generated');
}

function handleCheckout() {
  var items = cart.filter(function(i) { return i.qty > 0; });
  if (items.length === 0) { showToast('Your export order is empty'); return; }
  showToast('Proceeding to checkout...');
  setTimeout(function() { window.location.href = '{{ route("buyer.cart.index") }}'; }, 1000);
}

function handleNewsletter(e) {
  e.preventDefault();
  var input = e.target.querySelector('input');
  if (input && input.value) { showToast('✅ Subscribed! Check your inbox for trade alerts.'); input.value = ''; }
}

// ─── TOAST ───
function showToast(msg) {
  var t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('visible');
  setTimeout(function() { t.classList.remove('visible'); }, 2500);
}

updateCartUI();
</script>
@endsection
