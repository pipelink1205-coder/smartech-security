// ============================================
// SMART TECH SECURITY – app.js
// ============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Hamburger menu ──────────────────────────
    const hamburger   = document.getElementById('hamburger');
    const mobileMenu  = document.getElementById('mobileMenu');
    const menuClose   = document.getElementById('menuClose');

    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
        });
        menuClose?.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            document.body.style.overflow = '';
        });
        mobileMenu.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    }

    // ── Navbar scroll shadow ────────────────────
    const nav = document.getElementById('navbar');
    if (nav) {
        window.addEventListener('scroll', () => {
            nav.style.boxShadow = window.scrollY > 10
                ? '0 4px 24px rgba(0,0,0,.12)'
                : '0 1px 3px rgba(0,0,0,.08)';
        }, { passive: true });
    }

    // ── Scroll reveal ────────────────────────────
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));

    // ── Stat counters ────────────────────────────
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-num, .hero-stat-num').forEach(el => counterObserver.observe(el));

    function animateCounter(el) {
        const text   = el.textContent;
        const num    = parseInt(text.replace(/\D/g,''));
        if (isNaN(num)) return;
        const prefix = text.match(/^[^0-9]*/)[0];
        const suffix = text.match(/[^0-9]*$/)[0];
        let current  = 0;
        const step   = Math.ceil(num / 40);
        const timer  = setInterval(() => {
            current = Math.min(current + step, num);
            el.textContent = prefix + current + suffix;
            if (current >= num) clearInterval(timer);
        }, 28);
    }

    // ── Smooth anchor scroll ─────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
