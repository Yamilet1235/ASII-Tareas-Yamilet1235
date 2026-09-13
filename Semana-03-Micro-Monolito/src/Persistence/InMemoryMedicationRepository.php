<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use MicroHis\Domain\Exception\DuplicateMedicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Medication;
use MicroHis\Domain\MedicationNameNormalizer;
use MicroHis\Domain\MedicationStatus;
use MicroHis\Domain\Repository\MedicationRepository;

final class InMemoryMedicationRepository implements MedicationRepository
{
    /** @var array<int, Medication> */
    private array $medications = [];

    private int $nextId = 1;

    public function add(Medication $medication): Medication
    {
        if ($this->findByName($medication->name()) !== null) {
            throw new DuplicateMedicationException(
                'Ya existe un medicamento con ese nombre.'
            );
        }

        $stored = new Medication(
            $this->nextId++,
            $medication->name(),
            $medication->genericName(),
            $medication->presentation(),
            $medication->concentration(),
            $medication->status(),
        );

        $this->medications[(int) $stored->id()] = $stored;

        return $stored;
    }

    public function findById(int $id): ?Medication
    {
        return $this->medications[$id] ?? null;
    }

    public function findByName(string $name): ?Medication
    {
        $normalized = MedicationNameNormalizer::normalize($name);

        foreach ($this->medications as $medication) {
            if (
                MedicationNameNormalizer::normalize($medication->name())
                === $normalized
            ) {
                return $medication;
            }
        }

        return null;
    }

    public function search(string $query): array
    {
        $query = MedicationNameNormalizer::normalize($query);

        if ($query === '') {
            return array_values($this->medications);
        }

        return array_values(array_filter(
            $this->medications,
            static function (Medication $medication) use ($query): bool {
                $name = MedicationNameNormalizer::normalize(
                    $medication->name()
                );

                $genericName = MedicationNameNormalizer::normalize(
                    $medication->genericName()
                );

                return str_contains($name, $query)
                    || str_contains($genericName, $query);
            }
        ));
    }

    public function changeStatus(
        int $id,
        MedicationStatus $status
    ): Medication {
        $medication = $this->findById($id);

        if ($medication === null) {
            throw new RepositoryException(
                'El medicamento no existe.'
            );
        }

        $medication->changeStatus($status);

        return $medication;
    }
}