<x-layouts.dashboard>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your store configuration and preferences</p>
    </div>

    <!-- Settings Tabs -->
    <div class="mb-6" x-data="{ activeTab: '{{ request('tab', 'general') }}' }">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button 
                    @click="activeTab = 'general'; window.location.search = '?tab=general'"
                    :class="activeTab === 'general' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    General
                </button>
                <button 
                    @click="activeTab = 'email'; window.location.search = '?tab=email'"
                    :class="activeTab === 'email' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Email Configuration
                </button>
                <button 
                    @click="activeTab = 'sms'; window.location.search = '?tab=sms'"
                    :class="activeTab === 'sms' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    SMS Configuration
                </button>
                <button 
                    @click="activeTab = 'localization'; window.location.search = '?tab=localization'"
                    :class="activeTab === 'localization' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Localization
                </button>
            </nav>
        </div>
    </div>

    @if(request('tab', 'general') === 'general')
        @include('settings.general')
    @elseif(request('tab') === 'email')
        @include('settings.email')
    @elseif(request('tab') === 'sms')
        @include('settings.sms')
    @elseif(request('tab') === 'localization')
        @include('settings.localization')
    @endif

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif
</x-layouts.dashboard>
