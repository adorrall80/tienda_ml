import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-order-step]');

    if (! button) {
        return;
    }

    const stepper = button.closest('[data-order-stepper]');
    const input = stepper?.querySelector('input[type="number"]');

    if (! input) {
        return;
    }

    const min = input.min === '' ? null : Number(input.min);
    const current = Number.parseInt(input.value || '0', 10);
    const next = button.dataset.orderStep === 'up' ? current + 1 : current - 1;

    input.value = String(min === null ? next : Math.max(min, next));
    input.dispatchEvent(new Event('change', { bubbles: true }));

    const form = input.form;

    if (form) {
        form.requestSubmit();
    }
});
