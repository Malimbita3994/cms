@php
    $workspaceApi = [
        'search' => route('admin.workspace.search'),
        'notifications' => route('admin.workspace.notifications'),
        'notificationsSummary' => route('admin.workspace.notifications.summary'),
        'notificationsRead' => route('admin.workspace.notifications.read'),
        'notificationsReadAll' => route('admin.workspace.notifications.read-all'),
    ];
@endphp

<script type="application/json" id="workspace-api-config">@json($workspaceApi)</script>

<div id="saas-command-palette" class="saas-cmd" hidden aria-hidden="true">
    <div class="saas-cmd__backdrop" data-cmd-close></div>
    <div class="saas-cmd__dialog" role="dialog" aria-modal="true" aria-label="Search workspace">
        <div class="saas-cmd__search-row">
            <svg class="saas-cmd__search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>
            </svg>
            <input
                type="search"
                class="saas-cmd__input"
                placeholder="Search pages, projects, services, insights…"
                autocomplete="off"
                aria-label="Search query"
            />
            <kbd class="saas-cmd__esc">Esc</kbd>
        </div>
        <div class="saas-cmd__meta">
            <span class="saas-cmd__hint" data-cmd-hint>Type to search · ↑↓ navigate · Enter open</span>
            <span class="saas-cmd__timing" data-cmd-timing hidden></span>
        </div>
        <div class="saas-cmd__results" data-cmd-results role="listbox" aria-label="Search results"></div>
        <div class="saas-cmd__empty" data-cmd-empty hidden>
            <p>No matches found</p>
            <span>Try project names, page titles, or user emails</span>
        </div>
    </div>
</div>

<div id="saas-notifications-panel" class="saas-notify-panel" hidden aria-hidden="true">
    <div class="saas-notify-panel__backdrop" data-notify-close></div>
    <div class="saas-notify-panel__sheet" role="dialog" aria-label="Notifications">
        <header class="saas-notify-panel__head">
            <div>
                <h2 class="saas-notify-panel__title">Notifications</h2>
                <p class="saas-notify-panel__sub" data-notify-sub>Activity across your workspace</p>
            </div>
            <button type="button" class="saas-notify-panel__mark" data-notify-mark-all>Mark all read</button>
        </header>
        <div class="saas-notify-panel__list" data-notify-list role="list"></div>
        <p class="saas-notify-panel__empty" data-notify-empty hidden>No notifications yet</p>
    </div>
</div>
