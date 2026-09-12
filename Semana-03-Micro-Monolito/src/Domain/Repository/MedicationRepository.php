<?php

declare(strict_types=1);

namespace MicroHis\Domain\Repository;

use MicroHis\Domain\Medication;
use MicroHis\Domain\MedicationStatus;

interface MedicationRepository
{
    public function add(Medication $medication): Medication;

    public function findById(int $id): ?Medication;

    public function findByName(string $name): ?Medication;

    /** @return list<Medication> */
    public function search(string $query): array;

    public function changeStatus(int $id, MedicationStatus $status): Medication;
}
