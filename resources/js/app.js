import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const initializeDemoVideos = () => {
    const videos = Array.from(document.querySelectorAll('[data-demo-video]'))
        .filter((video) => video instanceof HTMLVideoElement && video.dataset.demoVideoInitialized !== 'true');

    if (videos.length === 0) {
        return;
    }

    videos.forEach((video) => {
        video.dataset.demoVideoInitialized = 'true';
    });

    const loadVideo = (video) => {
        if (video.dataset.loaded === 'true') {
            return;
        }

        const source = video.querySelector('source[data-src]');

        if (!(source instanceof HTMLSourceElement) || !source.dataset.src) {
            return;
        }

        source.src = source.dataset.src;
        video.dataset.loaded = 'true';
        video.preload = 'auto';
        video.load();

        const playVideo = () => {
            if (video.dataset.inPlaybackRange !== 'true') {
                video.pause();
                return;
            }

            video.play().catch(() => {});
        };

        if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA) {
            playVideo();
        } else {
            video.addEventListener('canplay', playVideo, { once: true });
        }
    };

    if (!('IntersectionObserver' in window)) {
        videos.forEach((video) => {
            video.dataset.inPlaybackRange = 'true';
            loadVideo(video);
        });
        return;
    }

    const loadObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting || !(entry.target instanceof HTMLVideoElement)) {
                return;
            }

            loadVideo(entry.target);
            loadObserver.unobserve(entry.target);
        });
    }, {
        rootMargin: '650px 0px',
        threshold: 0.01,
    });

    videos.forEach((video) => loadObserver.observe(video));

    const visibilityRatios = new Map(videos.map((video) => [video, 0]));

    const syncPlayback = () => {
        let activeVideo = null;
        let activeRatio = 0;

        visibilityRatios.forEach((ratio, video) => {
            if (video.dataset.loaded !== 'true' || ratio <= activeRatio) {
                return;
            }

            activeVideo = video;
            activeRatio = ratio;
        });

        videos.forEach((video) => {
            const isActive = video === activeVideo && activeRatio >= 0.2;

            video.dataset.inPlaybackRange = isActive ? 'true' : 'false';

            if (video.dataset.loaded !== 'true') {
                return;
            }

            if (isActive) {
                video.play().catch(() => {});
            } else {
                video.pause();
            }
        });
    };

    const playbackObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.target instanceof HTMLVideoElement) {
                visibilityRatios.set(entry.target, entry.isIntersecting ? entry.intersectionRatio : 0);
            }
        });

        syncPlayback();
    }, {
        threshold: [0, 0.2, 0.35, 0.5, 0.65, 0.8, 0.95, 1],
    });

    videos.forEach((video) => playbackObserver.observe(video));
};

const initializeScrollReveal = () => {
    const revealElements = Array.from(document.querySelectorAll('[data-scroll-reveal]'))
        .filter((element) => element instanceof HTMLElement && element.dataset.scrollRevealInitialized !== 'true');

    if (revealElements.length === 0) {
        return;
    }

    revealElements.forEach((element) => {
        element.dataset.scrollRevealInitialized = 'true';
    });

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        revealElements.forEach((element) => element.classList.add('is-visible'));
        return;
    }

    let scrollingDown = true;
    let lastScrollY = window.scrollY;

    window.addEventListener('scroll', () => {
        scrollingDown = window.scrollY >= lastScrollY;
        lastScrollY = window.scrollY;
    }, { passive: true });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting || !(entry.target instanceof HTMLElement)) {
                return;
            }

            entry.target.dataset.scrollRevealFrom = scrollingDown ? 'below' : 'above';
            entry.target.offsetHeight;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    });

    revealElements.forEach((element) => observer.observe(element));
};

const initializeReadMoreCue = () => {
    const cue = document.querySelector('[data-read-more-cue]');

    if (!(cue instanceof HTMLElement) || cue.dataset.readMoreCueInitialized === 'true') {
        return;
    }

    cue.dataset.readMoreCueInitialized = 'true';

    let ticking = false;

    const updateCue = () => {
        cue.classList.toggle('is-hidden', window.scrollY > 80);
        ticking = false;
    };

    const requestUpdate = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        window.requestAnimationFrame(updateCue);
    };

    updateCue();
    window.addEventListener('scroll', requestUpdate, { passive: true });
};

const initializeTypingTitle = () => {
    const title = document.querySelector('[data-typing-title]');

    if (!(title instanceof HTMLElement) || title.dataset.typingInitialized === 'true') {
        return;
    }

    const parts = Array.from(title.querySelectorAll('[data-type-part]'))
        .filter((part) => part instanceof HTMLElement);

    if (parts.length === 0) {
        return;
    }

    title.dataset.typingInitialized = 'true';

    const fullText = parts.map((part) => part.dataset.typeText || part.textContent || '');

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        parts.forEach((part, index) => {
            part.textContent = fullText[index];
        });

        return;
    }

    parts.forEach((part) => {
        part.textContent = '';
    });

    title.classList.add('is-typing');

    let partIndex = 0;
    let characterIndex = 0;

    const randomBetween = (minimum, maximum) => minimum + Math.random() * (maximum - minimum);

    const getTypingDelay = (character, nextCharacter) => {
        if (character === '.') {
            return randomBetween(340, 520);
        }

        if (character === ' ') {
            return randomBetween(130, 250);
        }

        if (nextCharacter === ' ') {
            return randomBetween(95, 175);
        }

        const hesitation = Math.random() < 0.18 ? randomBetween(80, 190) : 0;

        return randomBetween(62, 138) + hesitation;
    };

    const typeNextCharacter = () => {
        if (partIndex >= parts.length) {
            title.classList.remove('is-typing');
            return;
        }

        const currentText = fullText[partIndex];
        const currentCharacter = currentText[characterIndex];
        const nextCharacter = currentText[characterIndex + 1];

        parts[partIndex].textContent = currentText.slice(0, characterIndex + 1);
        characterIndex += 1;

        if (characterIndex >= currentText.length) {
            partIndex += 1;
            characterIndex = 0;
        }

        window.setTimeout(typeNextCharacter, getTypingDelay(currentCharacter, nextCharacter));
    };

    window.setTimeout(typeNextCharacter, randomBetween(320, 540));
};

const initializeSite = () => {
    initializeDemoVideos();
    initializeScrollReveal();
    initializeReadMoreCue();
    initializeTypingTitle();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSite, { once: true });
} else {
    initializeSite();
}
