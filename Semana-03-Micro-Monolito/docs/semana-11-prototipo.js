(function () {
    'use strict';

    const ADMIN_ROLE = 'Administrador';
    const initialMedications = [
        {
            id: 1,
            name: 'Alivion',
            genericName: 'Compuesto Alfa',
            presentation: 'Tabletas',
            concentration: '250 mg',
            status: 'ACTIVE',
        },
        {
            id: 2,
            name: 'Alivion Plus',
            genericName: 'Compuesto Beta',
            presentation: 'Solución',
            concentration: '100 mg/5 ml',
            status: 'INACTIVE',
        },
    ];

    let medications = initialMedications.map((medication) => ({ ...medication }));
    let currentRole = ADMIN_ROLE;
    let currentQuery = '';
    let pendingMedicationId = null;
    let dialogTrigger = null;

    const appShell = document.querySelector('#app-shell');
    const catalogView = document.querySelector('#catalog-view');
    const registerView = document.querySelector('#register-view');
    const successView = document.querySelector('#success-view');
    const resultsContainer = document.querySelector('#catalog-results');
    const searchForm = document.querySelector('#search-form');
    const searchInput = document.querySelector('#search-input');
    const clearSearchButton = document.querySelector('#clear-search');
    const searchSummary = document.querySelector('#search-summary');
    const resultCount = document.querySelector('#result-count');
    const resultLabel = document.querySelector('#result-label');
    const feedback = document.querySelector('#catalog-feedback');
    const roleSelect = document.querySelector('#role-select');
    const registerForm = document.querySelector('#register-form');
    const errorSummary = document.querySelector('#form-error-summary');
    const globalStatus = document.querySelector('#global-status');
    const modalBackdrop = document.querySelector('#modal-backdrop');
    const dialog = document.querySelector('#status-dialog');
    const dialogCancel = document.querySelector('#dialog-cancel');
    const dialogConfirm = document.querySelector('#dialog-confirm');

    const formFields = {
        name: document.querySelector('#medicine-name'),
        genericName: document.querySelector('#generic-name'),
        presentation: document.querySelector('#presentation'),
        concentration: document.querySelector('#concentration'),
        status: document.querySelector('#initial-status'),
    };

    function normalizeText(value) {
        return value
            .trim()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('es');
    }

    function escapeHtml(value) {
        const element = document.createElement('span');
        element.textContent = value;
        return element.innerHTML;
    }

    function isAdmin() {
        return currentRole === ADMIN_ROLE;
    }

    function setView(view, focusTarget) {
        [catalogView, registerView, successView].forEach((candidate) => {
            candidate.hidden = candidate !== view;
        });

        window.scrollTo(0, 0);
        window.setTimeout(() => focusTarget?.focus(), 0);
    }

    function statusText(status) {
        return status === 'ACTIVE' ? 'ACTIVO' : 'INACTIVO';
    }

    function statusCodeAndText(status) {
        return `${status} / ${statusText(status)}`;
    }

    function statusDescription(status) {
        return status === 'ACTIVE' ? 'Vigente y seleccionable' : 'No vigente ni seleccionable';
    }

    function actionText(status) {
        return status === 'ACTIVE' ? 'Desactivar' : 'Activar';
    }

    function filteredMedications() {
        const query = normalizeText(currentQuery);

        if (query === '') {
            return medications;
        }

        return medications.filter((medication) => (
            normalizeText(medication.name).includes(query)
            || normalizeText(medication.genericName).includes(query)
        ));
    }

    function actionButton(medication) {
        if (!isAdmin()) {
            return '';
        }

        const action = actionText(medication.status);
        return `<button class="status-action" type="button" data-toggle-id="${medication.id}" aria-label="${action} ${escapeHtml(medication.name)}">${action}</button>`;
    }

    function renderTable(items) {
        const actionHeader = isAdmin() ? '<th scope="col">Acción</th>' : '';
        const rows = items.map((medication) => `
            <tr class="${medication.status === 'INACTIVE' ? 'inactive-row' : ''}">
                <td><strong>${escapeHtml(medication.name)}</strong><small>${escapeHtml(medication.genericName)}</small></td>
                <td>${escapeHtml(medication.presentation)}<small>${escapeHtml(medication.concentration)}</small></td>
                <td><span class="status status-${medication.status.toLowerCase()}">${statusText(medication.status)}</span><small>${statusDescription(medication.status)}</small></td>
                ${isAdmin() ? `<td>${actionButton(medication)}</td>` : ''}
            </tr>
        `).join('');

        return `
            <div class="table-wrap">
                <table>
                    <thead><tr><th scope="col">Medicamento</th><th scope="col">Presentación</th><th scope="col">Vigencia</th>${actionHeader}</tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    }

    function renderCards(items) {
        const cards = items.map((medication) => `
            <article class="medicine-card ${medication.status === 'INACTIVE' ? 'inactive-row' : ''}">
                <div class="medicine-heading">
                    <h3>${escapeHtml(medication.name)}</h3>
                    <span class="status status-${medication.status.toLowerCase()}">${statusText(medication.status)}</span>
                </div>
                <p class="medicine-dose"><strong>${escapeHtml(medication.presentation)}</strong><span aria-hidden="true">/</span> ${escapeHtml(medication.concentration)}</p>
                <p class="medicine-generic"><span>Nombre genérico</span>${escapeHtml(medication.genericName)}</p>
                <p class="medicine-status-detail">${statusDescription(medication.status)}</p>
                ${actionButton(medication)}
            </article>
        `).join('');

        return `<div class="card-list" aria-label="Medicamentos encontrados">${cards}</div>`;
    }

    function renderEmptyState() {
        const hasQuery = currentQuery.trim() !== '';
        const title = hasQuery
            ? `No encontramos medicamentos para “${escapeHtml(currentQuery.trim())}”.`
            : 'No hay medicamentos registrados.';
        const detail = hasQuery
            ? 'Revise el término o limpie la búsqueda para recuperar el catálogo completo.'
            : 'Cuando se registre el primer medicamento, aparecerá aquí.';
        const clearAction = hasQuery ? '<button class="button button-quiet" type="button" data-clear-empty>Limpiar búsqueda</button>' : '';
        const registerAction = isAdmin() ? '<button class="button button-primary" type="button" data-register-empty>Registrar medicamento</button>' : '';

        return `
            <div class="empty-state">
                <div class="empty-symbol" aria-hidden="true">${hasQuery ? '0' : '+'}</div>
                <h3>${title}</h3>
                <p>${detail}</p>
                <div class="empty-actions">${clearAction}${registerAction}</div>
            </div>
        `;
    }

    function renderCatalog() {
        const items = filteredMedications();
        const filtering = currentQuery.trim() !== '';
        const noun = items.length === 1 ? 'coincidencia' : 'coincidencias';

        resultCount.textContent = String(items.length);
        resultLabel.textContent = filtering
            ? `${noun} en la búsqueda`
            : (items.length === 1 ? 'medicamento en catálogo' : 'medicamentos en catálogo');
        searchSummary.textContent = filtering
            ? `${items.length} ${noun} para “${currentQuery.trim()}”.`
            : `${items.length} ${items.length === 1 ? 'medicamento disponible' : 'medicamentos disponibles'}.`;
        clearSearchButton.hidden = !filtering;
        resultsContainer.innerHTML = items.length > 0
            ? `${renderTable(items)}${renderCards(items)}`
            : renderEmptyState();
    }

    function updateRole() {
        document.querySelectorAll('.admin-only').forEach((control) => {
            control.hidden = !isAdmin();
        });

        if (!isAdmin() && !registerView.hidden) {
            clearFormErrors();
            setView(catalogView, searchInput);
        }

        feedback.hidden = true;
        renderCatalog();
        globalStatus.textContent = `${currentRole}: consulta y búsqueda disponibles.${isAdmin() ? ' Registro y cambio de vigencia disponibles.' : ' Controles administrativos ocultos.'}`;
    }

    function showCatalog(options = {}) {
        if (options.clearSearch) {
            currentQuery = '';
            searchInput.value = '';
        }

        renderCatalog();
        setView(catalogView, options.focusSearch ? searchInput : document.querySelector('#catalog-title'));
    }

    function openRegister() {
        if (!isAdmin()) {
            return;
        }

        feedback.hidden = true;
        clearFormErrors();
        setView(registerView, formFields.name);
    }

    function clearFormErrors() {
        errorSummary.hidden = true;
        Object.entries(formFields).forEach(([name, field]) => {
            field.removeAttribute('aria-invalid');
            const error = document.querySelector(`#${field.id}-error`);
            if (error) {
                error.hidden = true;
                error.textContent = '';
            }
        });
    }

    function showFieldError(name, message) {
        const field = formFields[name];
        const error = document.querySelector(`#${field.id}-error`);
        field.setAttribute('aria-invalid', 'true');
        error.textContent = message;
        error.hidden = false;
    }

    function validateMedication(values) {
        const errors = {};
        const requiredMessages = {
            name: 'Ingrese el nombre comercial.',
            genericName: 'Ingrese el nombre genérico.',
            presentation: 'Ingrese la presentación.',
            concentration: 'Ingrese la concentración.',
        };

        Object.entries(requiredMessages).forEach(([name, message]) => {
            if (values[name] === '') {
                errors[name] = message;
            }
        });

        if (!['ACTIVE', 'INACTIVE'].includes(values.status)) {
            errors.status = 'Seleccione un estado válido.';
        }

        if (!errors.name && medications.some((medication) => normalizeText(medication.name) === normalizeText(values.name))) {
            errors.name = 'Ya existe un medicamento con ese nombre.';
        }

        return errors;
    }

    function showRegistrationSuccess(medication) {
        const preview = document.querySelector('#new-medication-preview');
        document.querySelector('#success-description').textContent = `${medication.name} ya está disponible en el catálogo simulado.`;
        preview.innerHTML = `
            <h2>${escapeHtml(medication.name)}</h2>
            <p><strong>Nombre genérico:</strong> ${escapeHtml(medication.genericName)}</p>
            <p><strong>Presentación:</strong> ${escapeHtml(medication.presentation)} / ${escapeHtml(medication.concentration)}</p>
            <p><strong>Vigencia:</strong> ${statusCodeAndText(medication.status)} / ${statusDescription(medication.status)}</p>
        `;
        setView(successView, document.querySelector('#success-title'));
        globalStatus.textContent = `Medicamento registrado correctamente. ${medication.name} ya está disponible en el catálogo.`;
    }

    function openStatusDialog(medicationId, trigger) {
        if (!isAdmin()) {
            return;
        }

        const medication = medications.find((item) => item.id === medicationId);
        if (!medication) {
            return;
        }

        const targetStatus = medication.status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        const action = actionText(medication.status);
        pendingMedicationId = medication.id;
        dialogTrigger = trigger;
        document.querySelector('#dialog-title').textContent = `¿${action} ${medication.name}?`;
        document.querySelector('#dialog-current-status').textContent = statusCodeAndText(medication.status);
        document.querySelector('#dialog-target-status').textContent = statusCodeAndText(targetStatus);
        document.querySelector('#dialog-effect').textContent = targetStatus === 'INACTIVE'
            ? 'El medicamento permanecerá en el catálogo, pero dejará de estar vigente y seleccionable.'
            : 'El medicamento volverá a estar vigente y seleccionable dentro de esta simulación.';
        dialogConfirm.textContent = `Confirmar ${action.toLocaleLowerCase('es')}`;
        modalBackdrop.hidden = false;
        appShell.setAttribute('inert', '');
        document.body.style.overflow = 'hidden';
        dialogCancel.focus();
    }

    function closeStatusDialog(returnFocus = true) {
        modalBackdrop.hidden = true;
        appShell.removeAttribute('inert');
        document.body.style.overflow = '';
        pendingMedicationId = null;

        if (returnFocus && dialogTrigger?.isConnected) {
            dialogTrigger.focus();
        }
    }

    function confirmStatusChange() {
        const medication = medications.find((item) => item.id === pendingMedicationId);
        if (!medication) {
            closeStatusDialog();
            return;
        }

        const priorStatus = medication.status;
        medication.status = priorStatus === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        const message = `${medication.name} fue ${priorStatus === 'ACTIVE' ? 'desactivado' : 'activado'} correctamente.`;

        closeStatusDialog(false);
        renderCatalog();
        feedback.textContent = message;
        feedback.hidden = false;
        globalStatus.textContent = message;
        const replacement = resultsContainer.querySelector(`[data-toggle-id="${medication.id}"]`);
        replacement?.focus();
        dialogTrigger = replacement;
    }

    function trapDialogFocus(event) {
        if (event.key !== 'Tab') {
            return;
        }

        const focusable = [...dialog.querySelectorAll('button:not([disabled])')];
        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        currentQuery = searchInput.value.trim();
        feedback.hidden = true;
        renderCatalog();
        globalStatus.textContent = searchSummary.textContent;
        document.querySelector('#list-title').focus?.();
    });

    clearSearchButton.addEventListener('click', () => showCatalog({ clearSearch: true, focusSearch: true }));
    document.querySelector('#open-register').addEventListener('click', openRegister);
    document.querySelector('#back-from-register').addEventListener('click', () => showCatalog());
    document.querySelector('#cancel-register').addEventListener('click', () => showCatalog());
    document.querySelector('#brand-home').addEventListener('click', () => showCatalog({ clearSearch: true }));
    document.querySelector('#success-catalog').addEventListener('click', () => showCatalog({ clearSearch: true }));
    document.querySelector('#success-register-another').addEventListener('click', () => {
        registerForm.reset();
        openRegister();
    });

    roleSelect.addEventListener('change', () => {
        currentRole = roleSelect.value;
        updateRole();
    });

    registerForm.addEventListener('submit', (event) => {
        event.preventDefault();
        clearFormErrors();

        const values = {
            name: formFields.name.value.trim(),
            genericName: formFields.genericName.value.trim(),
            presentation: formFields.presentation.value.trim(),
            concentration: formFields.concentration.value.trim(),
            status: formFields.status.value,
        };
        const errors = validateMedication(values);

        if (Object.keys(errors).length > 0) {
            Object.entries(errors).forEach(([name, message]) => showFieldError(name, message));
            errorSummary.hidden = false;
            globalStatus.textContent = 'Revise los campos indicados. Los datos correctos se conservaron.';
            formFields[Object.keys(errors)[0]].focus();
            return;
        }

        const medication = { id: Date.now(), ...values };
        medications.push(medication);
        registerForm.reset();
        showRegistrationSuccess(medication);
    });

    resultsContainer.addEventListener('click', (event) => {
        const toggleButton = event.target.closest('[data-toggle-id]');
        if (toggleButton) {
            openStatusDialog(Number(toggleButton.dataset.toggleId), toggleButton);
            return;
        }

        if (event.target.closest('[data-clear-empty]')) {
            showCatalog({ clearSearch: true, focusSearch: true });
            return;
        }

        if (event.target.closest('[data-register-empty]')) {
            openRegister();
        }
    });

    dialogCancel.addEventListener('click', () => closeStatusDialog());
    dialogConfirm.addEventListener('click', confirmStatusChange);
    dialog.addEventListener('keydown', trapDialogFocus);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modalBackdrop.hidden) {
            event.preventDefault();
            closeStatusDialog();
        }
    });

    renderCatalog();
}());
