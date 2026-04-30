import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { Editor, Extension, InputRule } from '@tiptap/core';
import { StarterKit } from '@tiptap/starter-kit';
import { TextAlign } from '@tiptap/extension-text-align';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';

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
            entry.target.offsetHeight; // force reflow so the starting transform is applied before transition
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px 0px 0px',
        threshold: 0,
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

// ─── Rich editor (Tiptap) ─────────────────────────────────────────────────

const ALIGN_CYCLE = ['left', 'center', 'right'];

const EDITOR_ICONS = {
    link: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>`,
    unlink: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/><line x1="4.93" y1="19.07" x2="19.07" y2="4.93"/></svg>`,
    alignLeft: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="14" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>`,
    alignCenter: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>`,
    alignRight: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>`,
};

const syncEditorToolbar = (editor, root) => {
    const toolbar = root.querySelector('[data-tiptap-toolbar]');
    if (!toolbar) {
        return;
    }

    [
        ['bold', () => editor.isActive('bold')],
        ['italic', () => editor.isActive('italic')],
        ['underline', () => editor.isActive('underline')],
        ['strike', () => editor.isActive('strike')],
        ['h2', () => editor.isActive('heading', { level: 2 })],
        ['h3', () => editor.isActive('heading', { level: 3 })],
        ['bulletList', () => editor.isActive('bulletList')],
        ['orderedList', () => editor.isActive('orderedList')],
        ['blockquote', () => editor.isActive('blockquote')],
    ].forEach(([key, check]) => {
        const btn = toolbar.querySelector(`[data-tt="${key}"]`);
        if (btn) {
            btn.classList.toggle('is-active', check());
        }
    });

    const linkBtn = toolbar.querySelector('[data-tt="link"]');
    if (linkBtn instanceof HTMLButtonElement) {
        const inLink = editor.isActive('link');
        linkBtn.innerHTML = inLink ? EDITOR_ICONS.unlink : EDITOR_ICONS.link;
        linkBtn.title = inLink ? 'Remove link' : 'Insert link (⌘K)';
        linkBtn.dataset.ttLinkState = inLink ? 'unlink' : 'link';
    }

    const alignBtn = toolbar.querySelector('[data-tt="align"]');
    if (alignBtn instanceof HTMLButtonElement) {
        const current = ALIGN_CYCLE.find((a) => editor.isActive({ textAlign: a })) || 'left';
        const iconKey = `align${current.charAt(0).toUpperCase()}${current.slice(1)}`;
        alignBtn.innerHTML = EDITOR_ICONS[iconKey];
        alignBtn.dataset.ttAlign = current;
        alignBtn.title = `Align: ${current} — click to cycle`;
    }
};

const MarkdownLinkRule = Extension.create({
    name: 'markdownLinkRule',
    addInputRules() {
        return [
            new InputRule({
                find: /\[(.+?)\]\((.+?)\)$/,
                handler({ state, range, match }) {
                    const [, text, href] = match;
                    const linkMark = state.schema.marks.link;
                    if (!linkMark) return null;
                    state.tr.replaceWith(
                        range.from,
                        range.to,
                        state.schema.text(text, [linkMark.create({ href, target: '_blank', rel: 'noopener noreferrer' })]),
                    );
                },
            }),
        ];
    },
});

const initTiptapEditors = () => {
    document.querySelectorAll('[data-rich-editor]').forEach((root) => {
        if (!(root instanceof HTMLElement) || root.dataset.richEditorInitialized === 'true') {
            return;
        }

        root.dataset.richEditorInitialized = 'true';

        const contentEl = root.querySelector('[data-rich-editor-content]');
        const input = root.querySelector('[data-rich-editor-input]');
        const linkBar = root.querySelector('[data-tt-link-bar]');
        const linkInput = root.querySelector('[data-tt-link-input]');

        if (!(contentEl instanceof HTMLElement) || !(input instanceof HTMLTextAreaElement)) {
            return;
        }

        const editor = new Editor({
            element: contentEl,
            extensions: [
                StarterKit.configure({
                    link: {
                        openOnClick: false,
                        HTMLAttributes: { target: '_blank', rel: 'noopener noreferrer' },
                    },
                }),
                MarkdownLinkRule,
                TextAlign.configure({ types: ['heading', 'paragraph'] }),
                TextStyle,
                Color,
            ],
            content: input.value || '',
            editorProps: {
                attributes: { class: 'rich-copy', spellcheck: 'true' },
            },
            onUpdate: ({ editor }) => {
                input.value = editor.getHTML();
                syncEditorToolbar(editor, root);
            },
            onSelectionUpdate: ({ editor }) => {
                syncEditorToolbar(editor, root);
            },
        });

        syncEditorToolbar(editor, root);

        root.querySelector('[data-tiptap-toolbar]')?.addEventListener('mousedown', (e) => {
            if (e.target instanceof Element && e.target.closest('button, label')) {
                e.preventDefault();
            }
        });

        root.closest('form')?.addEventListener('submit', () => {
            input.value = editor.getHTML();
        });

        root.querySelector('[data-tiptap-toolbar]')?.addEventListener('click', (e) => {
            const btn = e.target instanceof Element ? e.target.closest('[data-tt]') : null;

            if (!(btn instanceof HTMLElement)) {
                return;
            }

            const tt = btn.dataset.tt;

            const actions = {
                bold: () => editor.chain().focus().toggleBold().run(),
                italic: () => editor.chain().focus().toggleItalic().run(),
                underline: () => editor.chain().focus().toggleUnderline().run(),
                strike: () => editor.chain().focus().toggleStrike().run(),
                h2: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
                h3: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
                bulletList: () => editor.chain().focus().toggleBulletList().run(),
                orderedList: () => editor.chain().focus().toggleOrderedList().run(),
                blockquote: () => editor.chain().focus().toggleBlockquote().run(),
                clear: () => editor.chain().focus().clearNodes().unsetAllMarks().run(),
            };

            if (tt in actions) {
                actions[tt]();
                syncEditorToolbar(editor, root);
                return;
            }

            if (tt === 'link') {
                if (btn.dataset.ttLinkState === 'unlink') {
                    editor.chain().focus().unsetLink().run();
                    syncEditorToolbar(editor, root);
                } else if (linkBar instanceof HTMLElement) {
                    linkBar.hidden = false;
                    if (linkInput instanceof HTMLInputElement) {
                        linkInput.value = editor.getAttributes('link').href || '';
                        linkInput.focus();
                    }
                }
                return;
            }

            if (tt === 'align') {
                const current = btn.dataset.ttAlign || 'left';
                const next = ALIGN_CYCLE[(ALIGN_CYCLE.indexOf(current) + 1) % ALIGN_CYCLE.length];
                editor.chain().focus().setTextAlign(next).run();
                syncEditorToolbar(editor, root);
                return;
            }
        });

        root.querySelector('[data-tt="color"]')?.addEventListener('input', (e) => {
            if (!(e.target instanceof HTMLInputElement)) {
                return;
            }
            editor.chain().focus().setColor(e.target.value).run();
            const dot = root.querySelector('[data-tt-color-dot]');
            if (dot instanceof HTMLElement) {
                dot.style.background = e.target.value;
            }
        });

        if (linkBar instanceof HTMLElement && linkInput instanceof HTMLInputElement) {
            const applyLink = () => {
                const url = linkInput.value.trim();
                if (url) {
                    editor.chain().focus().setLink({ href: url }).run();
                } else {
                    editor.chain().focus().unsetLink().run();
                }
                linkBar.hidden = true;
                syncEditorToolbar(editor, root);
            };

            root.querySelector('[data-tt-link-apply]')?.addEventListener('click', applyLink);
            root.querySelector('[data-tt-link-cancel]')?.addEventListener('click', () => {
                linkBar.hidden = true;
                editor.commands.focus();
            });

            linkInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyLink();
                }
                if (e.key === 'Escape') {
                    linkBar.hidden = true;
                    editor.commands.focus();
                }
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
        initTiptapEditors();
    }, { once: true });
} else {
    initializeAutoDismiss();
    initializeDemoVideos();
    initializeScrollReveal();
    initializeReadMoreCue();
    initializeTypingTitle();
    initTiptapEditors();
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
