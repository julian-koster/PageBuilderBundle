<?php

namespace JulianKoster\PageBuilderBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GroupByExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('group_by', [$this, 'groupBy']),
        ];
    }

    public function groupBy(iterable $items, $propertyOrCallable): array
    {
        $grouped = [];

        foreach ($items as $item) {
            if (is_callable($propertyOrCallable)) {
                $key = $propertyOrCallable($item);
            } else {
                if (is_array($item)) {
                    $key = $item[$propertyOrCallable] ?? null;
                } elseif (is_object($item)) {
                    $getter = 'get' . ucfirst($propertyOrCallable);
                    $key = method_exists($item, $getter) ? $item->$getter() : null;
                } else {
                    $key = null;
                }
            }

            if ($key !== null) {
                $grouped[$key][] = $item;
            }
        }

        return $grouped;
    }
}