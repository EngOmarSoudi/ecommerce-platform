<div class="card">
    <h2 class="text-xl font-semibold mb-6 dark:text-white">SMS Configuration</h2>
    
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="tab" value="sms">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Provider Settings -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">SMS Provider</h3>
                
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">SMS Service Provider *</label>
                    <select name="sms_provider" required class="input w-full" id="smsProvider">
                        <option value="">Select Provider</option>
                        <option value="twilio" {{ ($settings['sms_provider'] ?? 'twilio') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                        <option value="nexmo" {{ ($settings['sms_provider'] ?? '') === 'nexmo' ? 'selected' : '' }}>Vonage (Nexmo)</option>
                        <option value="aws_sns" {{ ($settings['sms_provider'] ?? '') === 'aws_sns' ? 'selected' : '' }}>AWS SNS</option>
                        <option value="messagebird" {{ ($settings['sms_provider'] ?? '') === 'messagebird' ? 'selected' : '' }}>MessageBird</option>
                    </select>
                </div>

                <!-- Twilio Settings -->
                <div id="twilioSettings" style="display: {{ ($settings['sms_provider'] ?? 'twilio') === 'twilio' ? 'block' : 'none' }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Twilio Account SID</label>
                            <input 
                                type="text" 
                                name="twilio_account_sid" 
                                value="{{ old('twilio_account_sid', $settings['twilio_account_sid'] ?? '') }}" 
                                class="input w-full">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Twilio Auth Token</label>
                            <input 
                                type="password" 
                                name="twilio_auth_token" 
                                value="{{ old('twilio_auth_token', $settings['twilio_auth_token'] ?? '') }}" 
                                placeholder="Leave blank to keep current"
                                class="input w-full">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Twilio Phone Number</label>
                            <input 
                                type="tel" 
                                name="twilio_phone_number" 
                                value="{{ old('twilio_phone_number', $settings['twilio_phone_number'] ?? '') }}" 
                                placeholder="+1234567890"
                                class="input w-full">
                        </div>
                    </div>
                </div>

                <!-- Nexmo Settings -->
                <div id="nexmoSettings" style="display: {{ ($settings['sms_provider'] ?? '') === 'nexmo' ? 'block' : 'none' }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Vonage API Key</label>
                            <input 
                                type="text" 
                                name="nexmo_api_key" 
                                value="{{ old('nexmo_api_key', $settings['nexmo_api_key'] ?? '') }}" 
                                class="input w-full">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Vonage API Secret</label>
                            <input 
                                type="password" 
                                name="nexmo_api_secret" 
                                value="{{ old('nexmo_api_secret', $settings['nexmo_api_secret'] ?? '') }}" 
                                placeholder="Leave blank to keep current"
                                class="input w-full">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Sender ID</label>
                            <input 
                                type="text" 
                                name="nexmo_sender_id" 
                                value="{{ old('nexmo_sender_id', $settings['nexmo_sender_id'] ?? '') }}" 
                                class="input w-full">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input 
                        type="checkbox" 
                        name="sms_enabled" 
                        id="smsEnabled"
                        value="1"
                        {{ ($settings['sms_enabled'] ?? false) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-brand.primary focus:ring-brand.primary">
                    <label for="smsEnabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable SMS Notifications</label>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">SMS Notifications</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            name="sms_order_confirmation" 
                            id="smsOrderConfirmation"
                            value="1"
                            {{ ($settings['sms_order_confirmation'] ?? true) ? 'checked' : '' }}
                            class="mt-1 rounded border-gray-300 text-brand.primary focus:ring-brand.primary">
                        <div>
                            <label for="smsOrderConfirmation" class="text-sm font-medium text-gray-700 dark:text-gray-300">Order Confirmation</label>
                            <p class="text-xs text-gray-500">Send SMS when order is placed</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            name="sms_order_shipped" 
                            id="smsOrderShipped"
                            value="1"
                            {{ ($settings['sms_order_shipped'] ?? true) ? 'checked' : '' }}
                            class="mt-1 rounded border-gray-300 text-brand.primary focus:ring-brand.primary">
                        <div>
                            <label for="smsOrderShipped" class="text-sm font-medium text-gray-700 dark:text-gray-300">Order Shipped</label>
                            <p class="text-xs text-gray-500">Send SMS when order ships</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            name="sms_order_delivered" 
                            id="smsOrderDelivered"
                            value="1"
                            {{ ($settings['sms_order_delivered'] ?? false) ? 'checked' : '' }}
                            class="mt-1 rounded border-gray-300 text-brand.primary focus:ring-brand.primary">
                        <div>
                            <label for="smsOrderDelivered" class="text-sm font-medium text-gray-700 dark:text-gray-300">Order Delivered</label>
                            <p class="text-xs text-gray-500">Send SMS when order is delivered</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            name="sms_low_stock" 
                            id="smsLowStock"
                            value="1"
                            {{ ($settings['sms_low_stock'] ?? false) ? 'checked' : '' }}
                            class="mt-1 rounded border-gray-300 text-brand.primary focus:ring-brand.primary">
                        <div>
                            <label for="smsLowStock" class="text-sm font-medium text-gray-700 dark:text-gray-300">Low Stock Alert</label>
                            <p class="text-xs text-gray-500">Send SMS to admin when stock is low</p>
                        </div>
                    </div>
                </div>

                <!-- Test SMS -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                    <h4 class="font-medium text-gray-900 dark:text-white">Test SMS</h4>
                    <div>
                        <input 
                            type="tel" 
                            id="testPhoneNumber"
                            placeholder="+1234567890"
                            class="input w-full mb-2">
                        <button 
                            type="button" 
                            onclick="sendTestSMS()"
                            class="btn-outline w-full">
                            Send Test SMS
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Send a test SMS to verify your configuration</p>
                </div>

                <!-- Cost Warning -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h4 class="font-medium text-yellow-900 dark:text-yellow-100">Cost Notice</h4>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200 mt-1">
                                SMS messages incur costs from your provider. Monitor usage to avoid unexpected charges.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t dark:border-gray-700">
            <button type="reset" class="btn-outline">Reset</button>
            <button type="submit" class="btn-primary">Save SMS Settings</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('smsProvider')?.addEventListener('change', function() {
    const provider = this.value;
    document.getElementById('twilioSettings').style.display = provider === 'twilio' ? 'block' : 'none';
    document.getElementById('nexmoSettings').style.display = provider === 'nexmo' ? 'block' : 'none';
});

function sendTestSMS() {
    const phone = document.getElementById('testPhoneNumber').value;
    if (!phone) {
        alert('Please enter a phone number');
        return;
    }
    
    fetch('{{ route("settings.test-sms") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ phone })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test SMS sent successfully!');
        } else {
            alert('Failed to send test SMS: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error sending test SMS: ' + error.message);
    });
}
</script>
@endpush
