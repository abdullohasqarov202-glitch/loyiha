<?php
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function money(float $amount): string
{
    return number_format($amount, 0, '.', ' ') . " so'm";
}

function cart_total_items(): int
{
    return array_sum($_SESSION['cart'] ?? []);
}

function cart_add(int $productId, int $qty = 1): void
{
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }
    $_SESSION['cart'][$productId] += $qty;
}

function cart_set(int $productId, int $qty): void
{
    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cart_clear(): void
{
    $_SESSION['cart'] = [];
}

function validate_phone(string $phone): bool
{
    return (bool) preg_match('/^\+?[0-9]{9,13}$/', preg_replace('/[\s\-\(\)]/', '', $phone));
}

<?php
/**
 * Har bir taom turi uchun qo'lda chizilgan oddiy SVG belgi qaytaradi.
 * Haqiqiy suratlar emas — mualliflik huquqi muammosiz, sticker uslubi.
 */
function food_icon(string $type, int $size = 34): string
{
    $color = "#FFC93C";
    switch ($type) {
        case 'burger':
            return "<svg width='$size' height='$size' viewBox='0 0 48 48' fill='none'>
                <ellipse cx='24' cy='14' rx='18' ry='8' fill='#FF5A1F'/>
                <rect x='6' y='20' width='36' height='5' rx='2.5' fill='#7CB342'/>
                <rect x='6' y='27' width='36' height='6' rx='3' fill='#D6430F'/>
                <path d='M6 36 Q24 46 42 36 L42 39 Q24 47 6 39 Z' fill='#FFC93C'/>
            </svg>";
        case 'pizza':
            return "<svg width='$size' height='$size' viewBox='0 0 48 48' fill='none'>
                <path d='M24 4 L44 40 L4 40 Z' fill='#FF5A1F'/>
                <path d='M24 4 L44 40 L4 40 Z' stroke='#FFC93C' stroke-width='2' fill='none'/>
                <circle cx='24' cy='24' r='2.4' fill='#D6430F'/>
                <circle cx='18' cy='31' r='2.4' fill='#D6430F'/>
                <circle cx='30' cy='31' r='2.4' fill='#D6430F'/>
            </svg>";
        case 'fries':
            return "<svg width='$size' height='$size' viewBox='0 0 48 48' fill='none'>
                <path d='M12 20 L36 20 L32 44 L16 44 Z' fill='#FF5A1F'/>
                <rect x='14' y='6' width='5' height='20' rx='1.5' fill='#FFC93C'/>
                <rect x='21.5' y='2' width='5' height='24' rx='1.5' fill='#FFC93C'/>
                <rect x='29' y='6' width='5' height='20' rx='1.5' fill='#FFC93C'/>
            </svg>";
        case 'drink':
            return "<svg width='$size' height='$size' viewBox='0 0 48 48' fill='none'>
                <path d='M14 12 L34 12 L31 42 L17 42 Z' fill='#7CB342'/>
                <rect x='12' y='8' width='24' height='6' rx='2' fill='#FFC93C'/>
                <rect x='22' y='2' width='4' height='10' rx='2' fill='#FFC93C'/>
            </svg>";
        default:
            return "<svg width='$size' height='$size' viewBox='0 0 48 48'><circle cx='24' cy='24' r='20' fill='#FF5A1F'/></svg>";
    }
}

<?php
require_once __DIR__ . '/../config.php';

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax', // savat/checkout uchun Lax, Strict emas
        ]);
        session_start();
    }
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

<?php
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(): bool
{
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

<?php
require_once __DIR__ . '/../config.php';

function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('DB ulanish xatosi: ' . $e->getMessage());
            http_response_code(500);
            die('Server xatosi. Keyinroq urinib ko\'ring.');
        }
    }
    return $pdo;
}
