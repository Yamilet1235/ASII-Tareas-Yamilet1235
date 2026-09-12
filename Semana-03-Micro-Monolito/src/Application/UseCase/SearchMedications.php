<?php

declare(strict_types=1);

namespace MicroHis\Application\UseCase;

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Domain\Exception\RepositoryException;
use MicroHis\Domain\Repository\MedicationRepository;

final class SearchMedications
{
    public function __construct(private readonly MedicationRepository $repository)
    {
    }

    public function execute(string $query = ''): array
    {
        try {
            return $this->repository->search(trim($query));
        } catch (RepositoryException $exception) {
            throw new ApplicationException(
                'No fue posible consultar el catálogo. Intente nuevamente.',
                0,
                $exception,
            );
        }
    }
}
