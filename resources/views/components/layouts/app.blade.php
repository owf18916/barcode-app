<!DOCTYPE html>
<html lang="en" x-data @scan-result.window="($event.detail.success ? $refs.success.play() : $refs.fail.play())">
<head>
    <meta charset="UTF-8" />
    <title>Barcode App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="p-6 bg-gray-100">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        {{ $slot }}
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.plugin(window.focus);
        });
    </script>
</body>
</html>
