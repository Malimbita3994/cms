<style>
    .fi-body {
        --sidebar-width: 14.5rem;
    }

    .fi-sidebar {
        background:
            radial-gradient(circle at top, rgba(37, 99, 235, 0.16), transparent 45%),
            linear-gradient(180deg, rgba(2, 6, 23, 0.96), rgba(8, 20, 48, 0.95));
        border-inline-end: 1px solid rgba(148, 163, 184, 0.16);
        backdrop-filter: blur(14px);
        width: var(--sidebar-width);
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        background: rgba(2, 6, 23, 0.45);
    }

    .fi-topbar .fi-logo {
        color: #0f172a;
    }

    .dark .fi-topbar .fi-logo {
        color: #f8fafc;
    }

    .fi-sidebar .fi-logo {
        color: #f8fafc;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .fi-sidebar-nav {
        padding-top: 1.1rem;
        padding-bottom: 1rem;
        gap: 1.15rem;
    }

    .fi-sidebar-nav-groups {
        gap: 1rem;
    }

    .fi-sidebar-group-label {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #bfdbfe;
    }

    .fi-sidebar-item-btn {
        border: 1px solid transparent;
        border-radius: 12px;
        min-height: 2.4rem;
        transition: all 0.2s ease;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background: linear-gradient(90deg, rgba(37, 99, 235, 0.34), rgba(59, 130, 246, 0.2));
        border-color: rgba(96, 165, 250, 0.5);
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.35);
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-icon {
        color: #eff6ff;
    }

    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover,
    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:focus-visible {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(148, 163, 184, 0.4);
    }

    .fi-sidebar-item-label {
        font-size: 0.92rem;
        font-weight: 600;
        color: #dbeafe;
    }

    .fi-sidebar-item-btn > .fi-icon {
        color: #bfdbfe;
    }

    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover > .fi-sidebar-item-label,
    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:focus-visible > .fi-sidebar-item-label,
    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover > .fi-icon,
    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:focus-visible > .fi-icon {
        color: #f8fafc;
    }

    .fi-sidebar-footer {
        border-top: 1px solid rgba(148, 163, 184, 0.16);
        padding-top: 0.85rem;
        margin-top: 0.4rem;
    }

    .fi-sidebar-database-notifications-btn {
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(2, 6, 23, 0.45);
    }

    .fi-sidebar-database-notifications-btn > .fi-sidebar-database-notifications-btn-label,
    .fi-sidebar-database-notifications-btn > .fi-icon {
        color: #dbeafe;
    }
</style>
