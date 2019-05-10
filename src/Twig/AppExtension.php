<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('array_column', array($this, 'arrayColumn')),
        ];
    }

    /**
     * get array of column
     *
     * @param array  $array
     * @param string $value
     *
     * @return array
     */
    public function arrayColumn(array $array, ?string $value): array
    {
        return array_column($array, $value);
    }
}
