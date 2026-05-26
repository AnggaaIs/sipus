import Alpine from 'alpinejs';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

window.Alpine = Alpine;

NProgress.configure({
    showSpinner: false,
    trickleSpeed: 120,
    minimum: 0.12,
});

const startTopLoader = () => {
    NProgress.start();
};

const stopTopLoader = () => {
    NProgress.done();
};

const isModifiedEvent = (event) => {
    return event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
};

const shouldTrackLink = (link, event) => {
    if (!link || isModifiedEvent(event)) {
        return false;
    }

    if (link.target && link.target !== '_self') {
        return false;
    }

    if (link.hasAttribute('download')) {
        return false;
    }

    const href = link.getAttribute('href');

    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return false;
    }

    const url = new URL(link.href, window.location.origin);

    if (url.origin !== window.location.origin) {
        return false;
    }

    const currentPath = window.location.pathname + window.location.search;
    const nextPath = url.pathname + url.search;

    if (currentPath === nextPath && url.hash !== '') {
        return false;
    }

    return true;
};

document.addEventListener('click', (event) => {
    const link = event.target.closest('a');

    if (shouldTrackLink(link, event)) {
        startTopLoader();
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (form.target && form.target !== '_self') {
        return;
    }

    startTopLoader();
});

window.addEventListener('pageshow', stopTopLoader);
window.addEventListener('load', stopTopLoader);
window.addEventListener('popstate', startTopLoader);

document.addEventListener('alpine:init', () => {
    Alpine.data('publicNavbar', ({ overlay = false } = {}) => ({
        overlay,
        isScrolled: !overlay,

        syncScrollState() {
            if (!this.overlay) {
                this.isScrolled = true;

                return;
            }

            this.isScrolled = window.scrollY > 24;
        },
    }));

    Alpine.data('mobileSheet', () => ({
        isOpen: false,

        init() {
            this.$watch('isOpen', (isOpen) => {
                document.body.classList.toggle('overflow-hidden', isOpen);
            });
        },

        openSheet() {
            this.isOpen = true;
        },

        closeSheet() {
            this.isOpen = false;
        },
    }));
});

Alpine.start();
