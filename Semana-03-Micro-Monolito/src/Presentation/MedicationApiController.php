<?php

declare(strict_types=1);

namespace MicroHis\Presentation;

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Application\UseCase\ChangeMedicationStatus;
use MicroHis\Application\UseCase\GetMedication;
use MicroHis\Application\UseCase\RegisterMedication;
use MicroHis\Application\UseCase\SearchMedications;
use MicroHis\Domain\Exception\DuplicateMedicationException;
use MicroHis\Domain\Exception\ValidationException;
use MicroHis\Domain\Medication;

final class MedicationApiController
{
    public function __construct(
        private readonly RegisterMedication $registerMedication,
        private readonly SearchMedications $searchMedications,
        private readonly GetMedication $getMedication,
        private readonly ChangeMedicationStatus $changeMedicationStatus,
    ) {
    }

    /**
     * @return array{status:int, body:array<string,mixed>}
     */
    public function index(string $search = ''): array
    {
        try {
            $medications = $this->searchMedications->execute($search);

            return [
                'status' => 200,
                'body' => [
                    'data' => array_map(
                        fn (Medication $medication): array =>
                            $this->serializeMedication($medication),
                        $medications
                    ),
                ],
            ];
        } catch (ApplicationException $exception) {
            return $this->error(
                500,
                'INTERNAL_ERROR',
                $exception->getMessage()
            );
        }
    }

    /**
     * @return array{status:int, body:array<string,mixed>}
     */
    public function show(int $id): array
    {
        try {
            $medication = $this->getMedication->execute($id);

            if ($medication === null) {
                return $this->error(
                    404,
                    'MEDICATION_NOT_FOUND',
                    'El medicamento solicitado no existe.'
                );
            }

            return [
                'status' => 200,
                'body' => [
                    'data' => $this->serializeMedication($medication),
                ],
            ];
        } catch (ApplicationException $exception) {
            return $this->error(
                500,
                'INTERNAL_ERROR',
                $exception->getMessage()
            );
        }
    }

    /**
     * @param array<string,mixed> $input
     * @return array{status:int, body:array<string,mixed>}
     */
    public function store(array $input): array
    {
        try {
            $medication = $this->registerMedication->execute(
                (string) ($input['name'] ?? ''),
                (string) ($input['genericName'] ?? ''),
                (string) ($input['presentation'] ?? ''),
                (string) ($input['concentration'] ?? ''),
                (string) ($input['status'] ?? 'ACTIVE'),
            );

            return [
                'status' => 201,
                'body' => [
                    'data' => $this->serializeMedication($medication),
                ],
            ];
        } catch (DuplicateMedicationException $exception) {
            return $this->error(
                409,
                'DUPLICATE_MEDICATION',
                $exception->getMessage()
            );
        } catch (ValidationException $exception) {
            return $this->error(
                422,
                'VALIDATION_ERROR',
                $exception->getMessage()
            );
        } catch (ApplicationException $exception) {
            return $this->error(
                500,
                'INTERNAL_ERROR',
                $exception->getMessage()
            );
        }
    }

    /**
     * @param array<string,mixed> $input
     * @return array{status:int, body:array<string,mixed>}
     */
    public function updateStatus(int $id, array $input): array
    {
        try {
            $medication = $this->changeMedicationStatus->execute(
                $id,
                (string) ($input['status'] ?? '')
            );

            return [
                'status' => 200,
                'body' => [
                    'data' => [
                        'id' => $medication->id(),
                        'status' => $medication->status()->value,
                        'current' => $medication->isCurrent(),
                        'selectable' => $medication->isSelectable(),
                    ],
                ],
            ];
        } catch (ValidationException $exception) {
            if (str_contains(
                strtolower($exception->getMessage()),
                'no se encontró'
            )) {
                return $this->error(
                    404,
                    'MEDICATION_NOT_FOUND',
                    'El medicamento solicitado no existe.'
                );
            }

            return $this->error(
                422,
                'VALIDATION_ERROR',
                $exception->getMessage()
            );
        } catch (ApplicationException $exception) {
            return $this->error(
                500,
                'INTERNAL_ERROR',
                $exception->getMessage()
            );
        }
    }

    /**
     * @return array<string,mixed>
     */
    private function serializeMedication(Medication $medication): array
    {
        return [
            'id' => $medication->id(),
            'name' => $medication->name(),
            'genericName' => $medication->genericName(),
            'presentation' => $medication->presentation(),
            'concentration' => $medication->concentration(),
            'status' => $medication->status()->value,
        ];
    }

    /**
     * @return array{status:int, body:array<string,mixed>}
     */
    private function error(
        int $status,
        string $code,
        string $message
    ): array {
        return [
            'status' => $status,
            'body' => [
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
            ],
        ];
    }
}