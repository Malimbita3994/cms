const ICONS = {
    chart: '<path d="M3 3v18h18"/><path d="M7 16l4-8 4 5 5-7"/>',
    home: '<path d="M3 10.5 12 3l9 7.5V21H3z"/>',
    user: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    academic: '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/>',
    folder: '<path d="M3 7.5V6a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1.5"/><path d="M3 7.5h18"/>',
    cube: '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/>',
    document: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
    mail: '<path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/>',
    key: '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    lock: '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    plus: '<path d="M12 5v14M5 12h14"/>',
    briefcase: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>',
    bell: '<path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/><path d="M9.5 17a2.5 2.5 0 0 0 5 0"/>',
};

const escapeHtml = (value) =>
    String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

const getApiConfig = () => {
    const node = document.getElementById('workspace-api-config');

    if (!node?.textContent) {
        return null;
    }

    try {
        return JSON.parse(node.textContent);
    } catch {
        return null;
    }
};

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const apiFetch = async (url, options = {}) => {
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.method && options.method !== 'GET' ? { 'X-CSRF-TOKEN': csrfToken() } : {}),
        ...options.headers,
    };

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers,
    });

    if (!response.ok) {
        throw new Error(`Request failed (${response.status})`);
    }

    return response.json();
};

const iconSvg = (name) => {
    const path = ICONS[name] ?? ICONS.document;

    return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">${path}</svg>`;
};

const debounce = (fn, ms = 200) => {
    let timer;

    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
};

class CommandPalette {
    constructor(api) {
        this.api = api;
        this.abortController = null;
        this.flatItems = [];
        this.activeIndex = -1;
        this.topbarInput = null;

        this.refreshDomRefs();

        if (!this.root || !this.input || !this.results) {
            return;
        }

        this.bindPaletteControls();
    }

    refreshDomRefs() {
        this.root = document.getElementById('saas-command-palette');
        this.input = this.root?.querySelector('.saas-cmd__input') ?? null;
        this.results = this.root?.querySelector('[data-cmd-results]') ?? null;
        this.empty = this.root?.querySelector('[data-cmd-empty]') ?? null;
        this.timing = this.root?.querySelector('[data-cmd-timing]') ?? null;
        this.topbarInput = document.querySelector('.saas-topbar-search__input');
    }

    bindPaletteControls() {
        if (this.root.dataset.bound === 'true') {
            return;
        }

        this.root.dataset.bound = 'true';

        this.root.querySelectorAll('[data-cmd-close]').forEach((el) => {
            el.addEventListener('click', () => this.close());
        });

        this.input.addEventListener('input', debounce(() => {
            if (this.topbarInput) {
                this.topbarInput.value = this.input.value;
            }

            this.runSearch(this.input.value.trim());
        }, 180));

        this.input.addEventListener('keydown', (event) => this.onKeydown(event));
    }

    isOpen() {
        return this.root && !this.root.hidden;
    }

    open() {
        this.refreshDomRefs();

        if (!this.root || !this.input) {
            return;
        }

        this.root.hidden = false;
        this.root.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('saas-cmd-open');
        this.input.value = this.topbarInput?.value ?? '';
        this.input.focus();
        this.input.select?.();
        this.runSearch(this.input.value.trim());
    }

    openFromTopbar(topbarInput) {
        this.topbarInput = topbarInput;
        this.open();

        if (this.input && topbarInput) {
            this.input.value = topbarInput.value;
            this.runSearch(topbarInput.value.trim());
        }
    }

    close() {
        if (!this.root) {
            return;
        }

        this.root.hidden = true;
        this.root.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('saas-cmd-open');
    }

    async runSearch(query) {
        if (!this.results) {
            return;
        }

        if (this.abortController) {
            this.abortController.abort();
        }

        this.abortController = new AbortController();

        try {
            const url = new URL(this.api.search, window.location.origin);
            url.searchParams.set('q', query);

            const data = await apiFetch(url.toString(), { signal: this.abortController.signal });
            this.renderResults(data);
        } catch (error) {
            if (error.name !== 'AbortError') {
                this.results.innerHTML = '<p class="saas-cmd__error">Search unavailable. Try again.</p>';
                if (this.empty) {
                    this.empty.hidden = true;
                }
            }
        }
    }

