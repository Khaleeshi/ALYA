// Polices auto-hébergées : déclarées dans le manifeste Vite pour pouvoir être
// préchargées par la mise en page (voir resources/views/components/amani/polices.blade.php).
import.meta.glob(['../fonts/*.woff2']);

// Apparition des sections : fondu et montée de 8 px sur 400 ms, discrète (cahier §2.3).
// Seuls les éléments encore hors de l'écran au chargement sont concernés, pour
// ne jamais retarder ce qui est déjà visible ; « réduire les animations » est respecté.
function preparerApparitions() {
    const elements = document.querySelectorAll('[data-apparition]');

    if (elements.length === 0 || !('IntersectionObserver' in window)) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const observateur = new IntersectionObserver(
        (entrees) => {
            for (const entree of entrees) {
                if (entree.isIntersecting) {
                    entree.target.classList.add('apparition-jouee');
                    entree.target.classList.remove('apparition-en-attente');
                    observateur.unobserve(entree.target);
                }
            }
        },
        { rootMargin: '0px 0px -10% 0px' },
    );

    for (const element of elements) {
        if (element.getBoundingClientRect().top > window.innerHeight) {
            element.classList.add('apparition-en-attente');
            observateur.observe(element);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', preparerApparitions);
} else {
    preparerApparitions();
}
