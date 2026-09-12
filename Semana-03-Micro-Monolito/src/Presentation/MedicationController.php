<?php

declare(strict_types=1);

namespace MicroHis\Presentation;

use MicroHis\Application\Exception\ApplicationException;
use MicroHis\Application\UseCase\RegisterMedication;
use MicroHis\Application\UseCase\SearchMedications;
use MicroHis\Application\UseCase\ToggleMedicationStatus;
use MicroHis\Domain\Exception\DuplicateMedicationException;
use MicroHis\Domain\Exception\ValidationException;

final class MedicationController
{
    public function __construct(
        private readonly RegisterMedication $registerMedication,
        private readonly SearchMedications $searchMedications,
        private readonly ToggleMedicationStatus $toggleMedicationStatus,
    ) {
    }

    /**
     * @param array<string, mixed> $server
     * @param array<string, mixed> $query
     * @param array<string, mixed> $form
     * @return array<string, mixed>
     */
    public function handle(array $server, array $query, array $form): array
    {
        $errors = [];
        $input = static function (array $source, string $key): string {
            $value = $source[$key] ?? '';
            return is_scalar($value) ? (string) $value : '';
        };
        $selectedStatus = $input($form, 'status');
        $formValues = [
            'name' => $input($form, 'name'),
            'generic_name' => $input($form, 'generic_name'),
            'presentation' => $input($form, 'presentation'),
            'concentration' => $input($form, 'concentration'),
            'status' => $selectedStatus === '' ? 'ACTIVE' : $selectedStatus,
        ];

        if (($server['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            try {
                $action = $input($form, 'action');

                if ($action === 'register') {
                    $medication = $this->registerMedication->execute(
                        $input($form, 'name'),
                        $input($form, 'generic_name'),
                        $input($form, 'presentation'),
                        $input($form, 'concentration'),
                        $input($form, 'status'),
                    );
                    $_SESSION['success'] = sprintf(
                        'Medicamento "%s" registrado correctamente.',
                        $medication->name(),
                    );

                    return ['redirect' => true];
                }

                if ($action === 'toggle') {
                    $medication = $this->toggleMedicationStatus->execute(
                        (int) $input($form, 'id')
                    );
                    $_SESSION['success'] = sprintf(
                        'El medicamento "%s" ahora está %s.',
                        $medication->name(),
                        $medication->status()->label(),
                    );

                    return ['redirect' => true];
                }

                throw new ValidationException('La acción solicitada no es válida.');
            } catch (ValidationException|DuplicateMedicationException|ApplicationException $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        $search = trim($input($query, 'q'));

        try {
            $medications = $this->searchMedications->execute($search);
        } catch (ApplicationException $exception) {
            $errors[] = $exception->getMessage();
            $medications = [];
        }

        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        return [
            'redirect' => false,
            'medications' => $medications,
            'search' => $search,
            'errors' => $errors,
            'success' => is_string($success) ? $success : null,
            'formValues' => $formValues,
        ];
    }
}
