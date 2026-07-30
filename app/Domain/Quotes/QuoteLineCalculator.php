<?php

namespace App\Domain\Quotes;

final class QuoteLineCalculator
{
    /**
     * @return array{
     *     gross_subtotal: float,
     *     discount_amount: float,
     *     net_subtotal: float,
     *     tax_amount: float,
     *     line_total: float
     * }
     */
    public static function calculate(
        float $quantity,
        float $unitPrice,
        float $discountPercent,
        float $taxRate,
    ): array {
        $gross = round($quantity * $unitPrice, 2);
        $discount = round($gross * ($discountPercent / 100), 2);
        $net = round($gross - $discount, 2);
        $tax = round($net * ($taxRate / 100), 2);

        return [
            'gross_subtotal' => $gross,
            'discount_amount' => $discount,
            'net_subtotal' => $net,
            'tax_amount' => $tax,
            'line_total' => round($net + $tax, 2),
        ];
    }
}
