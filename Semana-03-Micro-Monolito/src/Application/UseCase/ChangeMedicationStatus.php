<?php

declare(strict_types=1);

namespace MicroHis\Application\UseCase;

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Exception\ValidationException;
use MicroHis\Domain\Medication;
use MicroHis\Domain\MedicationStatus;
use MicroHis\Domain\Repository\MedicationRepository;

final class ChangeMedicationStatus
{
    public function __construct(
        private readonly MedicationRepository $repository
    ) {
    }

    public function execute(int $id, string $status): Medication
    {
        if ($id <= 0) {
            throw new ValidationException(
                'El medicamento indicado no es válido.'
            );
        }

        $newStatus = MedicationStatus::fromInput($status);

        try {
            $medication = $this->repository->findById($id);

            if ($medication === null) {
                throw new ValidationException(
                    'No se encontró el medicamento solicitado.'
                );
            }

            return $this->repository->changeStatus($id, $newStatus);
        } catch (RepositoryException $exception) {
            throw new ApplicationException(
                'No fue posible cambiar el estado del medicamento.',
                0,
                $exception,
            );
        }
    }
}