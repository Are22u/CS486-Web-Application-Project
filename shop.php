
<?php
require_once __DIR__ . '/includes/init.php';

$productModel = new Product();
$products = $productModel->all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <script>document.documentElement.classList.add('js')</script>
  <meta charset="utf-8">
  <meta name="csrf-token" content="<?= e(csrfToken()) ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shop Bouquets &mdash; Fleur Florist</title>
  <meta name="description" content="Browse the full Fleur bouquet shop.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/vendor/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <nav class="site-nav scrolled" aria-label="Primary">
    <div class="container">
      <a class="nav-brand" href="index.php">Fleur<span class="mark">.</span></a>

      <ul class="nav-links" id="primary-menu">
        <li><a href="shop.php" class="active">Shop</a></li>
        <li><a href="index.php#occasions">Occasions</a></li>
        <li><a href="index.php#subscription">Subscriptions</a></li>
        <li><a href="index.php#story">Our Story</a></li>
        <li><a href="index.php#services">Services</a></li>
        <li><a href="index.php#delivery">Delivery</a></li>
      </ul>

      <div class="nav-actions">
        <button type="button" class="cart-pill" data-cart-open aria-label="Open shopping bag">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <path d="M3 6h18"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
          <span>Bag</span>
          <span class="cart-count" aria-live="polite">0</span>
        </button>

        <a href="index.php" class="btn btn-rose btn-sm d-none d-lg-inline-flex">Home</a>

        <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-menu" aria-label="Toggle menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </nav>
  <div class="nav-backdrop" aria-hidden="true"></div>
</header>

