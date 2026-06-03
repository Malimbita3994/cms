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

document.addEventListener('alpine:init', openSidebarOnDesktop);
document.addEventListener('livewire:navigated', () => {
    assignSidebarTitles();
    syncTopbarAvatarFromDocument();
});

document.addEventListener('livewire:init', bindUserAvatarUpdates);
