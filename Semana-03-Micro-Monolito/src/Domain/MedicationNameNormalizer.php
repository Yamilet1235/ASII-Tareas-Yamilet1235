<?php

declare(strict_types=1);

namespace MicroHis\Domain;

final class MedicationNameNormalizer
{
    public static function normalize(string $name): string
    {
        $trimmed = trim($name);

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($trimmed, 'UTF-8');
        }

        return strtolower(strtr($trimmed, [
            'Á' => 'á', 'É' => 'é', 'Í' => 'í', 'Ó' => 'ó', 'Ú' => 'ú',
            'Ü' => 'ü', 'Ñ' => 'ñ', 'À' => 'à', 'È' => 'è', 'Ì' => 'ì',
            'Ò' => 'ò', 'Ù' => 'ù', 'Â' => 'â', 'Ê' => 'ê', 'Î' => 'î',
            'Ô' => 'ô', 'Û' => 'û', 'Ç' => 'ç',
        ]));
    }
}
