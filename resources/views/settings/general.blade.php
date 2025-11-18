<div class="card" x-data="{ logoPreview: '{{ $settings['company_logo'] ?? '/assets/placeholder.png' }}' }">
    <h2 class="text-xl font-semibold mb-6 dark:text-white">General Settings</h2>
    
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="tab" value="general">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Company Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Company Information</h3>
                
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Company Name *</label>
                    <input 
                        type="text" 
                        name="company_name" 
                        value="{{ old('company_name', $settings['company_name'] ?? '') }}" 
                        required
                        class="input w-full">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Company Email *</label>
                    <input 
                        type="email" 
                        name="company_email" 
                        value="{{ old('company_email', $settings['company_email'] ?? '') }}" 
                        required
                        class="input w-full">
                    @error('company_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Company Phone</label>
                    <input 
                        type="tel" 
                        name="company_phone" 
                        value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" 
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Company Address</label>
                    <textarea 
                        name="company_address" 
                        rows="3"
                        class="input w-full">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Tax ID / Registration Number</label>
                    <input 
                        type="text" 
                        name="tax_id" 
                        value="{{ old('tax_id', $settings['tax_id'] ?? '') }}" 
                        class="input w-full">
                </div>
            </div>

            <!-- Branding & Logo -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Branding</h3>
                
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Company Logo</label>
                    <div class="space-y-4">
                        <!-- Logo Preview -->
                        <div class="flex items-center justify-center w-full h-48 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                            <img :src="logoPreview" alt="Company Logo" class="max-h-full max-w-full object-contain">
                        </div>
                        
                        <!-- Upload Input -->
                        <input 
                            type="file" 
                            name="company_logo" 
                            accept="image/*"
                            class="input w-full"
                            @change="event => {
                                const file = event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => logoPreview = e.target.result;
                                    reader.readAsDataURL(file);
                                }
                            }">
                        <p class="text-xs text-gray-500">Recommended: PNG or SVG, max 2MB</p>
                    </div>
                    @error('company_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Website URL</label>
                    <input 
                        type="url" 
                        name="website_url" 
                        value="{{ old('website_url', $settings['website_url'] ?? '') }}" 
                        placeholder="https://example.com"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Support Email</label>
                    <input 
                        type="email" 
                        name="support_email" 
                        value="{{ old('support_email', $settings['support_email'] ?? '') }}" 
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Support Phone</label>
                    <input 
                        type="tel" 
                        name="support_phone" 
                        value="{{ old('support_phone', $settings['support_phone'] ?? '') }}" 
                        class="input w-full">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t dark:border-gray-700">
            <button type="reset" class="btn-outline">Reset</button>
            <button type="submit" class="btn-primary">Save General Settings</button>
        </div>
    </form>
</div>
