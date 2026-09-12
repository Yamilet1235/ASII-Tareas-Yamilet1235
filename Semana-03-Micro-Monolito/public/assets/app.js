const searchForm = document.querySelector('[data-async-form="search"]');
const searchInput = document.querySelector('#q');
const searchStatus = document.querySelector('#search-status');
const clearSearch = document.querySelector('#clear-search');
const messageRegion = document.querySelector('#message-region');
const mutationStatus = document.querySelector('#mutation-status');
const busyForms = new WeakSet();

let debounceTimer;
let searchController;
let mutationInProgress = false;

function normalizeText(value) {
    return value.normalize('NFC').toLocaleLowerCase('es');
}

function parseDocument(html) {
    return new DOMParser().parseFromString(html, 'text/html');
}

function setButtonBusy(button, busy) {
    if (!button) {
        return;
    }

    if (busy) {
        button.dataset.originalLabel = button.textContent.trim();
        button.textContent = button.dataset.busyLabel || 'Procesando...';
        button.disabled = true;
        return;
    }

    button.textContent = button.dataset.originalLabel || button.textContent;
    button.disabled = false;
    delete button.dataset.originalLabel;
}

function updateCounter(count, filtering) {
    const counter = document.querySelector('#result-count');
    const label = document.querySelector('#result-label');

    counter.textContent = String(count);
    label.textContent = filtering
        ? (count === 1 ? 'resultado encontrado' : 'resultados encontrados')
        : (count === 1 ? 'medicamento en catálogo' : 'medicamentos en catálogo');
}

function updateCatalog(sourceDocument, query) {
    const incoming = sourceDocument.querySelector('#catalog-results');
    const current = document.querySelector('#catalog-results');

    if (!incoming || !current) {
        throw new Error('La respuesta no contiene el catálogo esperado.');
    }

    const normalizedQuery = normalizeText(query.trim());
    const rows = [...incoming.querySelectorAll('[data-medication-row]')];

    if (normalizedQuery !== '') {
        rows.forEach((row) => {
            const name = normalizeText(row.dataset.name || '');
            const genericName = normalizeText(row.dataset.genericName || '');

            if (!name.includes(normalizedQuery) && !genericName.includes(normalizedQuery)) {
                row.remove();
            }
        });
    }

    const visibleRows = incoming.querySelectorAll('[data-medication-row]').length;
    if (visibleRows === 0 && rows.length > 0) {
        const template = document.querySelector('#empty-results-template');
        incoming.replaceChildren(template.content.cloneNode(true));
    }

    current.replaceWith(incoming);
    updateCounter(visibleRows, normalizedQuery !== '');
    clearSearch.hidden = normalizedQuery === '';
}

function updateMessages(sourceDocument) {
    const incoming = sourceDocument.querySelector('#message-region');
    messageRegion.replaceChildren(...(incoming ? [...incoming.childNodes] : []));
}

function showClientError(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-error';
    alert.setAttribute('role', 'alert');
    alert.textContent = message;
    messageRegion.replaceChildren(alert);
}

function updateAddress(query) {
    const url = new URL(window.location.href);

    if (query.trim() === '') {
        url.searchParams.delete('q');
    } else {
        url.searchParams.set('q', query.trim());
    }

    window.history.replaceState({}, '', url);
}

async function loadCatalog(query, submitButton = null) {
    if (mutationInProgress) {
        clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => loadCatalog(searchInput.value, submitButton), 300);
        return;
    }

    searchController?.abort();
    searchController = new AbortController();
    const activeController = searchController;
    const results = document.querySelector('#catalog-results');
    let completed = false;

    searchStatus.textContent = 'Buscando...';
    results.setAttribute('aria-busy', 'true');
    setButtonBusy(submitButton, true);

    try {
        const url = new URL(searchForm.action, window.location.href);
        url.searchParams.set('q', '');
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'fetch' },
            signal: activeController.signal,
        });

        if (!response.ok) {
            throw new Error('No fue posible actualizar el catálogo.');
        }

        updateCatalog(parseDocument(await response.text()), query);
        updateAddress(query);
        completed = true;
    } catch (error) {
        if (error.name === 'AbortError') {
            searchStatus.textContent = '';
        } else {
            searchStatus.textContent = 'No se pudo completar la búsqueda.';
        }
    } finally {
        if (searchController === activeController) {
            document.querySelector('#catalog-results')?.removeAttribute('aria-busy');
            if (completed) {
                searchStatus.textContent = '';
            }
            setButtonBusy(submitButton, false);
        }
    }
}

async function submitMutation(form, submitButton) {
    if (busyForms.has(form) || mutationInProgress) {
        return;
    }

    mutationInProgress = true;
    busyForms.add(form);
    clearTimeout(debounceTimer);
    searchController?.abort();
    searchStatus.textContent = '';
    const formType = form.dataset.asyncForm;
    const medicationId = form.querySelector('[name="id"]')?.value || null;
    mutationStatus.textContent = formType === 'register' ? 'Guardando...' : 'Actualizando...';
    form.setAttribute('aria-busy', 'true');
    setButtonBusy(submitButton, true);

    try {
        const response = await fetch(form.action, {
            method: form.method,
            body: new FormData(form),
            headers: { 'X-Requested-With': 'fetch' },
        });

        if (!response.ok) {
            throw new Error('No fue posible procesar la operación.');
        }

        const sourceDocument = parseDocument(await response.text());
        const wasSuccessful = sourceDocument.querySelector('.alert-success') !== null;
        updateMessages(sourceDocument);
        updateCatalog(sourceDocument, searchInput.value);

        if (formType === 'register' && wasSuccessful) {
            form.reset();
            form.querySelector('[name="status"]').value = 'ACTIVE';
        }

        if (formType === 'toggle' && medicationId !== null) {
            const replacementForm = [...document.querySelectorAll('[data-async-form="toggle"]')]
                .find((candidate) => candidate.querySelector('[name="id"]')?.value === medicationId);
            replacementForm?.querySelector('[type="submit"]')?.focus();
        }
    } catch (error) {
        showClientError(error.message || 'Ocurrió un error inesperado.');
    } finally {
        mutationInProgress = false;
        busyForms.delete(form);
        form.removeAttribute('aria-busy');
        mutationStatus.textContent = '';
        setButtonBusy(submitButton, false);
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-async-form]');

    if (!form) {
        return;
    }

    event.preventDefault();
    const submitButton = event.submitter || form.querySelector('[type="submit"]');

    if (form.dataset.asyncForm === 'search') {
        clearTimeout(debounceTimer);
        loadCatalog(searchInput.value, submitButton);
        return;
    }

    submitMutation(form, submitButton);
});

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    searchController?.abort();
    debounceTimer = window.setTimeout(() => loadCatalog(searchInput.value), 300);
});

clearSearch.addEventListener('click', (event) => {
    event.preventDefault();
    clearTimeout(debounceTimer);
    searchInput.value = '';
    loadCatalog('');
    searchInput.focus();
});