    renderResults(data) {
        const groups = data.groups ?? [];
        this.flatItems = [];

        let html = '';

        groups.forEach((group) => {
            if (!group.items?.length) {
                return;
            }

            html += `<div class="saas-cmd__group"><p class="saas-cmd__group-label">${escapeHtml(group.label)}</p>`;

            group.items.forEach((item) => {
                const index = this.flatItems.length;
                this.flatItems.push(item);

                html += `
                    <button type="button" class="saas-cmd__item" data-cmd-index="${index}" role="option">
                        <span class="saas-cmd__item-icon">${iconSvg(item.icon)}</span>
                        <span class="saas-cmd__item-body">
                            <span class="saas-cmd__item-title">${escapeHtml(item.title)}</span>
                            <span class="saas-cmd__item-sub">${escapeHtml(item.subtitle ?? '')}</span>
                        </span>
                        ${item.badge ? `<span class="saas-cmd__item-badge">${escapeHtml(item.badge)}</span>` : ''}
                    </button>
                `;
            });

            html += '</div>';
        });

        this.results.innerHTML = html;
        if (this.empty) {
            this.empty.hidden = this.flatItems.length > 0;
        }
        this.results.hidden = this.flatItems.length === 0;

        if (this.timing) {
            if (data.took_ms != null && data.query) {
                this.timing.hidden = false;
                this.timing.textContent = `${this.flatItems.length} results · ${data.took_ms}ms`;
            } else {
                this.timing.hidden = true;
            }
        }

        this.activeIndex = this.flatItems.length ? 0 : -1;
        this.highlightActive();

        this.results.querySelectorAll('[data-cmd-index]').forEach((button) => {
            button.addEventListener('click', () => {
                const item = this.flatItems[Number(button.dataset.cmdIndex)];

                if (item?.url) {
                    this.navigate(item.url);
                }
            });
        });
    }

    highlightActive() {
        this.results?.querySelectorAll('.saas-cmd__item').forEach((el, index) => {
            el.classList.toggle('is-active', index === this.activeIndex);
        });
    }

    onKeydown(event) {
        if (!this.flatItems.length) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.activeIndex = (this.activeIndex + 1) % this.flatItems.length;
            this.highlightActive();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.activeIndex = (this.activeIndex - 1 + this.flatItems.length) % this.flatItems.length;
            this.highlightActive();
        } else if (event.key === 'Enter') {
            event.preventDefault();
            const item = this.flatItems[this.activeIndex];

            if (item?.url) {
                this.navigate(item.url);
            }
        }
    }

    navigate(url) {
        this.close();

        if (window.Livewire?.navigate) {
            window.Livewire.navigate(url);
        } else {
            window.location.href = url;
        }
    }
}

class NotificationsPanel {
    constructor(api) {
        this.api = api;
        this.items = [];
        this.unreadPrefetchScheduled = false;
        this.refreshDomRefs();

        if (!this.panel || !this.bell) {
            return;
        }

        this.bindPanelControls();
        this.scheduleUnreadPrefetch();
    }

    refreshDomRefs() {
        this.panel = document.getElementById('saas-notifications-panel');
        this.bell = document.querySelector('.saas-topbar-bell');
        this.dot = this.bell?.querySelector('.saas-topbar-bell__dot') ?? null;
        this.list = this.panel?.querySelector('[data-notify-list]') ?? null;
        this.empty = this.panel?.querySelector('[data-notify-empty]') ?? null;
        this.sub = this.panel?.querySelector('[data-notify-sub]') ?? null;
        this.markAllBtn = this.panel?.querySelector('[data-notify-mark-all]') ?? null;
    }

