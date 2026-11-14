<?php
/**
 * footer.php — the template's footer + slide-in cart drawer + scripts.
 * Shared by every PHP page. Links use url() so they work from admin/ too.
 */
?>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <a href="<?= url('index.php') ?>" class="footer-brand">Fleur<span class="mark">.</span></a>
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
          <li><a href="<?= url('shop.php') ?>">All bouquets</a></li>
          <li><a href="<?= url('shop.php') ?>">Birthday</a></li>
          <li><a href="<?= url('shop.php') ?>">Weddings</a></li>
          <li><a href="<?= url('index.php#subscription') ?>">Subscriptions</a></li>
          <li><a href="<?= url('shop.php') ?>">Sympathy</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>Studio</h5>
        <ul>
          <li><a href="<?= url('index.php#story') ?>">Our story</a></li>
          <li><a href="<?= url('index.php#services') ?>">Services</a></li>
          <li><a href="<?= url('index.php#gallery') ?>">Gallery</a></li>
          <li><a href="<?= url('index.php#delivery') ?>">Delivery areas</a></li>
          <li><a href="<?= url('index.php#reviews') ?>">Reviews</a></li>
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
    <a href="<?= url('checkout.php') ?>" class="btn btn-rose btn-block">Checkout</a>
  </div>
</aside>


<script>window.FLEUR_BASE = <?= json_encode(BASE_URL . '/') ?>;</script>
<script src="<?= url('js/vendor/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('js/main.js') ?>"></script>
</body>
</html>
