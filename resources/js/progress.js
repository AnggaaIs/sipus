import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

NProgress.configure({
    showSpinner: false,
    minimum: 0.12,
    trickleSpeed: 120,
});

const isInternalNavigation = (link, event) => {
    if (
        !link ||
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey ||
        link.target === '_blank' ||
        link.hasAttribute('download')
    ) {
        return false;
    }

    const url = new URL(link.href, window.location.href);

    return url.origin === window.location.origin && url.href !== window.location.href;
};

document.addEventListener('click', (event) => {
    const link = event.target instanceof Element ? event.target.closest('a') : null;

    if (isInternalNavigation(link, event)) {
        NProgress.start();
    }
});

document.addEventListener('submit', (event) => {
    if (event.target instanceof HTMLFormElement && (!event.target.target || event.target.target === '_self')) {
        NProgress.start();
    }
});
window.addEventListener('pageshow', () => NProgress.done());