<main>
  <section class="page-head">
    <div class="container">
      <p class="breadcrumb-line"><a href="index.php">Home</a> &nbsp;/&nbsp; Shop</p>
      <h1>The Bouquet Shop</h1>
      <p class="section-lead" style="margin:1rem auto 0">
        Every bouquet is tied by hand the morning it ships. Filter by occasion, colour or price to find the one.
      </p>
    </div>
  </section>

  <section class="section" style="padding-top:3rem">
    <div class="container">
      <div class="shop-layout">

        <aside class="shop-filters" aria-label="Filter bouquets">
          <div class="d-flex justify-content-between align-items-center d-lg-none" style="margin-bottom:1.2rem">
            <strong style="font-family:var(--serif);font-size:1.4rem">Filters</strong>
            <button type="button" class="cart-close" data-filters-close aria-label="Close filters">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 6 6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div class="filter-group">
            <h4>Occasion</h4>
            <label class="filter-opt"><input type="checkbox" data-shop-occasion value="birthday"> Birthday</label>
            <label class="filter-opt"><input type="checkbox" data-shop-occasion value="anniversary"> Anniversary</label>
            <label class="filter-opt"><input type="checkbox" data-shop-occasion value="wedding"> Wedding</label>
            <label class="filter-opt"><input type="checkbox" data-shop-occasion value="sympathy"> Sympathy</label>
            <label class="filter-opt"><input type="checkbox" data-shop-occasion value="get-well"> Get Well</label>
            <label class="filter-opt"><input type="checkbox" data-shop-occasion value="just-because"> Just Because</label>
          </div>

          <div class="filter-group">
            <h4>Colour</h4>
            <label class="filter-opt"><input type="checkbox" data-shop-color value="pink"> <span class="swatch" style="background:#E8A9B6"></span> Pink</label>
            <label class="filter-opt"><input type="checkbox" data-shop-color value="red"> <span class="swatch" style="background:#A8323F"></span> Red</label>
            <label class="filter-opt"><input type="checkbox" data-shop-color value="white"> <span class="swatch" style="background:#F3EEE8"></span> White &amp; Ivory</label>
            <label class="filter-opt"><input type="checkbox" data-shop-color value="yellow"> <span class="swatch" style="background:#E7BE5A"></span> Yellow &amp; Gold</label>
            <label class="filter-opt"><input type="checkbox" data-shop-color value="purple"> <span class="swatch" style="background:#9C7BB0"></span> Purple</label>
            <label class="filter-opt"><input type="checkbox" data-shop-color value="mixed"> <span class="swatch" style="background:linear-gradient(135deg,#E8A9B6,#9DAF8F)"></span> Mixed</label>
          </div>

          <div class="filter-group">
            <h4>Price</h4>
            <label class="filter-opt"><input type="checkbox" data-shop-price value="0-50"> Under $50</label>
            <label class="filter-opt"><input type="checkbox" data-shop-price value="50-75"> $50 – $75</label>
            <label class="filter-opt"><input type="checkbox" data-shop-price value="75-100"> $75 – $100</label>
            <label class="filter-opt"><input type="checkbox" data-shop-price value="100-plus"> $100 &amp; up</label>
          </div>

          <button type="button" class="btn btn-ghost btn-block" data-shop-clear>Clear all filters</button>
        </aside>

        <div>
          <div class="shop-toolbar">
            <button type="button" class="btn btn-outline btn-sm filter-toggle-mobile" data-filters-open>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M4 6h16M7 12h10M10 18h4"/>
              </svg>
              Filters
            </button>

            <p class="shop-count"><b data-shop-count><?= count($products) ?></b> bouquets</p>

            <label class="d-flex align-items-center gap-2" style="gap:.5rem">
              <span class="visually-hidden">Sort by</span>
              <select class="sort-select" data-shop-sort aria-label="Sort bouquets">
                <option value="featured">Featured</option>
                <option value="price-low">Price: low to high</option>
                <option value="price-high">Price: high to low</option>
                <option value="name">Name: A to Z</option>
              </select>
            </label>
          </div>

          <div class="product-grid cols-3" data-shop-grid>
            <?php foreach ($products as $index => $product): ?>
              <?php
                $productId = (int) $product['product_id'];
                $productName = (string) $product['name'];
                $productPrice = (float) $product['price'];
                $productImage = (string) $product['image'];
                $productColor = (string) $product['color'];
                $productOccasion = (string) $product['category_slug'];
                $productDescription = (string) $product['description'];
                $productTag = (string) ($product['tag'] ?? '');
                $productStock = (int) $product['stock'];
              ?>

              <article class="product-card reveal<?= ($index % 4 === 1) ? ' d1' : (($index % 4 === 2) ? ' d2' : (($index % 4 === 3) ? ' d3' : '')) ?>"
                       data-occasion="<?= e($productOccasion) ?>"
                       data-color="<?= e($productColor) ?>"
                       data-price="<?= e($productPrice) ?>"
                       data-name="<?= e($productName) ?>"
                       data-order="<?= $index + 1 ?>">

                <div class="product-media">
                  <?php if ($productTag !== ''): ?>
                    <span class="product-tag"><?= e($productTag) ?></span>
                  <?php endif; ?>
                  <img src="<?= e($productImage) ?>" alt="<?= e($productName) ?>" width="400" height="400" loading="lazy">
                </div>

                <div class="product-body">
                  <h3><?= e($productName) ?></h3>
                  <p class="note"><?= e($productDescription) ?></p>

                  <div class="product-foot">
                    <span class="product-price"><?= money($productPrice) ?></span>

                    <?php if ($productStock > 0): ?>
                      <button type="button" class="add-btn"
                              data-id="<?= $productId ?>"
                              data-name="<?= e($productName) ?>"
                              data-price="<?= e($productPrice) ?>"
                              data-img="<?= e($productImage) ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                          <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span class="add-label">Add</span>
                      </button>
                    <?php else: ?>
                      <button type="button" class="add-btn" disabled>
                        <span class="add-label">Out of stock</span>
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>

            <p class="empty-note" hidden>No bouquets match those filters — try clearing a few.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-sm">
    <div class="container">
      <div class="newsletter reveal">
        <span class="eyebrow center" style="color:rgba(255,255,255,.85)">Join the List</span>
        <h2>Bloom notes in your inbox</h2>
        <p>What's in season, care tips, and the occasional subscriber-only bunch. One gentle email a week — no more.</p>

        <form class="news-form" data-validate novalidate>
          <label for="news-email" class="visually-hidden">Email address</label>
          <input type="email" id="news-email" name="email" placeholder="you@example.com" required autocomplete="email">
          <button type="submit" class="btn">Subscribe</button>
        </form>
        <p class="form-msg" role="status" aria-live="polite"></p>
      </div>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <a href="index.php" class="footer-brand">Fleur<span class="mark">.</span></a>
        <p>A small flower studio on Rowan Hill, tying seasonal bouquets by hand and delivering them across the city the same day.</p>
        <div class="social-row">
          <a href="#" aria-label="Fleur on Instagram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <rect x="2" y="2" width="20" height="20" rx="5"/>
              <circle cx="12" cy="12" r="4"/>
              <circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/>
            </svg>
          </a>
          <a href="#" aria-label="Fleur on Pinterest">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <circle cx="12" cy="12" r="10"/>
              <path d="M8.5 20c1-3 2.5-8 2.5-8m0 0c-.4-1 0-2.5 1.5-2.5s2 1.3 1.6 3c-.4 2-2 3-3.1 2.5"/>
            </svg>
          </a>
          <a href="#" aria-label="Fleur on Facebook">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path d="M15 3h-3a4 4 0 0 0-4 4v3H5v4h3v7h4v-7h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            </svg>
          </a>
        </div>
      </div>

      <div class="footer-col">
        <h5>Shop</h5>
        <ul>
          <li><a href="shop.php">All bouquets</a></li>
          <li><a href="shop.php">Birthday</a></li>
          <li><a href="shop.php">Weddings</a></li>
          <li><a href="index.php#subscription">Subscriptions</a></li>
          <li><a href="shop.php">Sympathy</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>Studio</h5>
        <ul>
          <li><a href="index.php#story">Our story</a></li>
          <li><a href="index.php#services">Services</a></li>
          <li><a href="index.php#gallery">Gallery</a></li>
          <li><a href="index.php#delivery">Delivery areas</a></li>
          <li><a href="index.php#reviews">Reviews</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>Visit Us</h5>
        <ul class="footer-contact">
          <li>14 Rowan Hill Lane, Old Town</li>
          <li>(555) 0142 — 3390</li>
          <li>hello@fleur-studio.example</li>
          <li>Tue–Sun, 9am–6pm</li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span>&copy; 2026 Fleur Flower Studio. Built on Bootstrap 5.</span>
      <span><a href="#">Privacy</a> &middot; <a href="#">Terms</a> &middot; <a href="#">Care Guide</a></span>
    </div>
  </div>
</footer>

<div class="cart-overlay" aria-hidden="true"></div>
<aside class="cart-drawer" aria-hidden="true" aria-label="Shopping bag" role="dialog" aria-modal="true">
  <div class="cart-head">
    <h3>Your Bag</h3>
    <button type="button" class="cart-close" aria-label="Close bag">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M18 6 6 18M6 6l12 12"/>
      </svg>
    </button>
  </div>
  <div class="cart-items"></div>
  <div class="cart-foot">
    <div class="cart-total">
      <span class="lbl">Subtotal</span>
      <span class="amt" data-cart-total>$0.00</span>
    </div>
    <p class="cart-note">Delivery calculated at checkout. Free within 5 miles.</p>
    <a href="checkout.php" class="btn btn-rose btn-block">Checkout</a>
  </div>
</aside>

<script src="js/vendor/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
