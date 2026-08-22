<?php

declare(strict_types=1);

namespace MicroHis\Domain;

use MicroHis\Domain\Exception\ValidationException;

final class Medication
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $genericName,
        private readonly string $presentation,
        private readonly string $concentration,
        private MedicationStatus $status,
    ) {
        $this->assertRequired($this->name, 'nombre');
        $this->assertRequired($this->genericName, 'nombre genérico');
        $this->assertRequired($this->presentation, 'presentación');
        $this->assertRequired($this->concentration, 'concentración');
    }

    public static function register(
        string $name,
        string $genericName,
        string $presentation,
        string $concentration,
        string $status,
    ): self {
        return new self(
            null,
            trim($name),
            trim($genericName),
            trim($presentation),
            trim($concentration),
            MedicationStatus::fromInput($status),
        );
    }

    private function assertRequired(string $value, string $field): void
    {
        if (trim($value) === '') {
            throw new ValidationException(sprintf('El campo %s es obligatorio.', $field));
        }
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function genericName(): string
    {
        return $this->genericName;
    }

    public function presentation(): string
    {
        return $this->presentation;
    }

    public function concentration(): string
    {
        return $this->concentration;
    }

    public function status(): MedicationStatus
    {
        return $this->status;
    }

    public function isCurrent(): bool
    {
        return $this->status === MedicationStatus::ACTIVE;
    }

    public function isSelectable(): bool
    {
        return $this->isCurrent();
    }

    public function changeStatus(MedicationStatus $status): void
    {
        $this->status = $status;
    }
}
