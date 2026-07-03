const SIDEBAR_NAV_VERSION_KEY = 'cmsSidebarNavVersion';

const readSidebarNavConfig = () => {
    const element = document.getElementById('cms-sidebar-nav-config');

    if (!element?.textContent) {
        return null;
    }

    try {
        return JSON.parse(element.textContent);
    } catch {
        return null;
    }
};

const ensureCollapsedGroupsArray = (store) => {
    if (!Array.isArray(store.collapsedGroups)) {
        store.collapsedGroups = [];
    }
};

const expandActiveSidebarGroup = (store = window.Alpine?.store?.('sidebar')) => {
    if (!store) {
        return;
    }

    ensureCollapsedGroupsArray(store);

    const activeGroup =
        document.querySelector('.fi-sidebar-group.fi-active') ||
        document.querySelector('.fi-sidebar-group .fi-sidebar-item.fi-active')?.closest('.fi-sidebar-group');

    if (!activeGroup) {
        return;
    }

    const label = activeGroup.getAttribute('data-group-label');

    if (!label || !store.collapsedGroups.includes(label)) {
        return;
    }

    store.collapsedGroups = store.collapsedGroups.filter((group) => group !== label);

    activeGroup.classList.remove('fi-collapsed');

    const items = activeGroup.querySelector('.fi-sidebar-group-items');

    if (items) {
        items.style.display = '';
    }
};

const initSidebarNavigationState = () => {
    const store = window.Alpine?.store?.('sidebar');

    if (!store) {
        return;
    }

    const config = readSidebarNavConfig();

    if (!config) {
        expandActiveSidebarGroup(store);

        return;
    }

    const savedVersion = localStorage.getItem(SIDEBAR_NAV_VERSION_KEY);
    const version = String(config.version ?? '');

    if (savedVersion !== version) {
        store.collapsedGroups = [...(config.defaultCollapsed ?? [])];
        localStorage.setItem('collapsedGroups', JSON.stringify(store.collapsedGroups));
        localStorage.setItem(SIDEBAR_NAV_VERSION_KEY, version);
    }

    ensureCollapsedGroupsArray(store);
    expandActiveSidebarGroup(store);
};

const assignSidebarTitles = () => {
    document
        .querySelectorAll('.fi-sidebar-item-btn, .fi-sidebar-database-notifications-btn')
        .forEach((itemButton) => {
            const label =
                itemButton
                    .querySelector('.fi-sidebar-item-label, .fi-sidebar-database-notifications-btn-label')
                    ?.textContent?.trim() || itemButton.getAttribute('aria-label');

            if (label) {
                itemButton.setAttribute('title', label);
            }
        });
};

const focusGlobalSearch = () => {
    const input = document.querySelector('.saas-topbar-search__input, .sap-topbar-search-input');

    if (input) {
        input.focus();
        input.select?.();
    }
};

const bindSearchShortcut = () => {
    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();

            if (window.__saasCommandPalette) {
                window.__saasCommandPalette.open();
            } else {
                focusGlobalSearch();
            }
        }
    });
};

const openSidebarOnDesktop = () => {
    if (!window.matchMedia('(min-width: 1024px)').matches) {
        return;
    }

    if (window.Alpine?.store?.('sidebar') && !window.Alpine.store('sidebar').isOpen) {
        window.Alpine.store('sidebar').open();
    }
};

const updateUserAvatars = (avatarUrl) => {
    if (!avatarUrl) {
        return;
    }

    document
        .querySelectorAll('.fi-topbar .fi-user-menu-trigger .fi-avatar, .saas-sidebar-user-avatar--image')
        .forEach((image) => {
            if (image.getAttribute('src') !== avatarUrl) {
                image.src = avatarUrl;
            }
        });
};

const syncTopbarAvatarFromDocument = () => {
    const element = document.getElementById('auth-user-avatar-url');

    if (!element?.textContent) {
        return;
    }

    try {
        const avatarUrl = JSON.parse(element.textContent);

        if (typeof avatarUrl === 'string' && avatarUrl !== '') {
            updateUserAvatars(avatarUrl);
        }
    } catch {
        // Ignore invalid JSON.
    }
};

const bindUserAvatarUpdates = () => {
    if (!window.Livewire) {
        return;
    }

    Livewire.on('user-avatar-updated', (payload) => {
        const data = Array.isArray(payload) ? (payload[0] ?? {}) : (payload ?? {});
        const avatarUrl = data.avatarUrl ?? data.avatar_url;

        if (avatarUrl) {
            updateUserAvatars(avatarUrl);

            const element = document.getElementById('auth-user-avatar-url');

            if (element) {
                element.textContent = JSON.stringify(avatarUrl);
            }
        }
    });
};

const initSaasShell = () => {
    assignSidebarTitles();
    bindSearchShortcut();
    openSidebarOnDesktop();
    syncTopbarAvatarFromDocument();
    bindUserAvatarUpdates();
    document.documentElement.classList.add('saas-app-shell');
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSaasShell, { once: true });
} else {
    initSaasShell();
}

document.addEventListener('alpine:init', () => {
    openSidebarOnDesktop();
    initSidebarNavigationState();
});

document.addEventListener('livewire:navigated', () => {
    assignSidebarTitles();
    syncTopbarAvatarFromDocument();
    initSidebarNavigationState();
});

document.addEventListener('livewire:init', bindUserAvatarUpdates);
