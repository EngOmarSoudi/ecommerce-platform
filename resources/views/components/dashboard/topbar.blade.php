<header class="bg-white dark:bg-gray-900 shadow sticky top-0 z-30">
    <div class="container flex items-center justify-between py-3 px-4">
        <button class="md:hidden btn-outline text-sm" @click="sidebar=!sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="flex-1 max-w-md mx-4 hidden md:block">
            <div class="relative">
                <input type="text" class="input w-full pl-10" placeholder="Search products, orders..." x-data @keyup.enter="console.log('Search:', $el.value)">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button class="btn-outline relative" @click="open=!open" title="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </button>
                <div x-show="open" @click.away="open=false" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50" style="display:none;">
                    <p class="px-4 py-2 text-sm font-semibold border-b dark:border-gray-700">Notifications</p>
                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                        <p class="font-medium">New order #12345</p>
                        <p class="text-xs text-gray-500">2 minutes ago</p>
                    </a>
                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                        <p class="font-medium">Low stock alert</p>
                        <p class="text-xs text-gray-500">1 hour ago</p>
                    </a>
                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                        <p class="font-medium">Payment received</p>
                        <p class="text-xs text-gray-500">3 hours ago</p>
                    </a>
                </div>
            </div>
            <button class="btn-outline" @click="toggleTheme()" title="Toggle theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            <div class="relative" x-data="{ open: false }">
                <button class="flex items-center gap-2 btn-outline" @click="open=!open">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="hidden md:inline text-sm">{{ auth()->user()->name ?? 'User' }}</span>
                </button>
                <div x-show="open" @click.away="open=false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50" style="display:none;">
                    <a href="/profile" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">Profile</a>
                    <a href="/settings" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">Settings</a>
                    <hr class="my-2 dark:border-gray-700">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-red-600">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
    function toggleTheme(){
        const root = document.documentElement;
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }
    (function(){
        const saved = localStorage.getItem('theme');
        if(saved === 'dark') document.documentElement.classList.add('dark');
    })();
</script>
