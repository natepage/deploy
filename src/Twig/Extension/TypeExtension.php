<?php
declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TypeExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('empty', [$this, 'isEmpty']),
            new TwigFilter('is_type', [$this, 'isType'])
        ];
    }

    /**
     * Check if given value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEmpty($value): bool
    {
        return empty($value);
    }

    /**
     * Check if given value is of the given type.
     *
     * @param mixed $value
     * @param string $type
     *
     * @return bool
     */
    public function isType($value, string $type): bool
    {
        $ctype = \sprintf('ctype_%s', $type);

        if (\function_exists($ctype)) {
            return $ctype($value);
        }

        $istype = \sprintf('is_%s', $type);

        return $istype($value);
    }
}
