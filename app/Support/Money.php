<?php

namespace App\Support;

final class Money
{
    public static function symbol(): string
    {
        return (string) config('app.currency_symbol', '₹');
    }

    public static function currency(): string
    {
        return (string) config('app.currency', 'INR');
    }

    /** Convert a percentage ("5.00") into basis points (500). */
    public static function parse(string $value): int
    {
        return (int) round(((float) $value) * 100);
    }

    /** Format an integer amount stored in the smallest currency unit into a display string. */
    public static function format(int $amount): string
    {
        return self::symbol().number_format($amount / 100, 2);
    }

    /** Format an integer amount without a symbol. */
    public static function plain(int $amount, int $decimals = 2): string
    {
        return number_format($amount / 100, $decimals);
    }
}