<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('amountEuro', [$this, 'amountEuro']),
            new TwigFilter('amountUSD', [$this, 'amountUSD'])
        ];
    }

    public function amountEuro(int $value): string
    {
        $finalValue = number_format(($value / 100), 2, ',', ' ');
        return $finalValue . ' €';
    }

    public function amountUSD(int $value): string
    {
        $finalValue = number_format(($value / 100), 2, '.', ',');
        return '$' . $finalValue;
    }
}
