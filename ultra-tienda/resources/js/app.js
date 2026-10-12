const THEME_KEY = 'theme';
const SIDEBAR_KEY = 'sidebar-collapsed';

const root = document.documentElement;
const mobileBreakpoint = () => window.matchMedia('(min-width: 64rem)').matches;

/* --------------------------------- Tema ---------------------------------- */

function applyTheme(theme) {
    root.classList.toggle('dark', theme === 'dark');

    try {
        localStorage.setItem(THEME_KEY, theme);
    } catch (error) {
        /* almacenamiento no disponible */
    }

    const label = theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('title', label);
        button.setAttribute('aria-label', label);
    });
}

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
    });
});

/* -------------------------------- Sidebar -------------------------------- */

function setCollapsed(collapsed) {
    if (collapsed) {
        root.setAttribute('data-sidebar', 'collapsed');
    } else {
        root.removeAttribute('data-sidebar');
    }

    try {
        localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0');
    } catch (error) {
        /* almacenamiento no disponible */
    }

    document.querySelectorAll('[data-sidebar-collapse]').forEach((button) => {
        button.setAttribute('aria-pressed', String(collapsed));
        button.setAttribute('title', collapsed ? 'Expandir menú' : 'Colapsar menú');
        button.setAttribute('aria-label', collapsed ? 'Expandir menú' : 'Colapsar menú');
    });
}

function setMobileOpen(open) {
    if (open) {
        root.setAttribute('data-mobile-open', '');
    } else {
        root.removeAttribute('data-mobile-open');
    }
}

setCollapsed(root.getAttribute('data-sidebar') === 'collapsed');

document.querySelectorAll('[data-sidebar-collapse]').forEach((button) => {
    button.addEventListener('click', () => {
        setCollapsed(root.getAttribute('data-sidebar') !== 'collapsed');
    });
});

document.querySelectorAll('[data-sidebar-open]').forEach((button) => {
    button.addEventListener('click', () => setMobileOpen(true));
});

document.querySelectorAll('[data-sidebar-close], [data-sidebar-backdrop]').forEach((element) => {
    element.addEventListener('click', () => setMobileOpen(false));
});

document.querySelectorAll('.ut-nav-link').forEach((link) => {
    link.addEventListener('click', () => {
        if (!mobileBreakpoint()) setMobileOpen(false);
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') setMobileOpen(false);
});

window.addEventListener('resize', () => {
    if (mobileBreakpoint()) setMobileOpen(false);
});

/* ------------------------ Diálogo de confirmación ------------------------ */

const CONFIRM_ICON =
    '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />' +
    '<path d="M12 9v4" /><path d="M12 17h.01" />';

let confirmDialog = null;
let confirmAction = null;

function getConfirmDialog() {
    if (confirmDialog) return confirmDialog;

    const dialog = document.createElement('dialog');
    dialog.className = 'ut-dialog';
    dialog.setAttribute('aria-labelledby', 'ut-confirm-title');
    dialog.innerHTML = `
        <div class="flex gap-4 p-6">
            <span class="ut-dialog-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${CONFIRM_ICON}</svg>
            </span>
            <div class="flex flex-col gap-1.5">
                <h2 id="ut-confirm-title" class="font-bold"></h2>
                <p class="text-sm leading-relaxed text-muted-foreground"></p>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t bg-muted/50 px-6 py-3">
            <button type="button" class="ut-btn ut-btn-outline ut-btn-sm" data-confirm-cancel>Cancelar</button>
            <button type="button" class="ut-btn ut-btn-danger ut-btn-sm" data-confirm-accept>Sí, eliminar</button>
        </div>`;

    dialog.querySelector('[data-confirm-cancel]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => {
        confirmAction = null;
    });

    document.body.appendChild(dialog);

    confirmDialog = dialog;

    return dialog;
}

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;
    if (form.dataset.confirmed === 'true') return;

    event.preventDefault();

    const dialog = getConfirmDialog();
    const [title, description] = form.dataset.confirm.split('|');

    dialog.querySelector('#ut-confirm-title').textContent = (title || '¿Confirmar acción?').trim();
    dialog.querySelector('p').textContent = (description || 'Esta acción no se puede deshacer.').trim();

    confirmAction = () => {
        form.dataset.confirmed = 'true';
        form.submit();
    };

    dialog.querySelector('[data-confirm-accept]').onclick = () => {
        dialog.close();
        if (confirmAction) confirmAction();
    };

    dialog.showModal();
});

/* --------------------------- Buscador de tablas -------------------------- */

function filterTable(table) {
    const query = (table.dataset.query || '').trim().toLowerCase();
    const status = table.dataset.status || 'all';
    const rows = Array.from(table.querySelectorAll('tbody tr[data-search]'));

    if (rows.length === 0) return;

    let visible = 0;

    rows.forEach((row) => {
        const matchesQuery = !query || (row.dataset.search || '').toLowerCase().includes(query);
        const matchesStatus = status === 'all' || row.dataset.status === status;
        const show = matchesQuery && matchesStatus;

        row.hidden = !show;
        if (show) visible += 1;
    });

    let emptyRow = table.querySelector('tbody tr[data-empty-row]');

    if (!emptyRow) {
        const columns = table.tHead?.rows[0]?.cells.length || 1;

        emptyRow = document.createElement('tr');
        emptyRow.setAttribute('data-empty-row', '');
        emptyRow.innerHTML = `
            <td colspan="${columns}">
                <div class="ut-empty">
                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 12h-6l-2 3h-4l-2-3H2" />
                            <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                        </svg>
                    </span>
                    <p class="font-semibold">Sin resultados</p>
                    <p class="text-sm text-muted-foreground">Ajusta la búsqueda o los filtros.</p>
                </div>
            </td>`;

        table.querySelector('tbody').appendChild(emptyRow);
    }

    emptyRow.hidden = visible > 0;
}

function bindTableControls() {
    document.querySelectorAll('[data-table-filter]').forEach((input) => {
        const table = document.getElementById(input.getAttribute('data-table-filter'));
        if (!table) return;

        input.addEventListener('input', () => {
            table.dataset.query = input.value;
            filterTable(table);
        });
    });

    document.querySelectorAll('[data-table-select]').forEach((select) => {
        const table = document.getElementById(select.getAttribute('data-table-select'));
        if (!table) return;

        select.addEventListener('change', () => {
            table.dataset.status = select.value;
            filterTable(table);
        });
    });
}

bindTableControls();

/* --------------------------- Navegación rápida --------------------------- */

const quickNav = document.querySelector('[data-quick-nav]');

if (quickNav) {
    let items = [];

    try {
        items = JSON.parse(quickNav.getAttribute('data-quick-nav-items') || '[]');
    } catch (error) {
        items = [];
    }

    quickNav.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;

        const query = quickNav.value.trim().toLowerCase();
        if (!query) return;

        const match =
            items.find((item) => item.label.toLowerCase() === query) ||
            items.find((item) => item.label.toLowerCase().includes(query));

        if (match) {
            event.preventDefault();
            window.location.href = match.url;
        }
    });
}

document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        if (!quickNav || quickNav.offsetParent === null) return;

        event.preventDefault();
        quickNav.focus();
        quickNav.select();
    }
});
