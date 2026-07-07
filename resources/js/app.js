import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const initializeHomeTypewriter = () => {
    const typeTarget = document.querySelector('[data-home-typewriter]');

    if (!(typeTarget instanceof HTMLElement) || typeTarget.dataset.typingInitialized === 'true') {
        return;
    }

    const parts = Array.from(typeTarget.querySelectorAll('[data-type-part]'))
        .filter((part) => part instanceof HTMLElement);
    const cursor = typeTarget.querySelector('.home-intro-cursor');
    const title = typeTarget.closest('.home-intro-title');

    if (parts.length === 0 || !(cursor instanceof HTMLElement)) {
        return;
    }

    const fullText = parts.map((part) => part.dataset.typeText || part.textContent || '');

    typeTarget.dataset.typingInitialized = 'true';

    const randomBetween = (minimum, maximum) => minimum + Math.random() * (maximum - minimum);

    const getTypingDelay = (character, nextCharacter) => {
        if (character === '.') {
            return randomBetween(260, 420);
        }

        if (character === ' ') {
            return randomBetween(115, 220);
        }

        if (nextCharacter === ' ') {
            return randomBetween(95, 170);
        }

        const hesitation = Math.random() < 0.16 ? randomBetween(70, 180) : 0;

        return randomBetween(48, 118) + hesitation;
    };

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        parts.forEach((part, index) => {
            part.textContent = fullText[index];
        });
        return;
    }

    const visibleParts = parts.map((part) => {
        const visiblePart = document.createElement('span');

        part.textContent = '';
        visiblePart.className = 'home-typewriter-visible';
        part.append(visiblePart);

        return visiblePart;
    });

    title?.classList.add('is-typing');

    let partIndex = 0;
    let characterIndex = 0;

    const typeNextCharacter = () => {
        if (partIndex >= parts.length) {
            title?.classList.remove('is-typing');
            return;
        }

        const currentPart = visibleParts[partIndex];
        const currentText = fullText[partIndex];
        const currentCharacter = currentText[characterIndex];
        const nextCharacter = currentText[characterIndex + 1];

        currentPart.textContent = currentText.slice(0, characterIndex + 1);
        currentPart.append(cursor);
        characterIndex += 1;

        if (characterIndex >= currentText.length) {
            partIndex += 1;
            characterIndex = 0;
        }

        window.setTimeout(typeNextCharacter, getTypingDelay(currentCharacter, nextCharacter));
    };

    window.setTimeout(typeNextCharacter, randomBetween(220, 380));
};

const initializeUiReveal = () => {
    const revealElements = Array.from(document.querySelectorAll('[data-ui-reveal]'))
        .filter((element) => element instanceof HTMLElement && element.dataset.uiRevealInitialized !== 'true');

    if (revealElements.length === 0) {
        return;
    }

    revealElements.forEach((element) => {
        element.dataset.uiRevealInitialized = 'true';
    });

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        revealElements.forEach((element) => element.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting || !(entry.target instanceof HTMLElement)) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -12% 0px',
        threshold: 0.12,
    });

    revealElements.forEach((element) => observer.observe(element));
};

const initializeSite = () => {
    initializeHomeTypewriter();
    initializeUiReveal();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSite, { once: true });
} else {
    initializeSite();
}