    bindPanelControls() {
        if (this.panel?.dataset.bound === 'true') {
            return;
        }

        if (this.panel) {
            this.panel.dataset.bound = 'true';
        }

        this.panel?.querySelectorAll('[data-notify-close]').forEach((el) => {
            el.addEventListener('click', () => this.close());
        });

        this.markAllBtn?.addEventListener('click', () => this.markAllRead());
    }

    isOpen() {
        return this.panel && !this.panel.hidden;
    }

    toggle() {
        this.refreshDomRefs();

        if (!this.panel || !this.bell) {
            return;
        }

        if (this.isOpen()) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        if (!this.panel || !this.bell) {
            return;
        }

        this.panel.hidden = false;
        this.panel.setAttribute('aria-hidden', 'false');
        this.bell.setAttribute('aria-expanded', 'true');
        this.refresh();
    }

    close() {
        if (!this.panel || !this.bell) {
            return;
        }

        this.panel.hidden = true;
        this.panel.setAttribute('aria-hidden', 'true');
        this.bell.setAttribute('aria-expanded', 'false');
    }

    setUnreadCount(count) {
        if (!this.dot || !this.bell) {
            return;
        }

        this.dot.hidden = count <= 0;
        this.bell.classList.toggle('has-unread', count > 0);

        const label = count > 0 ? `Notifications (${count} unread)` : 'Notifications';
        this.bell.setAttribute('aria-label', label);
    }

    scheduleUnreadPrefetch() {
        // Intentionally no-op: unread count loads on bell hover/focus only.
    }

    bindUnreadPrefetchOnBell() {
        if (!this.bell || this.bell.dataset.notifyPrefetchBound === 'true' || !this.api?.notificationsSummary) {
            return;
        }

        this.bell.dataset.notifyPrefetchBound = 'true';

        const run = () => {
            if (!this.unreadPrefetchScheduled) {
                this.unreadPrefetchScheduled = true;
                this.prefetchUnreadCount().finally(() => {
                    this.unreadPrefetchScheduled = false;
                });
            }
        };

        this.bell.addEventListener('mouseenter', run, { once: true, passive: true });
        this.bell.addEventListener('focus', run, { once: true });
    }

    async prefetchUnreadCount() {
        if (!this.bell || !this.api?.notificationsSummary) {
            return;
        }

        try {
            const data = await apiFetch(this.api.notificationsSummary);
            this.setUnreadCount(data.unread_count ?? 0);
        } catch {
            // Badge is optional; full list loads when the panel opens.
        }
    }

    async refresh() {
        if (!this.list) {
            return;
        }

        try {
            const data = await apiFetch(this.api.notifications);
            this.items = data.items ?? [];
            this.render();
            this.setUnreadCount(data.unread_count ?? 0);

            if (this.sub) {
                this.sub.textContent = data.unread_count
                    ? `${data.unread_count} unread · workspace activity`
                    : 'All caught up · workspace activity';
            }
        } catch {
            this.list.innerHTML = '<p class="saas-notify-panel__error">Could not load notifications.</p>';
        }
    }

