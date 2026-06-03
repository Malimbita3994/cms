@if (session('auth_alert'))
    <script type="application/json" id="auth-flash-data">@json(session('auth_alert'))</script>
@endif
