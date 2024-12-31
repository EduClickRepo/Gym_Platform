<?php

namespace App\Utils;

class PayStatusMapper
{
    public static function getStatusMapping(): array
    {
        return [
            PayStatusEnum::APPROVED->value => 1,
            PayStatusEnum::DECLINED->value => 2,
            PayStatusEnum::PENDING->value => 3,
            PayStatusEnum::ERROR->value => 4,
        ];
    }

    public static function getMappedValue(String $status): ?int
    {
        $statusEnum = PayStatusEnum::tryFrom($status);

        if (!$statusEnum) {
            throw new \InvalidArgumentException("El estado proporcionado no es válido.");
        }

        $mapping = self::getStatusMapping();
        return $mapping[$statusEnum->value] ?? null;
    }
}