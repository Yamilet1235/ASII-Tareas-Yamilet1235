<?php

declare(strict_types=1);

namespace MicroHis\Presentation;

final class MedicationWebRequest
{
    /**
     * @param array<string, mixed> $server
     * @param array<string, mixed> $query
     * @param array<string, mixed> $form
     */
    public function __construct(
        private readonly array $server,
        private readonly array $query,
        private readonly array $form,
    ) {
    }

    public function method(): string
    {
        return strtoupper(trim(
            $this->string($this->server, 'REQUEST_METHOD', 'GET')
        ));
    }

    public function action(): string
    {
        return trim($this->string($this->form, 'action'));
    }

    public function search(): string
    {
        return trim(
            $this->string($this->query, 'q')
        );
    }

    public function id(): int
    {
        return (int) $this->string($this->form, 'id');
    }

    /**
     * @return array{
     *   name:string,
     *   generic_name:string,
     *   presentation:string,
     *   concentration:string,
     *   status:string
     * }
     */
    public function medicationForm(): array
    {
        $status = $this->string($this->form, 'status');

        return [
            'name' => $this->string($this->form, 'name'),
            'generic_name' => $this->string(
                $this->form,
                'generic_name'
            ),
            'presentation' => $this->string(
                $this->form,
                'presentation'
            ),
            'concentration' => $this->string(
                $this->form,
                'concentration'
            ),
            'status' => $status === '' ? 'ACTIVE' : $status,
        ];
    }

    /**
     * @param array<string, mixed> $source
     */
    private function string(
        array $source,
        string $key,
        string $default = ''
    ): string {
        $value = $source[$key] ?? $default;

        return is_scalar($value)
            ? (string) $value
            : $default;
    }
}
