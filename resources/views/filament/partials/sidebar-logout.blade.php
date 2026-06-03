@auth
    <div class="saas-sidebar-logout-wrap">
        <form
            action="{{ filament()->getLogoutUrl() }}"
            method="post"
            class="saas-sidebar-logout-form"
        >
            @csrf

            <button type="submit" class="saas-sidebar-logout-btn">
                <x-filament::icon
                    icon="heroicon-o-arrow-left-start-on-rectangle"
                    class="saas-sidebar-logout-btn__icon"
                />
                <span class="saas-sidebar-logout-btn__label">
                    {{ __('filament-panels::layout.actions.logout.label') }}
                </span>
            </button>
        </form>
    </div>
@endauth
