<?php
require_once __DIR__ . '/includes/session.php';
startSecureSession();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/icons.php';

// --- Savatni yangilash / o'chirish ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    if (isset($_POST['update_qty'])) {
        $pid = (int) $_POST['product_id'];
        $qty = max(0, (int) $_POST['qty']);
        cart_set($pid, $qty);
    } elseif (isset($_POST['clear_cart'])) {
        cart_clear();
    }
    header('Location: cart.php');
    exit;
}

$pdo = getPDO();
$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productsById = [];
    foreach ($stmt->fetchAll() as $p) {
        $productsById[$p['id']] = $p;
    }
    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (!isset($productsById[$pid])) continue;
        $p = $productsById[$pid];
        $lineTotal = $p['price'] * $qty;
        $total += $lineTotal;
        $cartItems[] = ['product' => $p, 'qty' => $qty, 'lineTotal' => $lineTotal];
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
  if (window.Telegram && window.Telegram.WebApp) {
    const tg = window.Telegram.WebApp;
    tg.ready();
    tg.expand();
  }
</script>
<title>Savat — SmashBite</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="index.php" class="brand"><span class="flame-dot"></span> SmashBite</a>
    <nav class="nav-links">
      <a href="index.php#menu">Menyu</a>
      <a href="book-table.php">Joy band qilish</a>
    </nav>
  </div>
</header>

<div class="page-wrap">
  <h1>Savatingiz</h1>
  <p class="sub"><?= count($cartItems) ?> xil taom</p>

  <?php if (empty($cartItems)): ?>
    <p style="color:var(--text-dim)">Savat bo'sh. <a href="index.php#menu" style="color:var(--flame)">Menyuga qaytish →</a></p>
  <?php else: ?>
    <?php foreach ($cartItems as $item): ?>
      <div class="cart-row">
        <div><?= food_icon($item['product']['icon'], 44) ?></div>
        <div>
          <strong><?= e($item['product']['name']) ?></strong><br>
          <span style="color:var(--text-dim); font-size:13px;"><?= money($item['product']['price']) ?> / dona</span>
        </div>
        <form method="post" action="cart.php" class="qty-box">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
          <button type="submit" name="update_qty" value="1" onclick="this.form.qty.value=<?= $item['qty'] - 1 ?>">−</button>
          <input type="hidden" name="qty" value="<?= $item['qty'] ?>">
          <span><?= $item['qty'] ?></span>
          <button type="submit" name="update_qty" value="1" onclick="this.form.qty.value=<?= $item['qty'] + 1 ?>">+</button>
        </form>
        <strong><?= money($item['lineTotal']) ?></strong>
      </div>
    <?php endforeach; ?>

    <div class="cart-total">
      <span>Jami:</span>
      <span class="amount"><?= money($total) ?></span>
    </div>

    <div class="hero-actions" style="margin-top:20px;">
      <a href="checkout.php" class="btn-flame">Buyurtmani rasmiylashtirish</a>
      <form method="post" action="cart.php">
        <?= csrf_field() ?>
        <button type="submit" name="clear_cart" value="1" class="btn-outline" style="background:none; cursor:pointer;">Savatni tozalash</button>
      </form>
    </div>
  <?php endif; ?>
</div>

<footer>© 2026 SmashBite</footer>

</body>
</html>
