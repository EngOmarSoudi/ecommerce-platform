<div class="card">
    <h2 class="text-xl font-semibold mb-6 dark:text-white">Localization Settings</h2>
    
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="tab" value="localization">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Default Currency *</label>
                    <select name="currency" required class="input w-full">
                        <option value="">Select Currency</option>
                        <option value="USD" {{ ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD - US Dollar ($)</option>
                        <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro (€)</option>
                        <option value="GBP" {{ ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP - British Pound (£)</option>
                        <option value="JPY" {{ ($settings['currency'] ?? '') === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen (¥)</option>
                        <option value="AUD" {{ ($settings['currency'] ?? '') === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar (A$)</option>
                        <option value="CAD" {{ ($settings['currency'] ?? '') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar (C$)</option>
                        <option value="INR" {{ ($settings['currency'] ?? '') === 'INR' ? 'selected' : '' }}>INR - Indian Rupee (₹)</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Currency Display Format</label>
                    <select name="currency_format" class="input w-full">
                        <option value="symbol_before" {{ ($settings['currency_format'] ?? 'symbol_before') === 'symbol_before' ? 'selected' : '' }}>$100.00 (Symbol Before)</option>
                        <option value="symbol_after" {{ ($settings['currency_format'] ?? '') === 'symbol_after' ? 'selected' : '' }}>100.00$ (Symbol After)</option>
                        <option value="code_before" {{ ($settings['currency_format'] ?? '') === 'code_before' ? 'selected' : '' }}>USD 100.00 (Code Before)</option>
                        <option value="code_after" {{ ($settings['currency_format'] ?? '') === 'code_after' ? 'selected' : '' }}>100.00 USD (Code After)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Default Locale *</label>
                    <select name="locale" required class="input w-full">
                        <option value="en_US" {{ ($settings['locale'] ?? 'en_US') === 'en_US' ? 'selected' : '' }}>English (United States)</option>
                        <option value="en_GB" {{ ($settings['locale'] ?? '') === 'en_GB' ? 'selected' : '' }}>English (United Kingdom)</option>
                        <option value="es_ES" {{ ($settings['locale'] ?? '') === 'es_ES' ? 'selected' : '' }}>Spanish (Spain)</option>
                        <option value="fr_FR" {{ ($settings['locale'] ?? '') === 'fr_FR' ? 'selected' : '' }}>French (France)</option>
                        <option value="de_DE" {{ ($settings['locale'] ?? '') === 'de_DE' ? 'selected' : '' }}>German (Germany)</option>
                        <option value="ja_JP" {{ ($settings['locale'] ?? '') === 'ja_JP' ? 'selected' : '' }}>Japanese (Japan)</option>
                        <option value="zh_CN" {{ ($settings['locale'] ?? '') === 'zh_CN' ? 'selected' : '' }}>Chinese (Simplified)</option>
                    </select>
                    @error('locale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Timezone *</label>
                    <select name="timezone" required class="input w-full">
                        <option value="UTC" {{ ($settings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                        <option value="America/Chicago" {{ ($settings['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' }}>America/Chicago (CST)</option>
                        <option value="America/Denver" {{ ($settings['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' }}>America/Denver (MST)</option>
                        <option value="America/Los_Angeles" {{ ($settings['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los Angeles (PST)</option>
                        <option value="Europe/London" {{ ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                        <option value="Europe/Paris" {{ ($settings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
                        <option value="Asia/Tokyo" {{ ($settings['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (JST)</option>
                        <option value="Asia/Shanghai" {{ ($settings['timezone'] ?? '') === 'Asia/Shanghai' ? 'selected' : '' }}>Asia/Shanghai (CST)</option>
                    </select>
                    @error('timezone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Date Format</label>
                    <select name="date_format" class="input w-full">
                        <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>2025-01-15 (YYYY-MM-DD)</option>
                        <option value="m/d/Y" {{ ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>01/15/2025 (MM/DD/YYYY)</option>
                        <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>15/01/2025 (DD/MM/YYYY)</option>
                        <option value="d-M-Y" {{ ($settings['date_format'] ?? '') === 'd-M-Y' ? 'selected' : '' }}>15-Jan-2025 (DD-Mon-YYYY)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Time Format</label>
                    <select name="time_format" class="input w-full">
                        <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>14:30 (24-hour)</option>
                        <option value="h:i A" {{ ($settings['time_format'] ?? '') === 'h:i A' ? 'selected' : '' }}>02:30 PM (12-hour)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Weight Unit</label>
                    <select name="weight_unit" class="input w-full">
                        <option value="kg" {{ ($settings['weight_unit'] ?? 'kg') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="lb" {{ ($settings['weight_unit'] ?? '') === 'lb' ? 'selected' : '' }}>Pound (lb)</option>
                        <option value="g" {{ ($settings['weight_unit'] ?? '') === 'g' ? 'selected' : '' }}>Gram (g)</option>
                        <option value="oz" {{ ($settings['weight_unit'] ?? '') === 'oz' ? 'selected' : '' }}>Ounce (oz)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Dimension Unit</label>
                    <select name="dimension_unit" class="input w-full">
                        <option value="cm" {{ ($settings['dimension_unit'] ?? 'cm') === 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                        <option value="m" {{ ($settings['dimension_unit'] ?? '') === 'm' ? 'selected' : '' }}>Meter (m)</option>
                        <option value="in" {{ ($settings['dimension_unit'] ?? '') === 'in' ? 'selected' : '' }}>Inch (in)</option>
                        <option value="ft" {{ ($settings['dimension_unit'] ?? '') === 'ft' ? 'selected' : '' }}>Foot (ft)</option>
                    </select>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Note:</strong> Changes to locale and timezone will affect how dates, times, and numbers are displayed throughout the system.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t dark:border-gray-700">
            <button type="reset" class="btn-outline">Reset</button>
            <button type="submit" class="btn-primary">Save Localization Settings</button>
        </div>
    </form>
</div>
