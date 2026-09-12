<?php
require_once __DIR__ . '/includes/session.php';
startSecureSession();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getPDO();
$errors = [];
$old = ['name' => '', 'phone' => '', 'date' => '', 'time' => '', 'guests' => 2, 'notes' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Sessiya eskirgan. Sahifani yangilab qayta urinib ko\'ring.';
    } else {
        $old['name'] = trim($_POST['name'] ?? '');
        $old['phone'] = trim($_POST['phone'] ?? '');
        $old['date'] = $_POST['date'] ?? '';
        $old['time'] = $_POST['time'] ?? '';
        $old['guests'] = (int) ($_POST['guests'] ?? 2);
        $old['notes'] = trim($_POST['notes'] ?? '');

        if (mb_strlen($old['name']) < 2) $errors[] = 'Ismingizni kiriting.';
        if (!validate_phone($old['phone'])) $errors[] = 'Telefon raqam noto\'g\'ri formatda.';
        if (empty($old['date']) || strtotime($old['date']) < strtotime('today')) $errors[] = 'Sanani to\'g\'ri tanlang (bugundan keyingi kun).';
        if (empty($old['time'])) $errors[] = 'Vaqtni tanlang.';
        if ($old['guests'] < 1 || $old['guests'] > 20) $errors[] = 'Mehmonlar soni 1-20 orasida bo\'lishi kerak.';

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO bookings (customer_name, phone, booking_date, booking_time, guests, notes)
                VALUES (:name, :phone, :date, :time, :guests, :notes)");
            $stmt->execute([
                'name' => $old['name'], 'phone' => $old['phone'], 'date' => $old['date'],
                'time' => $old['time'], 'guests' => $old['guests'], 'notes' => $old['notes'],
            ]);
            header('Location: booking-success.php');
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
<title>Joy band qilish — SmashBite</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="index.php" class="brand"><span class="flame-dot"></span> SmashBite</a>
    <nav class="nav-links"><a href="index.php#menu">Menyu</a></nav>
  </div>
</header>

<div class="page-wrap">
  <h1>Joy band qilish</h1>
  <p class="sub">Stol band qilib, biz kutib olamiz</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post" action="book-table.php">
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
      <label for="date">Sana</label>
      <input type="date" id="date" name="date" value="<?= e($old['date']) ?>" min="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="field">
      <label for="time">Vaqt</label>
      <input type="time" id="time" name="time" value="<?= e($old['time']) ?>" required>
    </div>

    <div class="field">
      <label for="guests">Mehmonlar soni</label>
      <input type="number" id="guests" name="guests" value="<?= e((string)$old['guests']) ?>" min="1" max="20" required>
    </div>

    <div class="field">
      <label for="notes">Qo'shimcha izoh (ixtiyoriy)</label>
      <textarea id="notes" name="notes" rows="3" placeholder="Masalan: tug'ilgan kun uchun stol bezatilsin"><?= e($old['notes']) ?></textarea>
    </div>

    <button type="submit" class="btn-flame" style="width:100%;">Joyni band qilish</button>
  </form>
</div>

<footer>© 2026 SmashBite</footer>

</body>
</html>
