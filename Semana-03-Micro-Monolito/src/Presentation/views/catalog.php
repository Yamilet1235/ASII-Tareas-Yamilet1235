<?php

declare(strict_types=1);

use MicroHis\Domain\Medication;

/** @var list<Medication> $medications */
/** @var array<string, mixed> $formValues */

$h = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$selectedStatus = (string) ($formValues['status'] ?? 'ACTIVE');
$resultCount = count($medications);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Micro-HIS | Catálogo de medicamentos</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <script src="/assets/app.js" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="header-content">
            <a class="brand" href="/" aria-label="Ir al catálogo completo">
                <span class="brand-mark">Rx</span>
                <span><strong>Micro-HIS</strong><small>Catálogo de medicamentos</small></span>
            </a>
            <span class="scope-label">Módulo académico</span>
        </div>
    </header>

    <main class="page-shell">
        <section class="hero">
            <div>
                <p class="eyebrow">Gestión farmacológica educativa</p>
                <h1>Medicamentos claros.<br><span>Vigencia controlada.</span></h1>
                <p class="hero-copy">Registre medicamentos ficticios, consulte el catálogo y controle su disponibilidad.</p>
            </div>
            <div class="hero-stat">
                <strong id="result-count"><?= $resultCount ?></strong>
                <span id="result-label"><?php
                    if ($search !== '') {
                        echo $resultCount === 1 ? 'resultado encontrado' : 'resultados encontrados';
                    } else {
                        echo $resultCount === 1 ? 'medicamento en catálogo' : 'medicamentos en catálogo';
                    }
                ?></span>
            </div>
        </section>

        <div id="message-region" class="message-region" aria-live="polite" aria-atomic="true">
            <?php if ($success !== null): ?>
                <div class="alert alert-success" role="status"><?= $h($success) ?></div>
            <?php endif; ?>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
            <?php endforeach; ?>
        </div>
        <span id="mutation-status" class="sr-only" role="status" aria-live="polite"></span>

        <div class="workspace">
            <section class="panel form-panel" aria-labelledby="register-title">
                <div class="panel-heading">
                    <span class="step">01</span>
                    <div><p>Nuevo registro</p><h2 id="register-title">Alta de medicamento</h2></div>
                </div>

                <form method="post" action="/" class="medication-form" data-async-form="register">
                    <input type="hidden" name="action" value="register">

                    <label>Nombre comercial
                        <input type="text" name="name" maxlength="120" required value="<?= $h($formValues['name'] ?? '') ?>" placeholder="Ej. Alivion">
                    </label>
                    <label>Nombre genérico
                        <input type="text" name="generic_name" maxlength="120" required value="<?= $h($formValues['generic_name'] ?? '') ?>" placeholder="Ej. analgésico ficticio">
                    </label>
                    <div class="form-row">
                        <label>Presentación
                            <input type="text" name="presentation" maxlength="100" required value="<?= $h($formValues['presentation'] ?? '') ?>" placeholder="Ej. tabletas">
                        </label>
                        <label>Concentración
                            <input type="text" name="concentration" maxlength="80" required value="<?= $h($formValues['concentration'] ?? '') ?>" placeholder="Ej. 250 mg">
                        </label>
                    </div>
                    <label>Estado inicial
                        <select name="status" required>
                            <option value="ACTIVE" <?= $selectedStatus === 'ACTIVE' ? 'selected' : '' ?>>ACTIVO</option>
                            <option value="INACTIVE" <?= $selectedStatus === 'INACTIVE' ? 'selected' : '' ?>>INACTIVO</option>
                        </select>
                    </label>
                    <button type="submit" class="primary-button" data-busy-label="Guardando...">Registrar medicamento</button>
                    <p class="form-note">Use únicamente nombres y datos ficticios.</p>
                </form>
            </section>

            <section class="panel catalog-panel" aria-labelledby="catalog-title">
                <div class="panel-heading catalog-heading">
                    <span class="step">02</span>
                    <div><p>Consulta y control</p><h2 id="catalog-title">Catálogo</h2></div>
                </div>

                <form method="get" action="/" class="search-form" role="search" data-async-form="search">
                    <label for="q" class="sr-only">Buscar por nombre o nombre genérico</label>
                    <input id="q" type="search" name="q" value="<?= $h($search) ?>" placeholder="Buscar por nombre o nombre genérico" autocomplete="off" aria-controls="catalog-results">
                    <button type="submit" data-busy-label="Buscando...">Buscar</button>
                    <a id="clear-search" href="/" <?= $search === '' ? 'hidden' : '' ?>>Limpiar</a>
                    <span id="search-status" class="operation-status" role="status" aria-live="polite"></span>
                </form>

                <div id="catalog-results" aria-live="polite">
                    <?php if ($medications === []): ?>
                        <div class="empty-state">
                            <strong>Sin resultados</strong>
                            <p>No hay medicamentos que coincidan con la consulta.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Medicamento</th><th>Presentación</th><th>Vigencia</th><th>Acción</th></tr></thead>
                                <tbody>
                                <?php foreach ($medications as $medication): ?>
                                    <tr class="<?= $medication->isCurrent() ? '' : 'inactive-row' ?>" data-medication-row data-name="<?= $h($medication->name()) ?>" data-generic-name="<?= $h($medication->genericName()) ?>">
                                        <td data-label="Medicamento">
                                            <strong><?= $h($medication->name()) ?></strong>
                                            <small><?= $h($medication->genericName()) ?></small>
                                        </td>
                                        <td data-label="Presentación">
                                            <?= $h($medication->presentation()) ?>
                                            <small><?= $h($medication->concentration()) ?></small>
                                        </td>
                                        <td data-label="Vigencia">
                                            <span class="status status-<?= strtolower($medication->status()->value) ?>">
                                                <?= $h($medication->status()->label()) ?>
                                            </span>
                                            <small><?= $medication->isSelectable() ? 'Vigente y seleccionable' : 'No vigente ni seleccionable' ?></small>
                                        </td>
                                        <td data-label="Acción">
                                            <form method="post" action="/" data-async-form="toggle">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= $medication->id() ?>">
                                                <button type="submit" class="text-button" data-busy-label="Actualizando...">
                                                    <?= $medication->isCurrent() ? 'Desactivar' : 'Activar' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <template id="empty-results-template">
                    <div class="empty-state">
                        <strong>Sin resultados</strong>
                        <p>No hay medicamentos que coincidan con la consulta.</p>
                    </div>
                </template>
            </section>
        </div>
    </main>

    <footer>María Yamilet Lindo Pablo · Micro-HIS académico · Datos ficticios</footer>
</body>
</html>
