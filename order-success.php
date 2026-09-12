<?php
require_once __DIR__ . '/includes/session.php';
startSecureSession();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$orderId = (int) ($_GET['id'] ?? 0);
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute(['id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
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
<title>Buyurtma qabul qilindi — SmashBite</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="index.php" class="brand"><span class="flame-dot"></span> SmashBite</a>
  </div>
</header>

<div class="page-wrap" style="text-align:center;">
  <div style="font-size:60px; margin-bottom:10px;">🔥</div>
  <h1>Buyurtma qabul qilindi!</h1>
  <p class="sub">Buyurtma raqami: <strong>#<?= $order['id'] ?></strong></p>

  <div class="alert alert-success" style="text-align:left;">
    <?= e($order['customer_name']) ?>, buyurtmangiz jami <strong><?= money($order['total_amount']) ?></strong> ni tashkil qildi.
    <?= $order['order_type'] === 'delivery' ? 'Tez orada manzilingizga yetkazib beramiz.' : 'Restoranimizdan olib ketishingiz mumkin.' ?>
    Operatorimiz <?= e($order['phone']) ?> raqamiga qo'ng'iroq qiladi.
  </div>

  <a href="index.php" class="btn-flame">Bosh sahifaga qaytish</a>
</div>

<footer>© 2026 SmashBite</footer>

</body>
</html>
