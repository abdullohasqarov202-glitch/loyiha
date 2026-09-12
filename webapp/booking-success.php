<?php
require_once __DIR__ . '/includes/session.php';
startSecureSession();
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
<title>Joy band qilindi — SmashBite</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="index.php" class="brand"><span class="flame-dot"></span> SmashBite</a>
  </div>
</header>

<div class="page-wrap" style="text-align:center;">
  <div style="font-size:60px; margin-bottom:10px;">🎉</div>
  <h1>Joyingiz band qilindi!</h1>
  <div class="alert alert-success" style="text-align:left;">
    So'rovingiz qabul qilindi. Operatorimiz tez orada sizga qo'ng'iroq qilib, tasdiqlaydi.
  </div>
  <a href="index.php" class="btn-flame">Bosh sahifaga qaytish</a>
</div>

<footer>© 2026 SmashBite</footer>

</body>
</html>
