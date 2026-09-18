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

    /** @return array<string, mixed> */
    public function handle(MedicationWebRequest $request): array
    {
        $errors = [];
        $formValues = $request->medicationForm();

        if ($request->method() === 'POST') {
            try {
                $action = $request->action();

                if ($action === 'register') {
                    $medication = $this->registerMedication->execute(
                        $formValues['name'],
                        $formValues['generic_name'],
                        $formValues['presentation'],
                        $formValues['concentration'],
                        $formValues['status'],
                    );
                    $_SESSION['success'] = sprintf(
                        'Medicamento "%s" registrado correctamente.',
                        $medication->name(),
                    );

                    return ['redirect' => true];
                }

                if ($action === 'toggle') {
                    $medication = $this->toggleMedicationStatus->execute(
                        $request->id()
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

        $search = $request->search();

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
