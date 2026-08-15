<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Notify.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            Notify.error("{{ session('error') }}");
        @endif

        @if(session('info'))
            Notify.info("{{ session('info') }}");
        @endif

        @if(session('warning'))
            Notify.warning("{{ session('warning') }}");
        @endif

        @if($errors->any())
            Notify.error("{!! implode('<br>', $errors->all()) !!}", "Errores de validación");
        @endif
    });
</script>
