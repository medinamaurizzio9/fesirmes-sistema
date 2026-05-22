import './bootstrap';
import Alpine from 'alpinejs';
import { toPng } from 'html-to-image';

window.Alpine = Alpine;

Alpine.start();

async function downloadCredentialPng(button) {
    const selector = button.dataset.credentialTarget || '[data-credential-card]';
    const card = document.querySelector(selector);

    if (! card) {
        return;
    }

    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Generando PNG...';

    try {
        await document.fonts?.ready;

        const dataUrl = await toPng(card, {
            cacheBust: true,
            pixelRatio: 4,
            backgroundColor: null,
            width: card.offsetWidth,
            height: card.offsetHeight,
            style: {
                transform: 'none',
                margin: '0',
            },
        });

        const link = document.createElement('a');
        link.download = button.dataset.filename || 'credencial-fesirmes.png';
        link.href = dataUrl;
        link.click();

        if (button.dataset.auditUrl) {
            window.axios.post(button.dataset.auditUrl).catch(() => {});
        }
    } finally {
        button.disabled = false;
        button.textContent = originalText;
    }
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-download-credential-png]');

    if (! button) {
        return;
    }

    event.preventDefault();
    downloadCredentialPng(button);
});

document.addEventListener('DOMContentLoaded', () => {
    const button = document.querySelector('[data-download-credential-png][data-auto-download="true"]');

    if (button) {
        downloadCredentialPng(button);
    }
});

document.addEventListener('change', (event) => {
    const input = event.target.closest('[data-photo-input]');

    if (! input || ! input.files?.[0]) {
        return;
    }

    const preview = document.querySelector('[data-photo-preview]');
    const placeholder = document.querySelector('[data-photo-placeholder]');

    if (! preview) {
        return;
    }

    preview.src = URL.createObjectURL(input.files[0]);
    preview.classList.remove('hidden');
    placeholder?.classList.add('hidden');
});
