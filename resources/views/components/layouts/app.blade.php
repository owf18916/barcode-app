<!DOCTYPE html>
<html lang="en" x-data @scan-result.window="($event.detail.success ? $refs.success.play() : $refs.fail.play())">
<head>
    <meta charset="UTF-8" />
    <title>JAI Kanban & S/A Scanner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="p-6 bg-gray-100">

    <div class="max-w-4xl mx-auto px-4 mb-4 sm:px-6 lg:px-8 space-y-8">
        {{ $slot }}
    </div>

    <div class="text-center text-sm text-gray-400">
        &copy; {{ now()->year }} Jatim Autocomp Indonesia. 
        @unless(session()->has('admin_logged_in'))
            <a href="{{ route('admin.login') }}" class="text-blue-500 hover:underline">Admin Login</a>
        @endunless
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('alpine:init', () => {
                Alpine.plugin(window.focus);
            });
    
            Livewire.on("swal-fired", (params) => {
                const { title, message, type, footer = null } = params[0];
    
                Swal.fire({
                    title: title,
                    text: message,
                    icon: type,
                    confirmButtonText: "Ok",
                    footer: footer
                });
            });

            document.addEventListener("confirmation-fired", function (event) {
                const { eventName, rowId = null, title = "Yakin ?", message =  "Tekan Ya jika Anda sudah yakin."} = event.detail
    
                Swal.fire({
                    title: title,
                    text: message,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(eventName, { id: rowId });
                    }
                });
            });
    
            document.addEventListener("info-fired", function (event) {
                const { title = "Infromasi Penting", message =  ""} = event.detail
    
                Swal.fire({
                    title: title,
                    text: message,
                    icon: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ya",
                })
            });

            Livewire.on("toast-fired", (params) => {
                const { title, icon } = params[0];
    
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });
    
                Toast.fire({
                    icon: icon,
                    title: title,
                });
            });
        });
    </script>
</body>
</html>
