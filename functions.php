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
