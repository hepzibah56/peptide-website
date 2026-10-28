import './app.css';

document.addEventListener('DOMContentLoaded', () => {
    const drawer = document.getElementById('cart-drawer');
    const openBtn = document.getElementById('cart-open-btn');
    const closeBtn = document.getElementById('cart-close-btn');

    if (!drawer || !openBtn || !closeBtn) return;

    const focusables = () => drawer.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');

    function open() {
        drawer.classList.remove('translate-x-full');
        drawer.setAttribute('aria-hidden', 'false');
        openBtn.setAttribute('aria-expanded', 'true');
        const f = focusables()[0];
        if (f) f.focus();
        document.body.style.overflow = 'hidden';
    }

    function close() {
        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        openBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        openBtn.focus();
    }

    openBtn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    drawer.addEventListener('click', (e) => { if (e.target === drawer) close(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') close();
    });

    // Quantity stepper
    document.querySelectorAll('.qty-stepper').forEach((stepper) => {
        const dec = stepper.querySelector('.qty-dec');
        const inc = stepper.querySelector('.qty-inc');
        const input = stepper.querySelector('.qty-input');
        if (!dec || !inc || !input) return;
        dec.addEventListener('click', () => { if (input.value > 1) input.value--; input.dispatchEvent(new Event('change')); });
        inc.addEventListener('click', () => { input.value++; input.dispatchEvent(new Event('change')); });
    });

    // Cookie consent
    const cookiePanel = document.getElementById('cookie-consent');
    const acceptBtn = document.getElementById('cookie-accept');
    if (cookiePanel && acceptBtn) {
        if (localStorage.getItem('cookie-consent') === '1') {
            cookiePanel.style.display = 'none';
        }
        acceptBtn.addEventListener('click', () => {
            localStorage.setItem('cookie-consent', '1');
            cookiePanel.style.display = 'none';
        });
    }
});