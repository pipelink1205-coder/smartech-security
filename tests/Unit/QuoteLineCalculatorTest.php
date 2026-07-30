<?php

namespace Tests\Unit;

use App\Domain\Quotes\QuoteLineCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuoteLineCalculatorTest extends TestCase
{
    #[DataProvider('lineCases')]
    public function test_it_calculates_commercial_lines(
        float $quantity,
        float $unitPrice,
        float $discount,
        float $tax,
        array $expected,
    ): void {
        $this->assertSame(
            $expected,
            QuoteLineCalculator::calculate($quantity, $unitPrice, $discount, $tax),
        );
    }

    public static function lineCases(): array
    {
        return [
            'iva without discount' => [
                2, 100000, 0, 19,
                [
                    'gross_subtotal' => 200000.0,
                    'discount_amount' => 0.0,
                    'net_subtotal' => 200000.0,
                    'tax_amount' => 38000.0,
                    'line_total' => 238000.0,
                ],
            ],
            'discount before tax' => [
                3, 50000, 10, 19,
                [
                    'gross_subtotal' => 150000.0,
                    'discount_amount' => 15000.0,
                    'net_subtotal' => 135000.0,
                    'tax_amount' => 25650.0,
                    'line_total' => 160650.0,
                ],
            ],
            'non taxable service' => [
                1, 120000, 0, 0,
                [
                    'gross_subtotal' => 120000.0,
                    'discount_amount' => 0.0,
                    'net_subtotal' => 120000.0,
                    'tax_amount' => 0.0,
                    'line_total' => 120000.0,
                ],
            ],
        ];
    }
}
