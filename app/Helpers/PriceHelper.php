<?php

declare(strict_types=1);

namespace App\Helpers;

class PriceHelper
{
    /**
     * Formatar preço para exibição
     */
    public static function formatPrice(float|string $price): string
    {
        $price = is_string($price) ? floatval($price) : $price;
        return 'R$ ' . number_format($price, 2, ',', '.');
    }

    /**
     * Converter preço de string para float
     */
    public static function parsePrice(string $price): float
    {
        // Remove R$, espaços e substitui vírgula por ponto
        $price = str_replace(['R$', ' ', '.'], '', $price);
        $price = str_replace(',', '.', $price);
        return floatval($price);
    }

    /**
     * Formatar preço sem símbolo de moeda
     */
    public static function formatNumber(float|string $price): string
    {
        $price = is_string($price) ? floatval($price) : $price;
        return number_format($price, 2, ',', '.');
    }
}