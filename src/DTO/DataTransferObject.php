<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

use Spatie\DataTransferObject\DataTransferObject as BaseDataTransferObject;

abstract class DataTransferObject extends BaseDataTransferObject
{
    /**
     * Override the toArray method to return an array without null values
     */
    public function toArray(): array
    {
        return self::filterArrayRecursively(parent::toArray());
    }

    private static function filterArrayRecursively(array $array): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $val = self::filterArrayRecursively($item);
            } else {
                $val = $item;
            }

            $res[$key] = $val;
        }

        return array_filter($res, static function ($elm) {
            return null !== $elm;
        });
    }
}
