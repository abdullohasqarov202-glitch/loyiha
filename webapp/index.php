<?php
require_once __DIR__ . '/includes/session.php';
startSecureSession();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/icons.php';

// --- Savatga qo'shish (forma POST orqali) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (csrf_verify()) {
        $productId = (int) $_POST['product_id'];
        cart_add($productId, 1);
    }
    header('Location: index.php#menu');
    exit;
}

$pdo = getPDO();
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE is_available = 1 ORDER BY category_id")->fetchAll();

$byCategory = [];
foreach ($products as $p) {
    $byCategory[$p['category_id']][] = $p;
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
<title>SmashBite — tez va mazali</title>
<link rel="stylesheet" href="assets/style.css">
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
  // Agar sayt Telegram Mini App sifatida ochilgan bo'lsa, uni to'liq ekranga yoyish
  if (window.Telegram && window.Telegram.WebApp) {
    const tg = window.Telegram.WebApp;
    tg.ready();
    tg.expand();
  }
</script>
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="index.php" class="brand"><span class="flame-dot"></span> SmashBite</a>
    <nav class="nav-links">
      <a href="index.php#menu">Menyu</a>
      <a href="book-table.php">Joy band qilish</a>
      <a href="cart.php" class="cart-pill">
        🛒 Savat <span class="count"><?= cart_total_items() ?></span>
      </a>
    </nav>
  </div>
</header>

<section class="wrap hero">
  <div>
    <h1>Zarba bilan pishirilgan,<br><span class="hl">10 daqiqada</span> qo'lingizda.</h1>
    <p>Smash-burgerlar, issiq pitsalar va xrustall fri — uyingizga yetkazib beramiz yoki restoranda joy band qilib, o'zingiz kelib tatib ko'ring.</p>
    <div class="hero-actions">
      <a href="#menu" class="btn-flame">Menyuni ko'rish</a>
      <a href="book-table.php" class="btn-outline">Joy band qilish</a>
    </div>
  </div>
  <div class="hero-badge">
    <?= food_icon('burger', 140) ?>
  </div>
</section>

<div class="wrap" id="menu">
  <div class="cat-tabs" id="cat-tabs">
    <button class="cat-tab active" data-filter="all">Barchasi</button>
    <?php foreach ($categories as $cat): ?>
      <button class="cat-tab" data-filter="cat-<?= $cat['id'] ?>">
        <?= food_icon($cat['icon'], 18) ?> <?= e($cat['name']) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <div class="menu-section">
    <?php foreach ($categories as $cat): ?>
      <div class="menu-category-block" data-category="cat-<?= $cat['id'] ?>">
        <h2 class="menu-cat-title"><?= e($cat['name']) ?></h2>
        <div class="menu-grid">
          <?php foreach ($byCategory[$cat['id']] ?? [] as $p): ?>
            <div class="item-card">
              <div class="item-icon"><?= food_icon($p['icon']) ?></div>
              <h3><?= e($p['name']) ?></h3>
              <p><?= e($p['description']) ?></p>
              <div class="item-foot">
                <span class="item-price"><?= money($p['price']) ?></span>
                <form method="post" action="index.php">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                  <button type="submit" name="add_to_cart" class="add-btn" title="Savatga qo'shish">+</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  // Menyu kategoriya filtri (sahifa qayta yuklanmasdan)
  document.querySelectorAll('#cat-tabs .cat-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
      document.querySelectorAll('#cat-tabs .cat-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      var filter = this.dataset.filter;
      document.querySelectorAll('.menu-category-block').forEach(function(block){
        if (filter === 'all' || block.dataset.category === filter) {
          block.style.display = '';
        } else {
          block.style.display = 'none';
        }
      });
    });
  });
</script>

<section class="about-sec" id="about">
  <div class="wrap about-grid">
    <div>
      <p class="sec-eyebrow">Biz haqimizda</p>
      <h2>Tez tayyorlanadi, sekin unutiladi.</h2>
      <p>SmashBite — har bir burgerni mijoz kelgach, aynan o'sha zahoti presslab pishiradigan kichik jamoa. Muzlatilgan yarim tayyor mahsulot ishlatmaymiz — go'sht har kuni yangi keladi.</p>
      <ul class="about-points">
        <li><span class="dot"></span> Buyurtmadan 10-15 daqiqada tayyor</li>
        <li><span class="dot"></span> Har kuni yangi go'sht va sabzavot</li>
        <li><span class="dot"></span> Shahar bo'ylab yetkazib berish</li>
      </ul>
    </div>
    <div class="about-visual">
      <?= food_icon('fries', 160) ?>
    </div>
  </div>
</section>

