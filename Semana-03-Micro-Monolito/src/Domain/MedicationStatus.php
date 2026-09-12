<?php

declare(strict_types=1);

namespace MicroHis\Domain;

use MicroHis\Domain\Exception\ValidationException;

enum MedicationStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';

    public static function fromInput(string $status): self
    {
        $value = strtoupper(trim($status));
        $parsed = self::tryFrom($value);

        if ($parsed === null) {
            throw new ValidationException('El estado debe ser ACTIVE o INACTIVE.');
        }

        return $parsed;
    }

    public function label(): string
    {
        return $this === self::ACTIVE ? 'ACTIVO' : 'INACTIVO';
    }
}
