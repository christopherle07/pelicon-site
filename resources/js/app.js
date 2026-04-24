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
        initializeRichEditors();
    }, { once: true });
} else {
    initializeAutoDismiss();
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
