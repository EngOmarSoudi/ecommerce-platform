<div class="card">
    <h2 class="text-xl font-semibold mb-6 dark:text-white">Email Configuration</h2>
    
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="tab" value="email">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- SMTP Settings -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">SMTP Settings</h3>
                
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Mail Driver *</label>
                    <select name="mail_driver" required class="input w-full" id="mailDriver">
                        <option value="smtp" {{ ($settings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ ($settings['mail_driver'] ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ ($settings['mail_driver'] ?? '') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">SMTP Host *</label>
                    <input 
                        type="text" 
                        name="mail_host" 
                        value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" 
                        placeholder="smtp.gmail.com"
                        required
                        class="input w-full">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">SMTP Port *</label>
                        <input 
                            type="number" 
                            name="mail_port" 
                            value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" 
                            placeholder="587"
                            required
                            class="input w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Encryption</label>
                        <select name="mail_encryption" class="input w-full">
                            <option value="">None</option>
                            <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">SMTP Username *</label>
                    <input 
                        type="text" 
                        name="mail_username" 
                        value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" 
                        required
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">SMTP Password *</label>
                    <input 
                        type="password" 
                        name="mail_password" 
                        value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" 
                        placeholder="Leave blank to keep current"
                        class="input w-full">
                    <p class="text-xs text-gray-500 mt-1">Password is encrypted in the database</p>
                </div>
            </div>

            <!-- Sender Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sender Information</h3>
                
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">From Email *</label>
                    <input 
                        type="email" 
                        name="mail_from_address" 
                        value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" 
                        placeholder="noreply@example.com"
                        required
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">From Name *</label>
                    <input 
                        type="text" 
                        name="mail_from_name" 
                        value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" 
                        placeholder="Your Store Name"
                        required
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Reply-To Email</label>
                    <input 
                        type="email" 
                        name="mail_reply_to" 
                        value="{{ old('mail_reply_to', $settings['mail_reply_to'] ?? '') }}" 
                        placeholder="support@example.com"
                        class="input w-full">
                </div>

                <!-- Test Email -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                    <h4 class="font-medium text-gray-900 dark:text-white">Test Email Configuration</h4>
                    <div>
                        <input 
                            type="email" 
                            id="testEmailAddress"
                            placeholder="test@example.com"
                            class="input w-full mb-2">
                        <button 
                            type="button" 
                            onclick="sendTestEmail()"
                            class="btn-outline w-full">
                            Send Test Email
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Send a test email to verify your configuration</p>
                </div>

                <!-- Email Templates -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Email Templates</h4>
                    <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                        Customize email templates for:
                    </p>
                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• Order confirmation</li>
                        <li>• Shipping notifications</li>
                        <li>• Password reset</li>
                        <li>• Welcome emails</li>
                    </ul>
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">
                        Manage Templates →
                    </a>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t dark:border-gray-700">
            <button type="reset" class="btn-outline">Reset</button>
            <button type="submit" class="btn-primary">Save Email Settings</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function sendTestEmail() {
    const email = document.getElementById('testEmailAddress').value;
    if (!email) {
        alert('Please enter an email address');
        return;
    }
    
    // Send test email via AJAX
    fetch('{{ route("settings.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test email sent successfully! Check your inbox.');
        } else {
            alert('Failed to send test email: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error sending test email: ' + error.message);
    });
}
</script>
@endpush
