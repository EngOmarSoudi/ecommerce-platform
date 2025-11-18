<x-layouts.dashboard>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles & Permissions</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage user roles and their permissions</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Roles List -->
        <div class="lg:col-span-1">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold dark:text-white">Roles</h2>
                    <button @click="showCreateModal = true" class="btn-primary btn-sm">Add Role</button>
                </div>
                
                <div class="space-y-2" x-data="{ selectedRole: '{{ $roles->first()->id ?? null }}' }">
                    @forelse($roles as $role)
                    <button 
                        @click="selectedRole = '{{ $role->id }}'; window.location.href = '{{ route('permissions.index', ['role' => $role->id]) }}'"
                        :class="selectedRole == '{{ $role->id }}' ? 'bg-brand.primary/10 border-brand.primary text-brand.primary' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700'"
                        class="w-full px-4 py-3 rounded-lg border-2 text-left transition-colors hover:border-brand.primary">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">{{ $role->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $role->users_count ?? 0 }} users</p>
                            </div>
                            <svg class="w-5 h-5" :class="selectedRole == '{{ $role->id }}' ? '' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </button>
                    @empty
                    <p class="text-gray-500 text-sm">No roles found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Permissions Management -->
        <div class="lg:col-span-2">
            @if($selectedRole)
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold dark:text-white">{{ $selectedRole->name }} Permissions</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedRole->description }}</p>
                    </div>
                    <button class="btn-outline btn-sm">Edit Role</button>
                </div>

                <form action="{{ route('permissions.update', $selectedRole->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        @foreach($permissionGroups as $group => $permissions)
                        <div>
                            <div class="flex items-center justify-between mb-3 pb-2 border-b dark:border-gray-700">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ ucfirst($group) }}</h3>
                                <button 
                                    type="button"
                                    @click="toggleGroup('{{ $group }}')"
                                    class="text-sm text-brand.primary hover:underline">
                                    Toggle All
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions as $permission)
                                <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="permissions[]" 
                                        value="{{ $permission->id }}"
                                        {{ $selectedRole->permissions->contains($permission->id) ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-brand.primary focus:ring-brand.primary permission-checkbox"
                                        data-group="{{ $group }}">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm dark:text-white">{{ $permission->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t dark:border-gray-700">
                        <button type="reset" class="btn-outline">Reset</button>
                        <button type="submit" class="btn-primary">Save Permissions</button>
                    </div>
                </form>
            </div>
            @else
            <div class="card text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">Select a role to manage permissions</p>
            </div>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif

    @push('scripts')
    <script>
        function toggleGroup(group) {
            const checkboxes = document.querySelectorAll(`[data-group="${group}"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>
    @endpush
</x-layouts.dashboard>
