import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

window.Alpine = Alpine;
Livewire.start();

const initializeAutoDismiss = () => {
    document.querySelectorAll('[data-auto-dismiss]').forEach((toast) => {
        if (!(toast instanceof HTMLElement) || toast.dataset.autoDismissInitialized === 'true') {
            return;
        }

        toast.dataset.autoDismissInitialized = 'true';

        const delay = Number(toast.dataset.autoDismiss || '5000');

        window.setTimeout(() => {
            toast.classList.add('flash-toast--hiding');

            window.setTimeout(() => {
                toast.remove();
            }, 200);
        }, delay);
    });
};

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

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting || !(entry.target instanceof HTMLVideoElement)) {
                return;
            }

            loadVideo(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '650px 0px',
        threshold: 0.01,
    });

    videos.forEach((video) => observer.observe(video));

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
            if (!(entry.target instanceof HTMLVideoElement)) {
                return;
            }

            visibilityRatios.set(entry.target, entry.isIntersecting ? entry.intersectionRatio : 0);
        });

        syncPlayback();
    }, {
        rootMargin: '0px',
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
        threshold: 0.18,
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

const saveEditorSelection = (root, content) => {
    const selection = window.getSelection();

    if (!selection || selection.rangeCount === 0) {
        return;
    }

    const range = selection.getRangeAt(0);

    if (!content.contains(range.commonAncestorContainer)) {
        return;
    }

    root._savedRange = range.cloneRange();
};

const restoreEditorSelection = (root, content) => {
    const selection = window.getSelection();

    if (!selection || !root._savedRange) {
        return false;
    }

    content.focus();
    selection.removeAllRanges();
    selection.addRange(root._savedRange);

    return true;
};

const wrapEditorSelection = (root, content, styles) => {
    const restored = restoreEditorSelection(root, content);
    const selection = window.getSelection();

    if (!restored || !selection || selection.rangeCount === 0 || selection.isCollapsed) {
        return;
    }

    const range = selection.getRangeAt(0);
    const span = document.createElement('span');

    Object.entries(styles).forEach(([property, value]) => {
        span.style[property] = value;
    });

    try {
        range.surroundContents(span);
    } catch {
        const fragment = range.extractContents();
        span.appendChild(fragment);
        range.insertNode(span);
    }

    const newRange = document.createRange();
    newRange.selectNodeContents(span);
    selection.removeAllRanges();
    selection.addRange(newRange);
    root._savedRange = newRange.cloneRange();
};

const initializeRichEditors = () => {
    document.querySelectorAll('[data-rich-editor]').forEach((root) => {
        if (!(root instanceof HTMLElement) || root.dataset.richEditorInitialized === 'true') {
            return;
        }

        const content = root.querySelector('[data-rich-editor-content]');
        const input = root.querySelector('[data-rich-editor-input]');
        const toolbar = root.querySelector('.editor-toolbar');

        if (!(content instanceof HTMLElement) || !(input instanceof HTMLTextAreaElement) || !(toolbar instanceof HTMLElement)) {
            return;
        }

        root.dataset.richEditorInitialized = 'true';
        content.innerHTML = input.value.trim() !== '' ? input.value : '';

        const syncInput = () => {
            input.value = content.innerHTML;
        };

        const trackSelection = () => saveEditorSelection(root, content);

        ['keyup', 'mouseup', 'focus', 'blur'].forEach((eventName) => {
            content.addEventListener(eventName, trackSelection);
        });

        content.addEventListener('input', syncInput);

        const form = root.closest('form');

        if (form instanceof HTMLFormElement) {
            form.addEventListener('submit', syncInput);
        }

        toolbar.querySelectorAll('button').forEach((button) => {
            button.addEventListener('mousedown', (event) => {
                event.preventDefault();
            });
        });

        toolbar.addEventListener('click', (event) => {
            const target = event.target instanceof HTMLElement ? event.target.closest('button') : null;

            if (!(target instanceof HTMLButtonElement)) {
                return;
            }

            if (target.hasAttribute('data-editor-link')) {
                const url = window.prompt('Enter a link URL');

                if (!url) {
                    return;
                }

                restoreEditorSelection(root, content);
                document.execCommand('createLink', false, url);
                trackSelection();
                syncInput();

                return;
            }

            if (target.hasAttribute('data-editor-clear')) {
                restoreEditorSelection(root, content);
                document.execCommand('removeFormat');
                document.execCommand('unlink');
                trackSelection();
                syncInput();

                return;
            }

            const command = target.dataset.editorCommand;

            if (!command) {
                return;
            }

            restoreEditorSelection(root, content);
            document.execCommand('styleWithCSS', false, true);
            document.execCommand(command, false, target.dataset.editorValue || null);
            trackSelection();
            syncInput();
        });

        const colorInput = toolbar.querySelector('[data-editor-color]');

        if (colorInput instanceof HTMLInputElement) {
            colorInput.addEventListener('input', () => {
                restoreEditorSelection(root, content);
                document.execCommand('styleWithCSS', false, true);
                document.execCommand('foreColor', false, colorInput.value);
                trackSelection();
                syncInput();
            });
        }

        const sizeSelect = toolbar.querySelector('[data-editor-size]');

        if (sizeSelect instanceof HTMLSelectElement) {
            sizeSelect.addEventListener('change', () => {
                if (!sizeSelect.value) {
                    return;
                }

                wrapEditorSelection(root, content, {
                    fontSize: sizeSelect.value,
                });
                syncInput();
                sizeSelect.value = '';
            });
        }
    });
};

const updateReactionGroup = (form, payload) => {
    const group = form.closest('[data-reaction-group]');

    if (!(group instanceof HTMLElement) || !payload || typeof payload !== 'object') {
        return;
    }

    const currentReaction = typeof payload.currentReaction === 'string' ? payload.currentReaction : '';
    group.dataset.currentReaction = currentReaction;

    group.querySelectorAll('[data-reaction-button]').forEach((button) => {
        if (!(button instanceof HTMLElement)) {
            return;
        }

        const isActive = button.dataset.reactionButton === currentReaction;
        if (button.classList.contains('reaction-button')) {
            button.classList.toggle('reaction-button--active', isActive);
        }
        button.classList.toggle('forum-action-button--active', isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');

        const icon = button.querySelector('[data-reaction-icon]');

        if (icon instanceof HTMLImageElement) {
            const activeSrc = icon.dataset.activeSrc || '';
            const inactiveSrc = icon.dataset.inactiveSrc || '';

            if (activeSrc && inactiveSrc) {
                icon.src = isActive ? activeSrc : inactiveSrc;
            }
        }
    });

    group.querySelectorAll('[data-reaction-count]').forEach((countNode) => {
        if (!(countNode instanceof HTMLElement)) {
            return;
        }

        const type = countNode.dataset.reactionCount;

        if (!type || !payload.counts || typeof payload.counts[type] === 'undefined') {
            return;
        }

        countNode.textContent = String(payload.counts[type]);
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeAutoDismiss();
        initializeDemoVideos();
        initializeScrollReveal();
        initializeReadMoreCue();
        initializeTypingTitle();
        initializeRichEditors();
    }, { once: true });
} else {
    initializeAutoDismiss();
    initializeDemoVideos();
    initializeScrollReveal();
    initializeReadMoreCue();
    initializeTypingTitle();
    initializeRichEditors();
}

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.matches('[data-reaction-form]')) {
        return;
    }

    event.preventDefault();

    if (form.dataset.submitting === 'true') {
        return;
    }

    form.dataset.submitting = 'true';

    const submitButton = event.submitter instanceof HTMLButtonElement
        ? event.submitter
        : form.querySelector('button[type="submit"]');

    if (submitButton) {
        submitButton.disabled = true;
    }

    try {
        const response = await window.axios.post(form.action, new FormData(form), {
            headers: {
                'Accept': 'application/json',
            },
        });

        updateReactionGroup(form, response.data);
    } catch (error) {
        console.error('Reaction request failed.', error);
    } finally {
        form.dataset.submitting = 'false';

        if (submitButton) {
            submitButton.disabled = false;
        }
    }
});
