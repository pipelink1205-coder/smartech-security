(function () {
    const modal = document.getElementById('wa-lead-modal');
    if (!modal) {
        return;
    }

    const form = document.getElementById('wa-lead-form');
    const errorEl = document.getElementById('wa-lead-error');
    const submitBtn = document.getElementById('wa-lead-submit');
    const nameInput = document.getElementById('wa-name');
    const phoneInput = document.getElementById('wa-phone');
    const serviceInput = document.getElementById('wa-service');
    const messageInput = document.getElementById('wa-message');
    const storeUrl = modal.dataset.storeUrl;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    let pendingHref = '';
    let pendingMeta = {};

    function destinationFromHref(href) {
        try {
            return new URL(href, window.location.origin).pathname.replace(/^\//, '');
        } catch (e) {
            return '';
        }
    }

    function inferSource(anchor) {
        if (anchor.dataset.waSource) {
            return anchor.dataset.waSource;
        }
        if (anchor.classList.contains('whatsapp-fab')) {
            return 'fab';
        }
        if (anchor.closest('footer')) {
            return 'footer';
        }
        if (anchor.closest('.sd-hero')) {
            return 'service_hero';
        }
        if (anchor.closest('.sd-quote')) {
            return 'service_sidebar';
        }
        if (anchor.closest('.sd-cta')) {
            return 'service_cta';
        }
        if (anchor.closest('.legal')) {
            return 'legal';
        }
        if (anchor.closest('.contacto-info') || anchor.closest('#contacto')) {
            return 'contact';
        }
        return 'link';
    }

    function inferService(anchor) {
        return anchor.dataset.waService
            || anchor.closest('[data-wa-service]')?.dataset.waService
            || '';
    }

    function setServiceValue(value) {
        if (!value) {
            return;
        }
        const match = [...serviceInput.options].some((opt) => opt.value === value);
        if (match) {
            serviceInput.value = value;
        }
    }

    function showError(message) {
        errorEl.textContent = message;
        errorEl.hidden = !message;
    }

    function openModal(anchor) {
        pendingHref = anchor.getAttribute('href') || '';
        pendingMeta = {
            source: inferSource(anchor),
            service: inferService(anchor),
            page_url: window.location.href,
            page_title: document.title,
            destination: destinationFromHref(pendingHref),
        };

        try {
            nameInput.value = nameInput.value || localStorage.getItem('sts_wa_name') || '';
            phoneInput.value = phoneInput.value || localStorage.getItem('sts_wa_phone') || '';
        } catch (e) {
            // ignore storage
        }

        setServiceValue(pendingMeta.service);
        showError('');
        modal.hidden = false;
        document.body.classList.add('body-scroll-locked');
        nameInput.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('body-scroll-locked');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Continuar a WhatsApp';
    }

    document.addEventListener('click', (event) => {
        const closeBtn = event.target.closest('[data-wa-close]');
        if (closeBtn && modal.contains(closeBtn)) {
            closeModal();
            return;
        }

        const anchor = event.target.closest('a[href*="wa.me"]');
        if (!anchor || modal.contains(anchor)) {
            return;
        }

        event.preventDefault();
        openModal(anchor);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError('');

        const payload = {
            name: nameInput.value.trim(),
            phone: phoneInput.value.trim(),
            service: serviceInput.value,
            message: messageInput.value.trim(),
            website: document.getElementById('wa-website').value,
            source: pendingMeta.source,
            page_url: pendingMeta.page_url,
            page_title: pendingMeta.page_title,
            destination: pendingMeta.destination,
        };

        if (payload.name.length < 3 || !payload.phone || !payload.service) {
            showError('Completa nombre, teléfono y servicio para continuar.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Abriendo WhatsApp…';

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));

            if (response.status === 422) {
                const first = data.errors ? Object.values(data.errors)[0] : null;
                showError((first && first[0]) || 'Revisa los datos e inténtalo de nuevo.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Continuar a WhatsApp';
                return;
            }

            if (!response.ok || !data.whatsapp_url) {
                showError('No se pudo registrar el contacto. Se abrirá WhatsApp igual.');
            }

            try {
                localStorage.setItem('sts_wa_name', payload.name);
                localStorage.setItem('sts_wa_phone', payload.phone);
            } catch (e) {
                // ignore storage
            }

            const url = data.whatsapp_url || pendingHref;
            window.open(url, '_blank', 'noopener');
            closeModal();
        } catch (e) {
            window.open(pendingHref, '_blank', 'noopener');
            closeModal();
        }
    });
})();
