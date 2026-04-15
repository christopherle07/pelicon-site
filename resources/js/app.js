import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const initializeDownloadPages = () => {
    document.querySelectorAll('[data-download-page]').forEach((root) => {
        if (!(root instanceof HTMLElement) || root.dataset.downloadPageInitialized === 'true') {
            return;
        }

        const currencies = JSON.parse(root.dataset.currencies || '{}');
        const currencySelect = root.querySelector('[data-currency-select]');
        const businessPrice = root.querySelector('[data-business-price]');
        const currencyLabel = root.querySelector('[data-currency-label]');
        const billingLabel = root.querySelector('[data-billing-label]');
        const billingButtons = root.querySelectorAll('[data-billing-button]');
        const businessCheckout = root.querySelector('[data-business-checkout]');
        const checkoutTitle = root.querySelector('[data-checkout-title]');
        const checkoutPlan = root.querySelector('[data-checkout-plan]');
        const checkoutCurrency = root.querySelector('[data-checkout-currency]');
        const checkoutPrice = root.querySelector('[data-checkout-price]');
        const openPaymentModalButton = root.querySelector('[data-open-payment-modal]');
        const paymentModal = root.querySelector('[data-payment-modal]');
        const modalPlan = root.querySelector('[data-modal-plan]');
        const modalPrice = root.querySelector('[data-modal-price]');
        const tipOptions = root.querySelector('[data-tip-options]');
        const customTipWrap = root.querySelector('[data-custom-tip-wrap]');
        const customTipSymbol = root.querySelector('[data-custom-tip-symbol]');
        const customTipInput = root.querySelector('[data-custom-tip-input]');
        const tipSummary = root.querySelector('[data-tip-summary]');

        if (
            !(currencySelect instanceof HTMLSelectElement) ||
            !(businessPrice instanceof HTMLElement) ||
            !(currencyLabel instanceof HTMLElement) ||
            !(billingLabel instanceof HTMLElement) ||
            !(businessCheckout instanceof HTMLElement) ||
            !(checkoutTitle instanceof HTMLElement) ||
            !(checkoutPlan instanceof HTMLElement) ||
            !(checkoutCurrency instanceof HTMLElement) ||
            !(checkoutPrice instanceof HTMLElement) ||
            !(openPaymentModalButton instanceof HTMLButtonElement) ||
            !(paymentModal instanceof HTMLElement) ||
            !(modalPlan instanceof HTMLElement) ||
            !(modalPrice instanceof HTMLElement) ||
            !(tipOptions instanceof HTMLElement) ||
            !(customTipWrap instanceof HTMLElement) ||
            !(customTipSymbol instanceof HTMLElement) ||
            !(customTipInput instanceof HTMLInputElement) ||
            !(tipSummary instanceof HTMLElement)
        ) {
            return;
        }

        root.dataset.downloadPageInitialized = 'true';

        const state = {
            billing: root.dataset.defaultBilling || 'yearly',
            currency: root.dataset.defaultCurrency || currencySelect.value,
            tip: root.dataset.defaultTip || '',
            checkoutOpen: false,
        };

        const getCurrency = () => currencies[state.currency] || null;

        const formatTip = () => {
            const currency = getCurrency();

            if (!currency) {
                return 'Choose an amount';
            }

            if (state.tip === 'custom') {
                return customTipInput.value.trim() !== ''
                    ? `${currency.symbol}${customTipInput.value.trim()}`
                    : 'Choose an amount';
            }

            if (state.tip !== '') {
                return `${currency.symbol}${state.tip}`;
            }

            return 'Choose an amount';
        };

        const renderTipButtons = () => {
            const currency = getCurrency();

            if (!currency) {
                return;
            }

            const presetMarkup = currency.tips.map((amount) => `
                <button type="button" class="tip-chip ${String(state.tip) === String(amount) ? 'tip-chip--active' : ''}" data-tip-button="${amount}">
                    <span class="tip-chip__control" aria-hidden="true"></span>
                    <span>${currency.symbol}${amount}</span>
                </button>
            `).join('');

            const customActive = state.tip === 'custom' ? 'tip-chip--active' : '';

            tipOptions.innerHTML = `
                ${presetMarkup}
                <button type="button" class="tip-chip sm:col-span-2 ${customActive}" data-tip-button="custom">
                    <span class="tip-chip__control" aria-hidden="true"></span>
                    <span>Custom</span>
                </button>
            `;
        };

        const render = () => {
            const currency = getCurrency();

            if (!currency) {
                return;
            }

            const amount = state.billing === 'yearly' ? currency.business_yearly : currency.business_onetime;
            const suffix = state.billing === 'yearly' ? '/year per member' : ' one-time per member';
            const planLabel = state.billing === 'yearly' ? 'Yearly' : 'One-time';
            const formattedPrice = `${currency.symbol}${amount}${suffix}`;

            businessPrice.textContent = formattedPrice;
            currencyLabel.textContent = currency.label;
            billingLabel.textContent = state.billing === 'yearly' ? 'Recurring license' : 'One-time license';
            checkoutTitle.textContent = `Business ${state.billing === 'yearly' ? 'yearly' : 'one-time'} license`;
            checkoutPlan.textContent = planLabel;
            checkoutCurrency.textContent = currency.code;
            checkoutPrice.textContent = formattedPrice;
            modalPlan.textContent = `${planLabel} - ${currency.code}`;
            modalPrice.textContent = formattedPrice;
            customTipSymbol.textContent = currency.symbol;
            customTipInput.step = String(currency.custom_step ?? 1);
            businessCheckout.classList.toggle('hidden', !state.checkoutOpen);

            billingButtons.forEach((button) => {
                if (!(button instanceof HTMLButtonElement)) {
                    return;
                }

                const isActive = button.dataset.billingButton === state.billing;
                button.classList.toggle('button-primary', isActive);
                button.classList.toggle('button-secondary', !isActive);
            });

            renderTipButtons();
            customTipWrap.classList.toggle('hidden', state.tip !== 'custom');
            tipSummary.textContent = formatTip();
        };

        currencySelect.addEventListener('change', () => {
            state.currency = currencySelect.value;

            const currency = getCurrency();
            state.tip = currency?.tips?.[1] ?? currency?.tips?.[0] ?? '';
            customTipInput.value = '';

            render();
        });

        billingButtons.forEach((button) => {
            if (!(button instanceof HTMLButtonElement)) {
                return;
            }

            button.addEventListener('click', () => {
                state.billing = button.dataset.billingButton || 'yearly';
                state.checkoutOpen = true;
                render();
            });
        });

        openPaymentModalButton.addEventListener('click', () => {
            paymentModal.classList.remove('hidden');
            document.body.classList.add('modal-open');
        });

        paymentModal.querySelectorAll('[data-close-payment-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                paymentModal.classList.add('hidden');
                document.body.classList.remove('modal-open');
            });
        });

        tipOptions.addEventListener('click', (event) => {
            const button = event.target instanceof HTMLElement ? event.target.closest('[data-tip-button]') : null;

            if (!(button instanceof HTMLButtonElement)) {
                return;
            }

            state.tip = button.dataset.tipButton || '';

            if (state.tip !== 'custom') {
                customTipInput.value = '';
            }

            render();
        });

        customTipInput.addEventListener('input', () => {
            if (state.tip === 'custom') {
                tipSummary.textContent = formatTip();
            }
        });

        render();
    });
};

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
        button.classList.toggle('reaction-button--active', isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
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
        initializeDownloadPages();
        initializeRichEditors();
    }, { once: true });
} else {
    initializeAutoDismiss();
    initializeDownloadPages();
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
