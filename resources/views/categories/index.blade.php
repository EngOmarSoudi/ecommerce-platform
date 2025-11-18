<x-layouts.dashboard>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Categories</h1>
            <p class="text-gray-600 dark:text-gray-400">Organize your product catalog</p>
        </div>
        <div class="flex gap-2">
            <button @click="$dispatch('toggle-view')" class="btn-outline" x-data>
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span x-text="$store.categoryView.isTree ? 'Table View' : 'Tree View'"></span>
            </button>
            <a href="{{ route('categories.create') }}" class="btn-primary">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Category
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search categories..."
                        class="input w-full pl-10">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <select id="statusFilter" class="input w-40">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select id="parentFilter" class="input w-48">
                <option value="">All Levels</option>
                <option value="parent">Parent Only</option>
                <option value="subcategory">Subcategories Only</option>
            </select>
        </div>
    </div>

    <div x-data="categoryManager()" @toggle-view.window="toggleView()">
        <!-- Tree View -->
        <div x-show="isTreeView" x-transition class="card">
            <div class="space-y-2" id="categoryTree">
                <template x-for="category in filteredCategories.filter(c => !c.parent_id)" :key="category.id">
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                        <!-- Parent Category -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                             :class="{ 'bg-blue-50 dark:bg-blue-900/20': draggedId === category.id }"
                             draggable="true"
                             @dragstart="dragStart($event, category.id)"
                             @dragover.prevent
                             @drop="drop($event, category.id)">
                            <div class="flex items-center gap-3 flex-1">
                                <button 
                                    @click="toggleExpand(category.id)"
                                    x-show="category.children_count > 0"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-90': expandedCategories.includes(category.id) }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div class="w-12 h-12 rounded bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                    <span x-text="category.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white" x-text="category.name"></h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <span x-text="category.products_count || 0"></span> products
                                        <span x-show="category.children_count > 0" class="ml-2">
                                            • <span x-text="category.children_count"></span> subcategories
                                        </span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge" :class="category.is_active ? 'badge-success' : 'bg-gray-100 text-gray-600'" x-text="category.is_active ? 'Active' : 'Inactive'"></span>
                                    <span class="text-sm text-gray-500 px-2">Order: <span x-text="category.sort_order || 0"></span></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <a :href="`/categories/${category.id}/edit`" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button @click="deleteCategory(category.id, category.name)" class="text-red-600 hover:text-red-900 dark:text-red-400 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Subcategories -->
                        <div x-show="expandedCategories.includes(category.id) && category.children_count > 0" 
                             x-transition
                             class="bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                            <template x-for="subcategory in filteredCategories.filter(c => c.parent_id === category.id)" :key="subcategory.id">
                                <div class="flex items-center justify-between p-4 pl-16 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b last:border-b-0 dark:border-gray-700"
                                     :class="{ 'bg-blue-50 dark:bg-blue-900/20': draggedId === subcategory.id }"
                                     draggable="true"
                                     @dragstart="dragStart($event, subcategory.id)"
                                     @dragover.prevent
                                     @drop="drop($event, subcategory.id)">
                                    <div class="flex items-center gap-3 flex-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        <div class="w-10 h-10 rounded bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold">
                                            <span x-text="subcategory.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-white" x-text="subcategory.name"></h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                <span x-text="subcategory.products_count || 0"></span> products
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="badge text-xs" :class="subcategory.is_active ? 'badge-success' : 'bg-gray-100 text-gray-600'" x-text="subcategory.is_active ? 'Active' : 'Inactive'"></span>
                                            <span class="text-sm text-gray-500 px-2">Order: <span x-text="subcategory.sort_order || 0"></span></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        <a :href="`/categories/${subcategory.id}/edit`" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <button @click="deleteCategory(subcategory.id, subcategory.name)" class="text-red-600 hover:text-red-900 dark:text-red-400 p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <p class="text-lg font-medium">No categories found</p>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div x-show="!isTreeView" x-transition class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Parent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="category in filteredCategories" :key="category.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded bg-gradient-to-br flex items-center justify-center text-white font-bold"
                                             :class="category.parent_id ? 'from-purple-400 to-purple-600' : 'from-blue-400 to-blue-600'">
                                            <span x-text="category.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white" x-text="category.name"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="category.slug"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span x-text="category.parent_name || '—'" class="text-sm text-gray-600 dark:text-gray-400"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span x-text="category.products_count || 0" class="text-sm text-gray-900 dark:text-gray-300"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span x-text="category.sort_order || 0" class="text-sm text-gray-600 dark:text-gray-400"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="badge" :class="category.is_active ? 'badge-success' : 'bg-gray-100 text-gray-600'" x-text="category.is_active ? 'Active' : 'Inactive'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="`/categories/${category.id}/edit`" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <button @click="deleteCategory(category.id, category.name)" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Alpine store for view toggle
        document.addEventListener('alpine:init', () => {
            Alpine.store('categoryView', {
                isTree: localStorage.getItem('categoryView') === 'tree',
                
                toggle() {
                    this.isTree = !this.isTree;
                    localStorage.setItem('categoryView', this.isTree ? 'tree' : 'table');
                }
            });
        });

        function categoryManager() {
            return {
                categories: @json($categories),
                filteredCategories: [],
                expandedCategories: [],
                isTreeView: Alpine.store('categoryView').isTree,
                draggedId: null,
                searchQuery: '',

                init() {
                    this.filteredCategories = this.categories;
                    this.setupFilters();
                },

                setupFilters() {
                    const searchInput = document.getElementById('searchInput');
                    const statusFilter = document.getElementById('statusFilter');
                    const parentFilter = document.getElementById('parentFilter');

                    const applyFilters = () => {
                        let filtered = this.categories;

                        // Search filter
                        const query = searchInput.value.toLowerCase();
                        if (query) {
                            filtered = filtered.filter(c => 
                                c.name.toLowerCase().includes(query) || 
                                c.slug.toLowerCase().includes(query)
                            );
                        }

                        // Status filter
                        const status = statusFilter.value;
                        if (status === 'active') {
                            filtered = filtered.filter(c => c.is_active);
                        } else if (status === 'inactive') {
                            filtered = filtered.filter(c => !c.is_active);
                        }

                        // Parent filter
                        const parent = parentFilter.value;
                        if (parent === 'parent') {
                            filtered = filtered.filter(c => !c.parent_id);
                        } else if (parent === 'subcategory') {
                            filtered = filtered.filter(c => c.parent_id);
                        }

                        this.filteredCategories = filtered;
                    };

                    searchInput.addEventListener('input', applyFilters);
                    statusFilter.addEventListener('change', applyFilters);
                    parentFilter.addEventListener('change', applyFilters);
                },

                toggleView() {
                    this.isTreeView = !this.isTreeView;
                    Alpine.store('categoryView').toggle();
                },

                toggleExpand(id) {
                    const index = this.expandedCategories.indexOf(id);
                    if (index > -1) {
                        this.expandedCategories.splice(index, 1);
                    } else {
                        this.expandedCategories.push(id);
                    }
                },

                dragStart(event, id) {
                    this.draggedId = id;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/html', event.target.innerHTML);
                },

                drop(event, targetId) {
                    if (this.draggedId !== targetId) {
                        // In production, send AJAX request to update sort order
                        console.log(`Reorder: ${this.draggedId} before ${targetId}`);
                        // this.updateSortOrder(this.draggedId, targetId);
                    }
                    this.draggedId = null;
                },

                deleteCategory(id, name) {
                    if (confirm(`Are you sure you want to delete "${name}"? This will also delete all subcategories and reassign products.`)) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/categories/${id}`;
                        form.innerHTML = `
                            @csrf
                            @method('DELETE')
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>
