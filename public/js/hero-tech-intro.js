(() => {
    const intro = document.querySelector('[data-hero-tech-intro]');
    if (!intro) return;

    // Replays once per tab session; in local development, ?intro=force bypasses that marker.
    const storageKey = 'smarttech.heroIntro.seen.v1';
    const force = intro.dataset.force === 'true';
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reducedMotion) return;

    if (!force) {
        try {
            if (sessionStorage.getItem(storageKey)) return;
            sessionStorage.setItem(storageKey, '1');
        } catch (_) {
            // If session storage is unavailable, leave the existing Hero usable.
            return;
        }
    }

    const stage = intro.closest('.home-hero-stage');
    const target = stage?.querySelector('.hero-btns a[data-page-link="servicios"]');
    const cards = [...intro.querySelectorAll('.hero-tech-intro__card')];
    const skip = intro.querySelector('[data-hero-tech-intro-skip]');
    let finished = false;
    let finishTimer;

    intro.hidden = false;
    if (target) {
        const stageRect = stage.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const targetX = targetRect.left + targetRect.width / 2;
        const targetY = targetRect.top + targetRect.height / 2;
        const centerX = stageRect.left + stageRect.width / 2;
        const centerY = stageRect.top + stageRect.height / 2;

        cards.forEach((card) => {
            card.style.transform = 'none';
            const rect = card.getBoundingClientRect();
            const cardX = rect.left + rect.width / 2;
            const cardY = rect.top + rect.height / 2;
            card.style.setProperty('--from-x', `${centerX - cardX}px`);
            card.style.setProperty('--from-y', `${centerY - cardY}px`);
            card.style.setProperty('--intro-to-x', `${targetX - cardX}px`);
            card.style.setProperty('--intro-to-y', `${targetY - cardY}px`);
            card.style.transform = '';
        });
    }

    const finish = (pulse = false) => {
        if (finished) return;
        finished = true;
        window.clearTimeout(finishTimer);
        intro.classList.add('is-leaving');
        if (pulse && target) {
            window.setTimeout(() => target.classList.add('hero-tech-intro-services-pulse'), 320);
        }
        window.setTimeout(() => {
            intro.hidden = true;
            intro.classList.remove('is-playing', 'is-leaving');
        }, 320);
    };

    skip?.addEventListener('click', () => finish(false), { once: true });
    intro.addEventListener('animationend', (event) => {
        if (event.target === intro && event.animationName === 'hero-tech-intro-out') finish(true);
    });

    // Reveal the overlay only after its final positions and convergence vectors are known.
    requestAnimationFrame(() => {
        intro.classList.add('is-playing');
        finishTimer = window.setTimeout(() => finish(true), 5450);
    });

    const pointerQuery = window.matchMedia('(hover: hover) and (pointer: fine)');
    if (pointerQuery.matches) {
        let frame = 0;
        stage?.addEventListener('pointermove', (event) => {
            if (finished || !intro.classList.contains('is-playing') || frame) return;
            frame = requestAnimationFrame(() => {
                const bounds = stage.getBoundingClientRect();
                const dx = ((event.clientX - bounds.left) / bounds.width - 0.5) * 8;
                const dy = ((event.clientY - bounds.top) / bounds.height - 0.5) * 8;
                intro.style.setProperty('--parallax-x', `${dx}px`);
                intro.style.setProperty('--parallax-y', `${dy}px`);
                frame = 0;
            });
        }, { passive: true });
    }
})();
