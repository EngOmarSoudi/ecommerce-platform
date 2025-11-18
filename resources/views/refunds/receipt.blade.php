<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Receipt - {{ $refund->refund_number ?? 'REF-' . $refund->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            padding: 20mm;
            max-width: 210mm;
            margin: 0 auto;
        }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }

        @page {
            size: A4;
            margin: 20mm;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            margin: 20px 0 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .info-box {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }

        .info-box-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #374151;
            font-size: 13px;
        }

        .info-line {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #d1d5db;
            font-weight: bold;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .totals-row.grand-total {
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #dc2626;
        }

        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
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

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Receipt</button>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ config('app.name', 'E-Commerce Platform') }}</div>
        <div>123 Main Street, City, State 12345</div>
        <div>Tel: (555) 123-4567 | Email: info@example.com</div>
    </div>

    <div class="document-title text-center">REFUND RECEIPT</div>

    <!-- Refund & Order Info -->
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">Refund Information</div>
            <div class="info-line"><strong>Refund ID:</strong> {{ $refund->refund_number ?? 'REF-' . $refund->id }}</div>
            <div class="info-line"><strong>Date Issued:</strong> {{ $refund->created_at->format('F d, Y') }}</div>
            <div class="info-line"><strong>Time:</strong> {{ $refund->created_at->format('h:i A') }}</div>
            <div class="info-line">
                <strong>Status:</strong> 
                <span class="status-badge status-{{ $refund->status }}">{{ ucfirst($refund->status) }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title">Original Order</div>
            <div class="info-line"><strong>Order Number:</strong> #{{ $refund->order->order_number }}</div>
            <div class="info-line"><strong>Order Date:</strong> {{ $refund->order->created_at->format('M d, Y') }}</div>
            <div class="info-line"><strong>Original Amount:</strong> ${{ number_format($refund->order->total_amount, 2) }}</div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="info-box">
        <div class="info-box-title">Customer Information</div>
        <div class="info-line"><strong>Name:</strong> {{ $refund->order->customer_name ?? 'N/A' }}</div>
        <div class="info-line"><strong>Email:</strong> {{ $refund->order->customer_email ?? 'N/A' }}</div>
        @if($refund->order->customer_phone)
        <div class="info-line"><strong>Phone:</strong> {{ $refund->order->customer_phone }}</div>
        @endif
    </div>

    <!-- Refund Details -->
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Refund for Order #{{ $refund->order->order_number }}</strong>
                    @if($refund->reason)
                    <br><small>Reason: {{ $refund->reason }}</small>
                    @endif
                </td>
                <td class="text-right">${{ number_format($refund->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Refund Summary -->
    <div class="totals">
        <div class="totals-row">
            <span>Refund Amount:</span>
            <span>${{ number_format($refund->amount, 2) }}</span>
        </div>
        @if($refund->processing_fee > 0)
        <div class="totals-row">
            <span>Processing Fee:</span>
            <span>-${{ number_format($refund->processing_fee, 2) }}</span>
        </div>
        @endif
        <div class="totals-row grand-total">
            <span>Total Refunded:</span>
            <span>${{ number_format($refund->amount - ($refund->processing_fee ?? 0), 2) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <!-- Refund Method -->
    <div class="info-box" style="margin-top: 30px;">
        <div class="info-box-title">Refund Method</div>
        <div class="info-line">
            <strong>Method:</strong> 
            {{ ucfirst($refund->refund_method ?? 'Original Payment Method') }}
        </div>
        @if($refund->refund_method === 'original')
        <div class="info-line">
            <small>The refund will be credited to the original payment method used for this order.</small>
        </div>
        @endif
        <div class="info-line">
            <strong>Processing Time:</strong> 
            <small>Refunds typically take 5-10 business days to appear in your account.</small>
        </div>
    </div>

    <!-- Additional Notes -->
    @if($refund->notes)
    <div class="info-box" style="margin-top: 20px;">
        <div class="info-box-title">Additional Notes</div>
        <div>{{ $refund->notes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Important Information:</strong></p>
        <p>This is an official refund receipt. Please keep it for your records.</p>
        <p>For questions about this refund, please contact us at support@{{ config('app.url') }}</p>
        <p style="margin-top: 10px;">{{ config('app.name') }} | {{ config('app.url') }}</p>
        <p>Processed on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
