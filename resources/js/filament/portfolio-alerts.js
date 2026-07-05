const swalDefaults = {
    background: '#12151c',
    color: '#f4f4f5',
    confirmButtonColor: '#f59e0b',
    cancelButtonColor: '#3f3f46',
    customClass: {
        popup: 'portfolio-swal-popup',
        title: 'portfolio-swal-title',
        htmlContainer: 'portfolio-swal-text',
    },
};

let swalModulePromise = null;

const getSwal = async () => {
    if (!swalModulePromise) {
        swalModulePromise = import('sweetalert2').then(({ default: Swal }) => Swal);
    }

    return swalModulePromise;
};

const confirmDialog = async (options) => {
    const Swal = await getSwal();

    return Swal.fire({
        ...swalDefaults,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText ?? options.confirmText ?? 'Yes, continue',
        cancelButtonText: options.cancelButtonText ?? options.cancelText ?? 'Cancel',
        reverseButtons: true,
        focusCancel: true,
        ...options,
    });
};

document.addEventListener(
    'click',
    async (event) => {
        const trigger = event.target.closest('[data-swal-confirm]');

        if (!trigger || trigger.dataset.swalConfirmed === '1') {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        const result = await confirmDialog({
            title: trigger.dataset.swalTitle ?? 'Are you sure?',
            text: trigger.dataset.swalText ?? 'This action cannot be undone.',
            confirmText: trigger.dataset.swalConfirmText,
            cancelText: trigger.dataset.swalCancelText,
        });

        if (!result.isConfirmed) {
            return;
        }

        trigger.dataset.swalConfirmed = '1';
        trigger.click();
        delete trigger.dataset.swalConfirmed;
    },
    true,
);

const normalizePayload = (payload) => {
    if (Array.isArray(payload)) {
        return payload[0] ?? {};
    }

    return payload ?? {};
};

const showSessionFlashAlert = async (elementId) => {
    const element = document.getElementById(elementId);

    if (!element?.textContent) {
        return;
    }

    let data;

    try {
        data = JSON.parse(element.textContent);
    } catch {
        return;
    }

    const Swal = await getSwal();

    Swal.close();

    Swal.fire({
        ...swalDefaults,
        icon: data.type ?? 'success',
        title: data.title ?? 'Done',
        text: data.text ?? undefined,
        timer: (data.type ?? 'success') === 'success' ? 2800 : undefined,
        showConfirmButton: (data.type ?? 'success') !== 'success',
    });

    element.remove();
};

let livewireAlertsBound = false;

const bindLivewireAlerts = () => {
    if (!window.Livewire || livewireAlertsBound) {
        return;
    }

    livewireAlertsBound = true;

    Livewire.on('swal-loading', async (payload) => {
        const data = normalizePayload(payload);
        const Swal = await getSwal();

        Swal.fire({
            ...swalDefaults,
            title: data.title ?? 'Saving...',
            text: data.text ?? 'Please wait',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading(),
        });
    });

    Livewire.on('swal', async (payload) => {
        const data = normalizePayload(payload);
        const Swal = await getSwal();

        if (data.closeLoading !== false) {
            Swal.close();
        }

        Swal.fire({
            ...swalDefaults,
            icon: data.type ?? 'success',
            title: data.title ?? 'Done',
            text: data.text ?? undefined,
            timer: data.type === 'success' ? 2200 : undefined,
            showConfirmButton: data.type !== 'success',
        });
    });
};

const initPortfolioAlerts = async () => {
    if (document.getElementById('auth-flash-data') || document.getElementById('swal-flash-data')) {
        await showSessionFlashAlert('auth-flash-data');
        await showSessionFlashAlert('swal-flash-data');
    }

    bindLivewireAlerts();
};

window.portfolioConfirmCancel = async (livewire, options = {}) => {
    const result = await confirmDialog({
        title: options.title ?? 'Leave without saving?',
        text: options.text ?? 'Unsaved changes will be lost.',
        confirmText: options.confirmText ?? 'Leave',
        cancelText: 'Stay',
    });

    if (result.isConfirmed && livewire) {
        livewire.cancel();
    }
};

window.portfolioConfirmSave = async (livewire, options = {}) => {
    const result = await confirmDialog({
        title: options.title ?? 'Save changes?',
        text: options.text ?? 'Your updates will be published to the live site.',
        confirmText: options.confirmText ?? 'Save',
        cancelText: 'Cancel',
    });

    if (result.isConfirmed && livewire) {
        const onCreatePage = window.location.pathname.includes('/create');

        if (onCreatePage && typeof livewire.create === 'function') {
            await livewire.create();
        } else if (typeof livewire.save === 'function') {
            await livewire.save();
        } else if (typeof livewire.create === 'function') {
            await livewire.create();
        }
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initPortfolioAlerts();
    }, { once: true });
} else {
    initPortfolioAlerts();
}

document.addEventListener('livewire:init', () => {
    bindLivewireAlerts();

    Livewire.hook('request', ({ fail }) => {
        fail(async () => {
            const Swal = await getSwal();
            Swal.close();
        });
    });
});

document.addEventListener('livewire:navigated', () => {
    showSessionFlashAlert('auth-flash-data');
    showSessionFlashAlert('swal-flash-data');
});
