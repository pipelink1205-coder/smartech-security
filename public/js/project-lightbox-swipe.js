/**
 * Swipe interactivo del lightbox de proyectos.
 * Debe cargarse ANTES de @livewireScripts para registrar Alpine.data a tiempo.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('projectLightboxSwipe', () => ({
        offset: 0,
        startX: 0,
        startY: 0,
        width: 0,
        dragging: false,
        settling: false,
        axis: null,
        lastX: 0,
        lastT: 0,
        velocity: 0,

        get trackStyle() {
            return `transform: translate3d(calc(-100% + ${this.offset}px), 0, 0)`;
        },

        onStart(event) {
            if (this.settling || !event.touches?.length) {
                return;
            }

            this.dragging = true;
            this.axis = null;
            this.width = this.$el.clientWidth || 1;
            this.startX = event.touches[0].clientX;
            this.startY = event.touches[0].clientY;
            this.lastX = this.startX;
            this.lastT = performance.now();
            this.velocity = 0;
            this.offset = 0;
        },

        onMove(event) {
            if (!this.dragging || !event.touches?.length) {
                return;
            }

            const x = event.touches[0].clientX;
            const y = event.touches[0].clientY;
            const dx = x - this.startX;
            const dy = y - this.startY;

            if (this.axis === null && (Math.abs(dx) > 8 || Math.abs(dy) > 8)) {
                this.axis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
            }

            if (this.axis !== 'x') {
                return;
            }

            event.preventDefault();

            const now = performance.now();
            const dt = Math.max(now - this.lastT, 1);
            this.velocity = (x - this.lastX) / dt;
            this.lastX = x;
            this.lastT = now;

            this.offset = dx * 0.92;
        },

        async onEnd() {
            if (!this.dragging) {
                return;
            }

            this.dragging = false;

            if (this.axis !== 'x') {
                this.offset = 0;
                this.axis = null;
                return;
            }

            const threshold = this.width * 0.22;
            const flick = Math.abs(this.velocity) > 0.55;
            const goNext = this.offset < -threshold || (flick && this.velocity < -0.55);
            const goPrev = this.offset > threshold || (flick && this.velocity > 0.55);

            this.axis = null;

            if (!goNext && !goPrev) {
                this.offset = 0;
                return;
            }

            this.settling = true;
            this.offset = goNext ? -this.width : this.width;

            await new Promise((resolve) => setTimeout(resolve, 240));

            try {
                if (goNext) {
                    await this.$wire.nextImage();
                } else {
                    await this.$wire.prevImage();
                }
            } finally {
                this.$el.classList.add('is-resetting');
                this.offset = 0;
                await this.$nextTick();
                requestAnimationFrame(() => {
                    this.$el.classList.remove('is-resetting');
                    this.settling = false;
                });
            }
        },
    }));
});
