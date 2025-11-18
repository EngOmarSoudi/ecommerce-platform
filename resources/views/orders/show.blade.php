<x-layouts.dashboard>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $order->order_number }}</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
    </div>

    <div x-data="{ showRefundModal: false, refundAmount: {{ $order->total_amount }}, refundReason: '' }">
        <!-- Action Buttons -->
        <div class="mb-6 flex gap-2">
            <button onclick="window.print()" class="btn-outline">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Invoice
            </button>
            @if($order->payment_status === 'paid' && !$order->refund)
            <button @click="showRefundModal = true" class="btn-outline">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Process Refund
            </button>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Order Items</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-4 pb-4 border-b last:border-b-0 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded overflow-hidden flex-shrink-0">
                                <img src="{{ $item->product->image_url ?? '/assets/placeholder.png' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                </p>
                                @if($item->variant_options)
                                <p class="text-xs text-gray-500 mt-1">{{ $item->variant_options }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-4 pt-4 border-t dark:border-gray-700 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="dark:text-white">${{ number_format($order->subtotal_amount, 2) }}</span>
                        </div>
                        @if($order->shipping_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                            <span class="dark:text-white">${{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($order->tax_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tax</span>
                            <span class="dark:text-white">${{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Discount</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2 border-t dark:border-gray-700">
                            <span class="dark:text-white">Total</span>
                            <span class="text-brand.primary">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping & Tracking -->
                @if($order->shipments->count() > 0)
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Shipping & Tracking</h2>
                    @foreach($order->shipments as $shipment)
                    <div class="border dark:border-gray-700 rounded-lg p-4 mb-4 last:mb-0">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="font-medium dark:text-white">{{ $shipment->carrier_name ?? 'Standard Shipping' }}</p>
                                @if($shipment->tracking_number)
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tracking: {{ $shipment->tracking_number }}</p>
                                @endif
                            </div>
                            @if($shipment->tracking_url)
                            <a href="{{ $shipment->tracking_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                                Track Package →
                            </a>
                            @endif
                        </div>
                        @if($shipment->shipped_at)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Shipped on {{ $shipment->shipped_at->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Order Timeline -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Order Timeline</h2>
                    <div class="space-y-4">
                        @php
                            $timeline = [
                                ['status' => 'created', 'label' => 'Order Placed', 'date' => $order->created_at, 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                                ['status' => 'paid', 'label' => 'Payment Received', 'date' => $order->paid_at, 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                                ['status' => 'processing', 'label' => 'Processing', 'date' => $order->processing_at, 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                                ['status' => 'shipped', 'label' => 'Shipped', 'date' => $order->shipped_at, 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
                                ['status' => 'delivered', 'label' => 'Delivered', 'date' => $order->delivered_at, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ];
                        @endphp

                        @foreach($timeline as $index => $event)
                            @if($event['date'])
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $event['icon'] }}"/></svg>
                                    </div>
                                </div>
                                <div class="flex-1 pb-4" :class="{ 'border-l-2 border-gray-200 dark:border-gray-700 pl-4 ml-5': {{ $index < count($timeline) - 1 ? 'true' : 'false' }} }">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $event['label'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event['date']->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Customer Info -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Customer</h3>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name ?? 'Guest' }}</p>
                        @if($order->customer_email)
                        <p class="text-gray-600 dark:text-gray-400">{{ $order->customer_email }}</p>
                        @endif
                        @if($order->customer_phone)
                        <p class="text-gray-600 dark:text-gray-400">{{ $order->customer_phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Shipping Address -->
                @if($order->shippingAddress)
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Shipping Address</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>{{ $order->shippingAddress->address_line1 }}</p>
                        @if($order->shippingAddress->address_line2)
                        <p>{{ $order->shippingAddress->address_line2 }}</p>
                        @endif
                        <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                        <p>{{ $order->shippingAddress->country }}</p>
                    </div>
                </div>
                @endif

                <!-- Billing Address -->
                @if($order->billingAddress)
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Billing Address</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>{{ $order->billingAddress->address_line1 }}</p>
                        @if($order->billingAddress->address_line2)
                        <p>{{ $order->billingAddress->address_line2 }}</p>
                        @endif
                        <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                        <p>{{ $order->billingAddress->country }}</p>
                    </div>
                </div>
                @endif

                <!-- Payment Info -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Payment</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Method:</span>
                            <span class="font-medium dark:text-white">{{ ucfirst($order->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="font-medium" :class="{
                                'text-green-600': '{{ $order->payment_status }}' === 'paid',
                                'text-yellow-600': '{{ $order->payment_status }}' === 'pending',
                                'text-red-600': '{{ $order->payment_status }}' === 'failed'
                            }">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Refund Info -->
                @if($order->refund)
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 text-red-600">Refund Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                            <span class="font-medium text-red-600">${{ number_format($order->refund->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="font-medium dark:text-white">{{ ucfirst($order->refund->status) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Date:</span>
                            <span class="dark:text-white">{{ $order->refund->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($order->refund->reason)
                        <div class="pt-2 border-t dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Reason:</p>
                            <p class="dark:text-white">{{ $order->refund->reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Refund Modal -->
        <div x-show="showRefundModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="showRefundModal = false"
             style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full"
                 @click.stop>
                <form action="{{ route('refunds.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold dark:text-white">Process Refund</h2>
                            <button type="button" @click="showRefundModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Refund Amount *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input 
                                        type="number" 
                                        name="amount"
                                        x-model="refundAmount"
                                        step="0.01"
                                        min="0.01"
                                        :max="{{ $order->total_amount }}"
                                        required
                                        class="input w-full pl-8"
                                        placeholder="0.00">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Maximum: ${{ number_format($order->total_amount, 2) }}</p>
                                <p x-show="refundAmount > {{ $order->total_amount }}" class="mt-1 text-xs text-red-600">
                                    Refund amount cannot exceed order total
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Reason *</label>
                                <textarea 
                                    name="reason"
                                    x-model="refundReason"
                                    rows="3"
                                    required
                                    class="input w-full"
                                    placeholder="Explain why this refund is being processed..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Refund Method</label>
                                <select name="refund_method" class="input w-full">
                                    <option value="original">Original Payment Method</option>
                                    <option value="store_credit">Store Credit</option>
                                    <option value="manual">Manual/Other</option>
                                </select>
                            </div>

                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    ⚠️ This action cannot be undone. The refund will be processed immediately.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showRefundModal = false" class="flex-1 btn-outline">Cancel</button>
                            <button 
                                type="submit"
                                :disabled="refundAmount > {{ $order->total_amount }} || refundAmount <= 0 || !refundReason"
                                class="flex-1 btn-primary bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                Process Refund
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
