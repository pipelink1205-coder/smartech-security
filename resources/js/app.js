// ============================================
// SMART TECH SECURITY – app.js
// ============================================

const PAGE_BREAK = 769;
let currentPage = 'inicio';

function isMobileLayout() {
    return window.innerWidth <= PAGE_BREAK;
}

function closeMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenu?.classList.contains('open')) {
        mobileMenu.classList.remove('open');
        document.body.style.overflow = '';
    }
}

function setActiveNav(pageId) {
    document.querySelectorAll('[data-section]').forEach((dot) => {
        dot.classList.toggle('is-active', dot.dataset.section === pageId);
    });

    document.querySelectorAll('.nav-links a[data-page-link], .nav-links a[href^="#"]').forEach((link) => {
        const target = link.dataset.pageLink || link.getAttribute('href')?.slice(1);
        link.classList.toggle('active', target === pageId);
    });
}

function triggerPageReveals(root = document) {
    root.querySelectorAll('[data-aos]').forEach((el) => {
        if (!el.classList.contains('visible')) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            observer.observe(el);
        }
    });
}

window.showPage = function showPage(pageId, scrollBehavior = 'smooth') {
    const page = document.getElementById(pageId);
    if (!page) {
        return;
    }

    currentPage = pageId;
    setActiveNav(pageId);

    if (isMobileLayout()) {
        page.scrollIntoView({ behavior: scrollBehavior, block: 'start' });
        closeMobileMenu();
        return;
    }

    document.querySelectorAll('.site-page').forEach((p) => p.classList.remove('active'));
    page.classList.add('active');
    page.scrollTop = 0;
    window.scrollTo(0, 0);
    triggerPageReveals(page);
    window.dispatchEvent(new CustomEvent('smartech-show-page', { detail: { pageId } }));
};

function applyPagingMode() {
    const paging = !isMobileLayout();
    document.body.classList.toggle('site-paging', paging);

    if (paging) {
        const active = document.querySelector('.site-page.active')?.id || currentPage || 'inicio';
        window.showPage(active, 'auto');
    } else {
        document.querySelectorAll('.site-page').forEach((p) => p.classList.remove('active'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuClose = document.getElementById('mobile-menu-close');

    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
        });
        menuClose?.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            document.body.style.overflow = '';
        });
    }

    document.querySelectorAll('[data-page-link]').forEach((el) => {
        el.addEventListener('click', (e) => {
            const pageId = el.dataset.pageLink;
            if (!pageId) {
                return;
            }
            e.preventDefault();
            window.showPage(pageId);
        });
    });

    document.querySelectorAll('a[href^="#"]:not([data-page-link])').forEach((a) => {
        a.addEventListener('click', (e) => {
            const pageId = a.getAttribute('href')?.slice(1);
            if (!pageId || !document.getElementById(pageId)) {
                return;
            }
            e.preventDefault();
            window.showPage(pageId);
        });
    });

    const nav = document.getElementById('navbar');
    if (nav) {
        window.addEventListener('scroll', () => {
            if (!isMobileLayout()) {
                return;
            }
            nav.classList.toggle('is-scrolled', window.scrollY > 12);
        }, { passive: true });
    }

    triggerPageReveals(document);

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-num, .hero-stat-num').forEach((el) => counterObserver.observe(el));

    function animateCounter(el) {
        const text = el.textContent;
        const num = parseInt(text.replace(/\D/g, ''), 10);
        if (Number.isNaN(num)) {
            return;
        }
        const prefix = text.match(/^[^0-9]*/)[0];
        const suffix = text.match(/[^0-9]*$/)[0];
        let current = 0;
        const step = Math.ceil(num / 40);
        const timer = setInterval(() => {
            current = Math.min(current + step, num);
            el.textContent = prefix + current + suffix;
            if (current >= num) {
                clearInterval(timer);
            }
        }, 28);
    }

    if (isMobileLayout()) {
        const rail = document.getElementById('sectionRail');
        if (rail) {
            const pages = [...document.querySelectorAll('.site-page')];
            const spy = new IntersectionObserver((entries) => {
                entries.filter((e) => e.isIntersecting).forEach((e) => setActiveNav(e.target.id));
            }, { rootMargin: '-35% 0px -45% 0px', threshold: 0 });

            pages.forEach((p) => spy.observe(p));
        }
    }

    applyPagingMode();
    window.addEventListener('resize', applyPagingMode);
});
