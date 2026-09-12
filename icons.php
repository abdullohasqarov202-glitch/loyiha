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
