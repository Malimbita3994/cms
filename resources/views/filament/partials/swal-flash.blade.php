@if (session('swal_flash'))
    <script type="application/json" id="swal-flash-data">@json(session('swal_flash'))</script>
@endif