    render() {
        if (!this.list) {
            return;
        }

        if (!this.items.length) {
            this.list.innerHTML = '';
            if (this.empty) {
                this.empty.hidden = false;
            }

            return;
        }

        if (this.empty) {
            this.empty.hidden = true;
        }

        this.list.innerHTML = this.items
            .map(
                (item) => `
            <article class="saas-notify-item ${item.read ? 'is-read' : ''}" data-notify-id="${item.id}" role="listitem">
                <span class="saas-notify-item__icon">${iconSvg(item.icon ?? 'bell')}</span>
                <div class="saas-notify-item__body">
                    <p class="saas-notify-item__title">${escapeHtml(item.title)}</p>
                    <p class="saas-notify-item__text">${escapeHtml(item.body ?? '')}</p>
                    <span class="saas-notify-item__when">${escapeHtml(item.when ?? '')}</span>
                </div>
                ${item.url ? `<a href="${escapeHtml(item.url)}" class="saas-notify-item__link" data-notify-open>Open</a>` : ''}
            </article>
        `,
            )
            .join('');

        this.list.querySelectorAll('[data-notify-id]').forEach((row) => {
            row.addEventListener('click', (event) => {
                if (event.target.closest('[data-notify-open]')) {
                    return;
                }

                this.markRead(Number(row.dataset.notifyId));
            });
        });

        this.list.querySelectorAll('[data-notify-open]').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const row = link.closest('[data-notify-id]');
                const id = Number(row?.dataset.notifyId);
                const item = this.items.find((entry) => entry.id === id);

                this.markRead(id).finally(() => {
                    this.close();

                    if (item?.url) {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(item.url);
                        } else {
                            window.location.href = item.url;
                        }
                    }
                });
            });
        });
    }

    async markRead(id) {
        try {
            const data = await apiFetch(this.api.notificationsRead, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }),
            });

            this.items = this.items.map((item) => (item.id === id ? { ...item, read: true } : item));
            this.render();
            this.setUnreadCount(data.unread_count ?? 0);
        } catch {
            // ignore
        }
    }

    async markAllRead() {
        try {
            await apiFetch(this.api.notificationsReadAll, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
            });

            this.items = this.items.map((item) => ({ ...item, read: true }));
            this.render();
            this.setUnreadCount(0);

            if (this.sub) {
                this.sub.textContent = 'All caught up · workspace activity';
            }
        } catch {
            // ignore
        }
    }
}

let commandPalette = null;
let notificationsPanel = null;
let workspaceDelegationBound = false;

const bindWorkspaceDelegation = () => {
    if (workspaceDelegationBound) {
        return;
    }

    workspaceDelegationBound = true;

    document.addEventListener(
        'focusin',
        (event) => {
            const input = event.target.closest?.('.saas-topbar-search__input');

            if (input && commandPalette) {
                commandPalette.openFromTopbar(input);
            }
        },
        true,
    );

    document.addEventListener(
        'click',
        (event) => {
            const input = event.target.closest?.('.saas-topbar-search__input');

            if (input && commandPalette) {
                commandPalette.openFromTopbar(input);
            }

            const bell = event.target.closest?.('.saas-topbar-bell');

            if (bell && notificationsPanel) {
                event.preventDefault();
                event.stopPropagation();
                notificationsPanel.toggle();
            }
        },
        true,
    );

    document.addEventListener(
        'input',
        debounce((event) => {
            const input = event.target.closest?.('.saas-topbar-search__input');

            if (input && commandPalette) {
                commandPalette.openFromTopbar(input);
            }
        }, 180),
    );

    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();

            if (commandPalette) {
                commandPalette.open();
            }

            return;
        }

        if (event.key === 'Escape' && commandPalette?.isOpen()) {
            commandPalette.close();
        }
    });

    document.addEventListener('click', (event) => {
        if (!notificationsPanel?.isOpen()) {
            return;
        }

        const panel = document.getElementById('saas-notifications-panel');
        const bell = document.querySelector('.saas-topbar-bell');

        if (panel?.contains(event.target) || bell?.contains(event.target)) {
            return;
        }

        notificationsPanel.close();
    });
};

export const initWorkspaceShell = () => {
    const api = getApiConfig();

    if (!api) {
        return;
    }

    bindWorkspaceDelegation();

    if (!commandPalette) {
        commandPalette = new CommandPalette(api);
        window.__saasCommandPalette = commandPalette;
    } else {
        commandPalette.refreshDomRefs();
    }

    if (!notificationsPanel) {
        notificationsPanel = new NotificationsPanel(api);
        window.__saasNotifications = notificationsPanel;
    } else {
        notificationsPanel.refreshDomRefs();
        notificationsPanel.bindUnreadPrefetchOnBell();
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWorkspaceShell, { once: true });
} else {
    initWorkspaceShell();
}

document.addEventListener('livewire:navigated', initWorkspaceShell);
