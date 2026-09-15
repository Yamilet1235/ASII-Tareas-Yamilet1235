<?php

declare(strict_types=1);

namespace MicroHis\Application\UseCase;

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Medication;
use MicroHis\Domain\Repository\MedicationRepository;

final class GetMedication
{
    public function __construct(
        private readonly MedicationRepository $repository
    ) {
    }

    public function execute(int $id): ?Medication
    {
        try {
            return $this->repository->findById($id);
        } catch (RepositoryException $exception) {
            throw new ApplicationException(
                'No fue posible consultar el medicamento. Intente nuevamente.',
                0,
                $exception,
            );
        }
    }
}
