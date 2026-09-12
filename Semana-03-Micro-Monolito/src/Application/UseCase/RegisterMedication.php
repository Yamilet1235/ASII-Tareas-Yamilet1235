<?php

declare(strict_types=1);

namespace MicroHis\Application\UseCase;

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Domain\Exception\DuplicateMedicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Medication;
use MicroHis\Domain\Repository\MedicationRepository;

final class RegisterMedication
{
    public function __construct(private readonly MedicationRepository $repository)
    {
    }

    public function execute(
        string $name,
        string $genericName,
        string $presentation,
        string $concentration,
        string $status,
    ): Medication {
        $medication = Medication::register(
            $name,
            $genericName,
            $presentation,
            $concentration,
            $status,
        );

        try {
            if ($this->repository->findByName($medication->name()) !== null) {
                throw new DuplicateMedicationException(
                    'Ya existe un medicamento con ese nombre.'
                );
            }

            return $this->repository->add($medication);
        } catch (RepositoryException $exception) {
            throw new ApplicationException(
                'No fue posible registrar el medicamento. Intente nuevamente.',
                0,
                $exception,
            );
        }
    }
}
