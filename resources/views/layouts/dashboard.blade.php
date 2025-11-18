<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebar:false }" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#3b82f6">
    <title>{{ config('app.name') }} — Dashboard</title>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/pwa/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/pwa/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/pwa/icon-512x512.png">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-gray-100 dark:bg-gray-950">
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <x-dashboard.sidebar />

    <!-- Content wrapper -->
    <div class="flex-1 md:ml-64">
        <x-dashboard.topbar />

        <main class="container py-6">
            {{ $slot }}
        </main>
    </div>
</div>

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('SW registered:', registration))
            .catch(error => console.log('SW registration failed:', error));
    });
}

// PWA Install Prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    // Show install button if needed
});
</script>

@stack('scripts')
</body>
</html>