<section class="highlight-sec wrap">
  <div class="highlight-visual">
    <?= food_icon('pizza', 170) ?>
  </div>
  <div>
    <span class="tag">Bugungi tavsiya</span>
    <h2>Pepperoni pitsa — xamiri yupqa, ustki qismi mo'l-ko'l.</h2>
    <p>Har bir dilim qo'lda yoyilgan xamirga, mozarella va achchiq pepperoni bilan yopiladi. Pechda 300°C haroratda 6 daqiqada pishiriladi — qirralari xrustall, o'rtasi yumshoq.</p>
    <div class="hero-actions" style="margin-top:24px;">
      <a href="#menu" class="btn-flame">Menyudan buyurtma berish</a>
    </div>
  </div>
</section>

<section class="booking-teaser">
  <div class="wrap">
    <p class="sec-eyebrow">Restoranda tatib ko'rish</p>
    <h2 class="sec-heading">Stol band qiling, biz kutib olamiz</h2>
    <div class="booking-hours">
      <div>
        <div class="day">Dushanba – Payshanba</div>
        <div class="time">9:00 – 22:00</div>
      </div>
      <div>
        <div class="day">Juma – Yakshanba</div>
        <div class="time">11:00 – 23:00</div>
      </div>
    </div>
    <div class="booking-phone">📞 +998 88 309 02 07</div>
    <div class="hero-actions" style="justify-content:center; margin-top:24px;">
      <a href="book-table.php" class="btn-flame">Joy band qilish</a>
    </div>
  </div>
</section>

<section class="testi-sec">
  <div class="wrap">
    <p class="sec-eyebrow">Mijozlar fikri</p>
    <h2 class="sec-heading">Bizni shahar bo'ylab tanishadi</h2>
    <div class="testi-grid">
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p>"Buyurtma berganimdan 12 daqiqa o'tib eshik oldida edi. Burger hali issiq, non xrustall edi."</p>
        <div class="testi-name">— Diyor, Chilonzor</div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p>"Do'stlarim bilan stol band qildik, xodimlar juda samimiy. Pitsa xamiri boshqa joylardan farq qiladi."</p>
        <div class="testi-name">— Madina, Yunusobod</div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★☆</div>
        <p>"Fri kartoshkasi eng yaxshisi — sovuq kelmaydi, doim xrustall. Narxi ham munosib."</p>
        <div class="testi-name">— Sardor, Mirzo Ulug'bek</div>
      </div>
    </div>
  </div>
</section>

<section class="faq-sec">
  <div class="wrap">
    <p class="sec-eyebrow">Savol-javob</p>
    <h2 class="sec-heading">Tez-tez so'raladigan savollar</h2>

    <details class="faq-item">
      <summary>Yetkazib berish qancha vaqt oladi?</summary>
      <p>Odatda 15-25 daqiqa, shahar chekkasida trafikka qarab biroz uzoqroq bo'lishi mumkin.</p>
    </details>
    <details class="faq-item">
      <summary>Qanday to'lov usullari mavjud?</summary>
      <p>Hozircha naqd pul bilan to'lash mumkin. Karta orqali to'lov (Payme/Click) tez orada qo'shiladi.</p>
    </details>
    <details class="faq-item">
      <summary>Vegetarian taomlar bormi?</summary>
      <p>Ha, Margarita pitsa va ba'zi garnirlar go'shtsiz tayyorlanadi. Menyuda tarkibi ko'rsatilgan.</p>
    </details>
    <details class="faq-item">
      <summary>Stol band qilish uchun oldindan to'lov kerakmi?</summary>
      <p>Yo'q, joy band qilish bepul. Faqat kelganingizda buyurtma qilasiz.</p>
    </details>
  </div>
</section>

<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="brand"><span class="flame-dot"></span> SmashBite</div>
        <p>Tez tayyorlanadigan, sekin unutiladigan burger va pitsalar. Har kuni yangi mahsulotlar bilan.</p>
      </div>
      <div>
        <h4>Ish vaqti</h4>
        <ul>
          <li>Dush-Pay: 9:00 – 22:00</li>
          <li>Juma-Yak: 11:00 – 23:00</li>
        </ul>
      </div>
      <div>
        <h4>Havolalar</h4>
        <ul>
          <li><a href="index.php#menu">Menyu</a></li>
          <li><a href="book-table.php">Joy band qilish</a></li>
          <li><a href="cart.php">Savat</a></li>
        </ul>
      </div>
      <div>
        <h4>Aloqa</h4>
        <ul>
          <li>+998 88 309 02 07</li>
          <li>Toshkent, Chilonzor</li>
        </ul>
      </div>
    </div>
    © 2026 SmashBite
  </div>
</footer>

</body>
</html>
