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

document.addEventListener('change', (event) => {
    const input = event.target.closest('[data-csv-file-input]');

    if (! input || ! input.files?.[0]) {
        return;
    }

    const select = document.querySelector('[data-ci-column-select]');

    if (! select) {
        return;
    }

    const reader = new FileReader();
    reader.onload = () => {
        const firstLine = String(reader.result || '').split(/\r?\n/)[0] || '';
        const headers = firstLine.split(',').map((header) => header.trim()).filter(Boolean);

        select.innerHTML = '<option value="">Detectar automaticamente</option>';

        headers.forEach((header) => {
            const option = document.createElement('option');
            option.value = header;
            option.textContent = header;
            option.selected = header.toLowerCase() === 'ci' || header.toLowerCase().includes('c.i');
            select.appendChild(option);
        });
    };

    reader.readAsText(input.files[0].slice(0, 2048));
});
