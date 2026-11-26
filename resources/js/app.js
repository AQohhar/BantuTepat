import './bootstrap';

const siteHeader = document.querySelector('.site-header');

if (siteHeader && 'ResizeObserver' in window) {
    const updateHeaderHeight = () => {
        document.documentElement.style.setProperty('--site-header-height', `${siteHeader.getBoundingClientRect().height}px`);
    };

    updateHeaderHeight();
    new ResizeObserver(updateHeaderHeight).observe(siteHeader);
}

const mobileMenu = document.querySelector('.nav-mobile');

if (mobileMenu) {
    document.addEventListener('pointerdown', (event) => {
        if (mobileMenu.open && !mobileMenu.contains(event.target)) {
            mobileMenu.open = false;
        }
    });

    mobileMenu.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileMenu.open) {
            mobileMenu.open = false;
            mobileMenu.querySelector('summary').focus();
        }
    });
}

const catalogFilters = document.querySelector('[data-catalog-filters]');

if (catalogFilters) {
    const desktop = window.matchMedia('(min-width: 64rem)');
    const updateFilterPanel = () => {
        catalogFilters.open = desktop.matches || catalogFilters.hasAttribute('data-filter-errors');
    };

    updateFilterPanel();
    desktop.addEventListener('change', updateFilterPanel);
}

const programNav = document.querySelector('[data-program-sections]');

if (programNav) {
    const detail = programNav.closest('.program-detail');
    const stickySections = window.matchMedia('(width < 52rem) and (height >= 32rem)');
    const sections = [...programNav.querySelectorAll('a[href^="#"]')].map((link) => ({
        link,
        panel: document.getElementById(link.hash.slice(1)),
    })).filter(({ panel }) => panel);
    let activeLink;
    let frame;

    const updateCurrentSection = () => {
        frame = undefined;
        const hashLink = sections.find(({ link }) => link.hash === window.location.hash)?.link;
        let current = hashLink;

        if (stickySections.matches) {
            const navBottom = programNav.getBoundingClientRect().bottom;
            const readingLine = navBottom + 24;
            current = undefined;
            for (const { link, panel } of sections) {
                if (panel.getBoundingClientRect().top <= readingLine) current = link;
            }

            if (document.documentElement.scrollHeight - window.innerHeight - window.scrollY <= 2) {
                current = sections.at(-1)?.link;
            }
        }

        if (current !== activeLink) {
            activeLink?.removeAttribute('aria-current');
            current?.setAttribute('aria-current', 'location');
            activeLink = current;
        }
    };

    const scheduleUpdate = () => {
        frame ??= window.requestAnimationFrame(updateCurrentSection);
    };

    const measureSectionNav = () => {
        detail.style.setProperty('--program-nav-height', `${programNav.getBoundingClientRect().height}px`);
        scheduleUpdate();
    };

    measureSectionNav();
    if ('ResizeObserver' in window) {
        new ResizeObserver(measureSectionNav).observe(programNav);
    }
    window.addEventListener('scroll', scheduleUpdate, { passive: true });
    window.addEventListener('hashchange', scheduleUpdate);
    stickySections.addEventListener('change', scheduleUpdate);
}

const motionItems = [...document.querySelectorAll('[data-motion-reveal]')];

if (motionItems.length && 'IntersectionObserver' in window) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const seen = new WeakSet();
    const observer = new IntersectionObserver((entries) => {
        if (reducedMotion.matches) return;

        for (const { target, isIntersecting } of entries) {
            if (!isIntersecting) continue;
            seen.add(target);
            target.classList.add('is-in-view');
            observer.unobserve(target);
        }
    }, { rootMargin: '0px 0px -8% 0px' });

    const updateMotion = () => {
        observer.disconnect();
        for (const item of motionItems) {
            if (reducedMotion.matches) {
                item.classList.remove('is-in-view');
            } else if (!seen.has(item)) {
                observer.observe(item);
            }
        }
    };

    reducedMotion.addEventListener('change', updateMotion);
    updateMotion();
}

const faq = document.querySelector('[data-faq]');

if (faq) {
    const search = faq.querySelector('[data-faq-search]');
    const input = search.querySelector('input');
    const status = search.querySelector('[role="status"]');
    const empty = faq.querySelector('.faq-empty');
    const topics = faq.querySelectorAll('[data-faq-topic]');
    const questions = [...faq.querySelectorAll('.faq-questions details')].map((element) => ({
        element,
        text: element.textContent.toLocaleLowerCase('id'),
    }));
    const updateQuestions = () => {
        const query = input.value.trim().toLocaleLowerCase('id');
        const terms = query ? query.split(/\s+/) : [];
        let count = 0;

        for (const { element, text } of questions) {
            const visible = terms.every((term) => text.includes(term));
            element.hidden = !visible;
            count += Number(visible);
        }

        for (const topic of topics) {
            topic.hidden = !topic.querySelector('details:not([hidden])');
        }

        empty.hidden = count !== 0;
        status.textContent = query ? `${count} pertanyaan ditemukan.` : '';
    };

    input.addEventListener('input', updateQuestions);
    faq.querySelector('[data-faq-reset]').addEventListener('click', () => {
        input.value = '';
        updateQuestions();
        input.focus();
    });
    search.hidden = false;
    updateQuestions();
}

const informationToc = document.querySelector('[data-information-toc]');

if (informationToc) {
    const wide = window.matchMedia('(min-width: 52rem)');
    const updateToc = () => { informationToc.open = wide.matches; };
    updateToc();
    wide.addEventListener('change', updateToc);
}

if (document.body.classList.contains('demo-view')) {
    const pending = new Map();
    const status = document.querySelector('[data-submit-status]');

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (event.defaultPrevented || form.method !== 'post') return;
        if (pending.has(form)) {
            event.preventDefault();
            return;
        }

        const button = event.submitter;
        if (!button?.matches('button')) return;
        pending.set(form, { button, label: button.textContent, minSize: button.style.minInlineSize });
        button.style.minInlineSize = `min(${button.getBoundingClientRect().width}px, 100%)`;
        form.setAttribute('aria-busy', 'true');
        button.setAttribute('aria-disabled', 'true');
        button.textContent = 'Memproses…';
        status.textContent = 'Permintaan simulasi sedang diproses.';
    });

    window.addEventListener('pageshow', () => {
        for (const [form, { button, label, minSize }] of pending) {
            form.removeAttribute('aria-busy');
            button.removeAttribute('aria-disabled');
            button.style.minInlineSize = minSize;
            button.textContent = label;
        }
        pending.clear();
        status.textContent = '';
    });
}
