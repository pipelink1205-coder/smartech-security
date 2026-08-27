(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const trackEl = document.getElementById('wa-click-track');
    const storeUrl = trackEl?.dataset.storeUrl;
    if (!storeUrl || !csrf) {
        return;
    }

    function inferSource(anchor) {
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

    function trackClick(anchor) {
        const payload = JSON.stringify({
            source: inferSource(anchor),
            page_url: window.location.href,
            page_title: document.title,
        });

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: payload,
            keepalive: true,
            credentials: 'same-origin',
        }).catch(function () {});
    }

    document.addEventListener('click', function (event) {
        const anchor = event.target.closest('a[href*="wa.me"]');
        if (!anchor) {
            return;
        }
        trackClick(anchor);
    }, true);
})();
