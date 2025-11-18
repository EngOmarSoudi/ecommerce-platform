<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        /* Print styles optimized for thermal & A4 printers */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10mm;
            max-width: 80mm; /* Thermal printer width */
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            @page {
                margin: 0;
            }
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        /* A4 mode */
        @media screen and (min-width: 600px) {
            body {
                max-width: 210mm;
                margin: 0 auto;
                padding: 20mm;
                background: #f5f5f5;
            }
            .receipt {
                background: white;
                padding: 15mm;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .large {
            font-size: 16px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 8px 0;
        }

        .header {
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .info-line {
            font-size: 11px;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            font-weight: bold;
        }

        td {
            padding: 5px 0;
        }

        .item-name {
            max-width: 40mm;
        }

        .item-qty {
            text-align: center;
            width: 15mm;
        }

        .item-price,
        .item-total {
            text-align: right;
            width: 20mm;
        }

        .totals {
            margin-top: 10px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .totals-row.grand-total {
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #000;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 11px;
        }

        .barcode {
            text-align: center;
            margin: 10px 0;
            font-size: 24px;
            letter-spacing: 2px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Receipt</button>

    <div class="receipt">
        <!-- Header -->
        <div class="header text-center">
            <div class="company-name">{{ config('app.name', 'E-Commerce POS') }}</div>
            <div class="info-line">123 Main Street, City, State 12345</div>
            <div class="info-line">Tel: (555) 123-4567</div>
            <div class="info-line">Email: info@example.com</div>
        </div>

        <div class="double-divider"></div>

        <!-- Receipt Info -->
        <div class="text-center">
            <div class="bold large">SALES RECEIPT</div>
        </div>

        <div class="divider"></div>

        <div class="info-line">
            <span>Date:</span>
            <span id="receipt-date"></span>
        </div>
        <div class="info-line">
            <span>Receipt #:</span>
            <span id="receipt-number"></span>
        </div>
        <div class="info-line" id="customer-line" style="display:none;">
            <span>Customer:</span>
            <span id="customer-name"></span>
        </div>
        <div class="info-line">
            <span>Cashier:</span>
            <span>{{ auth()->user()->name ?? 'Staff' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="item-name">Item</th>
                    <th class="item-qty">Qty</th>
                    <th class="item-price">Price</th>
                    <th class="item-total">Total</th>
                </tr>
            </thead>
            <tbody id="items-body">
                <!-- Populated by JavaScript -->
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span id="subtotal-amount">$0.00</span>
            </div>
            <div class="totals-row">
                <span>Tax (10%):</span>
                <span id="tax-amount">$0.00</span>
            </div>
            <div class="totals-row grand-total">
                <span>TOTAL:</span>
                <span id="total-amount">$0.00</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Payment Info -->
        <div id="payment-info">
            <div class="totals-row">
                <span>Payment Method:</span>
                <span id="payment-method"></span>
            </div>
            <div id="cash-info" style="display:none;">
                <div class="totals-row">
                    <span>Cash Received:</span>
                    <span id="cash-received">$0.00</span>
                </div>
                <div class="totals-row bold">
                    <span>Change:</span>
                    <span id="change-amount">$0.00</span>
                </div>
            </div>
        </div>

        <div class="double-divider"></div>

        <!-- Barcode / Order Number -->
        <div class="barcode" id="barcode-display"></div>

        <!-- Footer -->
        <div class="footer">
            <div>Thank you for your purchase!</div>
            <div>Please come again</div>
            <div style="margin-top: 10px;">{{ config('app.url') }}</div>
        </div>

        <div class="divider"></div>

        <div class="text-center info-line">
            <div>** NO REFUNDS WITHOUT RECEIPT **</div>
            <div>Exchange within 7 days</div>
        </div>
    </div>

    <script>
        // Get order data from opener window
        const orderData = window.opener?.orderData || {
            items: [],
            customer_name: '',
            payment_method: 'cash',
            subtotal: 0,
            tax: 0,
            total: 0,
            cash_received: 0,
            change: 0
        };

        // Generate receipt number
        const receiptNumber = 'POS' + Date.now().toString().slice(-8);
        
        // Populate receipt
        document.getElementById('receipt-date').textContent = new Date().toLocaleString();
        document.getElementById('receipt-number').textContent = receiptNumber;
        
        if (orderData.customer_name) {
            document.getElementById('customer-line').style.display = 'block';
            document.getElementById('customer-name').textContent = orderData.customer_name;
        }

        // Populate items
        const itemsBody = document.getElementById('items-body');
        orderData.items.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="item-name">${item.name}</td>
                <td class="item-qty">${item.quantity}</td>
                <td class="item-price">$${parseFloat(item.price).toFixed(2)}</td>
                <td class="item-total">$${(item.price * item.quantity).toFixed(2)}</td>
            `;
            itemsBody.appendChild(row);
        });

        // Populate totals
        document.getElementById('subtotal-amount').textContent = '$' + orderData.subtotal.toFixed(2);
        document.getElementById('tax-amount').textContent = '$' + orderData.tax.toFixed(2);
        document.getElementById('total-amount').textContent = '$' + orderData.total.toFixed(2);

        // Payment method
        document.getElementById('payment-method').textContent = orderData.payment_method.toUpperCase();
        
        if (orderData.payment_method === 'cash') {
            document.getElementById('cash-info').style.display = 'block';
            document.getElementById('cash-received').textContent = '$' + orderData.cash_received.toFixed(2);
            document.getElementById('change-amount').textContent = '$' + orderData.change.toFixed(2);
        }

        // Barcode display (simplified)
        document.getElementById('barcode-display').textContent = receiptNumber;

        // Auto-print on load (optional)
        // window.onload = () => window.print();
    </script>
</body>
</html>
