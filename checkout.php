<?php
require_once __DIR__ . '/includes/session.php';
startSecureSession();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$pdo = getPDO();
$errors = [];
$old = ['name' => '', 'phone' => '', 'order_type' => 'delivery', 'address' => '', 'payment_method' => 'cash'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Sessiya eskirgan. Sahifani yangilab qayta urinib ko\'ring.';
    } else {
        $old['name'] = trim($_POST['name'] ?? '');
        $old['phone'] = trim($_POST['phone'] ?? '');
        $old['order_type'] = $_POST['order_type'] ?? 'delivery';
        $old['address'] = trim($_POST['address'] ?? '');
        $old['payment_method'] = $_POST['payment_method'] ?? 'cash';

        if (mb_strlen($old['name']) < 2) $errors[] = 'Ismingizni kiriting.';
        if (!validate_phone($old['phone'])) $errors[] = 'Telefon raqam noto\'g\'ri formatda.';
        if ($old['order_type'] === 'delivery' && mb_strlen($old['address']) < 5) $errors[] = 'Yetkazib berish manzilini kiriting.';

        if (empty($errors)) {
            // --- Savat asosida buyurtma summasini hisoblash ---
            $ids = array_keys($_SESSION['cart']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $productsById = [];
            foreach ($stmt->fetchAll() as $p) $productsById[$p['id']] = $p;

            $total = 0;
            $items = [];
            foreach ($_SESSION['cart'] as $pid => $qty) {
                if (!isset($productsById[$pid])) continue;
                $price = $productsById[$pid]['price'];
                $total += $price * $qty;
                $items[] = ['product_id' => $pid, 'qty' => $qty, 'price' => $price];
            }

            // --- Buyurtmani saqlash ---
            // TODO: PAYMENT_MODE haqiqiy API'ga ('payme'/'click' va h.k.) ulanganda,
            // shu joyda to'lov so'rovi yuborilib, natijaga qarab payment_status belgilanadi.
            $stmt = $pdo->prepare("INSERT INTO orders
                (customer_name, phone, order_type, address, total_amount, payment_method, payment_status)
                VALUES (:name, :phone, :type, :address, :total, :pm, 'pending')");
            $stmt->execute([
                'name' => $old['name'],
                'phone' => $old['phone'],
                'type' => $old['order_type'],
                'address' => $old['order_type'] === 'delivery' ? $old['address'] : null,
                'total' => $total,
                'pm' => $old['payment_method'],
            ]);
            $orderId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:oid, :pid, :qty, :price)");
            foreach ($items as $it) {
                $stmt->execute(['oid' => $orderId, 'pid' => $it['product_id'], 'qty' => $it['qty'], 'price' => $it['price']]);
            }

            cart_clear();
            header('Location: order-success.php?id=' . $orderId);
            exit;
        }
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
<title>Buyurtmani rasmiylashtirish — SmashBite</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="index.php" class="brand"><span class="flame-dot"></span> SmashBite</a>
  </div>
</header>

<div class="page-wrap">
  <h1>Buyurtmani rasmiylashtirish</h1>
  <p class="sub">Ma'lumotlarni to'ldiring, biz sizga qo'ng'iroq qilamiz</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post" action="checkout.php">
    <?= csrf_field() ?>

    <div class="field">
      <label for="name">Ismingiz</label>
      <input type="text" id="name" name="name" value="<?= e($old['name']) ?>" required>
    </div>

    <div class="field">
      <label for="phone">Telefon raqam</label>
      <input type="text" id="phone" name="phone" value="<?= e($old['phone']) ?>" placeholder="+998901234567" required>
    </div>

    <div class="field">
      <label>Buyurtma turi</label>
      <div class="radio-row">
        <label class="radio-card <?= $old['order_type'] === 'delivery' ? 'selected' : '' ?>">
          <input type="radio" name="order_type" value="delivery" <?= $old['order_type'] === 'delivery' ? 'checked' : '' ?> style="display:none" onclick="this.closest('form').querySelector('#address-field').style.display='block'">
          🛵 Yetkazib berish
        </label>
        <label class="radio-card <?= $old['order_type'] === 'pickup' ? 'selected' : '' ?>">
          <input type="radio" name="order_type" value="pickup" <?= $old['order_type'] === 'pickup' ? 'checked' : '' ?> style="display:none" onclick="this.closest('form').querySelector('#address-field').style.display='none'">
          🏃 Olib ketish
        </label>
      </div>
    </div>

    <div class="field" id="address-field" style="<?= $old['order_type'] === 'pickup' ? 'display:none' : '' ?>">
      <label for="address">Yetkazib berish manzili</label>
      <input type="text" id="address" name="address" value="<?= e($old['address']) ?>" placeholder="Ko'cha, uy raqami">
    </div>

    <div class="field">
      <label>To'lov usuli</label>
      <select name="payment_method">
        <option value="cash" <?= $old['payment_method'] === 'cash' ? 'selected' : '' ?>>Naqd pul (yetkazib berilganda)</option>
        <option value="card" <?= $old['payment_method'] === 'card' ? 'selected' : '' ?>>Karta orqali (Payme/Click)</option>
      </select>
      <div class="payment-note">
        💳 Karta orqali to'lov hozircha tayyorlanmoqda. Buyurtma qabul qilingach, operator siz bilan bog'lanib to'lov havolasini yuboradi.
      </div>
    </div>

    <button type="submit" class="btn-flame" style="width:100%; margin-top:10px;">Buyurtmani tasdiqlash</button>
  </form>
</div>

<footer>© 2026 SmashBite</footer>

</body>
</html>
