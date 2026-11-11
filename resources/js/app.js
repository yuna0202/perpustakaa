document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    const menu = document.getElementById(button.getAttribute('aria-controls'));
    if (!menu) return;

    const closeMenu = () => {
        button.setAttribute('aria-expanded', 'false');
        menu.hidden = true;
    };

    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        menu.hidden = expanded;
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.hidden) {
            closeMenu();
            button.focus();
        }
    });

    menu.addEventListener('click', (event) => {
        if (event.target.closest('a')) closeMenu();
    });
});

document.querySelectorAll('[data-print]').forEach((button) => {
    button.addEventListener('click', () => window.print());
});

document.querySelectorAll('[data-faculty-select]').forEach((select) => {
    const output = document.querySelector('[data-faculty-output]');
    if (!output) return;
    const update = () => { output.value = select.selectedOptions[0]?.dataset.faculty ?? ''; };
    select.addEventListener('change', update);
    update();
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.dataset.passwordToggle);
    if (!input) return;
    button.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        const label = visible ? 'Sembunyikan password' : 'Lihat password';
        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
        button.querySelector('[data-eye-open]')?.classList.toggle('hidden', visible);
        button.querySelector('[data-eye-closed]')?.classList.toggle('hidden', !visible);
    });
});

const firstInvalidField = document.querySelector('[aria-invalid="true"]');
if (firstInvalidField && !document.querySelector('[autofocus]')) {
    firstInvalidField.focus();
}

document.querySelectorAll('form[method="POST"], form[method="post"]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
            event.preventDefault();
            return;
        }
        // Disabled submitters are not submitted, so preserve the clicked value.
        if (event.submitter?.name) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = event.submitter.name;
            input.value = event.submitter.value;
            input.dataset.submitterValue = 'true';
            form.appendChild(input);
        }
        form.dataset.submitting = 'true';
        form.setAttribute('aria-busy', 'true');
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
            if (!button.disabled) {
                button.dataset.disabledOnSubmit = 'true';
                button.disabled = true;
            }
        });
    });
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submitting="true"]').forEach((form) => {
        delete form.dataset.submitting;
        form.removeAttribute('aria-busy');
        form.querySelectorAll('[data-disabled-on-submit]').forEach((button) => {
            button.disabled = false;
            delete button.dataset.disabledOnSubmit;
        });
        form.querySelectorAll('[data-submitter-value]').forEach((input) => input.remove());
    });
});
