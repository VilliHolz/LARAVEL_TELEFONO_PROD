@include('admin.layouts.partials.head')

<body>
    <div class="container-scroller">
        @include('admin.layouts.partials.navbar')
        <div class="container-fluid page-body-wrapper">
            @include('admin.layouts.partials.sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @if (session('success-sucursal'))
                        <div class="alert alert-success">
                            {{ session('success-sucursal') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
                @include('admin.layouts.partials.footer')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.cookie.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/admin/js/hoverable-collapse.js') }}"></script>
    {{-- <script src="{{ asset('assets/admin/js/misc.js') }}"></script> --}}
    <script src="{{ asset('assets/admin/js/all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function abrirVentanaEmergente(url) {
            const width = 800;
            const height = 600;

            const left = (window.screen.width / 2) - (width / 2);
            const top = (window.screen.height / 2) - (height / 2);

            const pdfWindow = window.open(
                url,
                '_blank',
                `width=${width},height=${height},top=${top},scrollbars=yes,resizable=yes`
            );

            pdfWindow.onload = function() {
                pdfWindow.print();
            };
        }

        if (document.getElementById('sucursal_id')) {
            document.getElementById('sucursal_id').addEventListener('change', function() {
                document.getElementById('sucursalForm').submit();
            });
        }
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
