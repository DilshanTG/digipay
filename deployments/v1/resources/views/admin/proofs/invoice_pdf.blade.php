<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $payment->order_id }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 12px; 
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .container {
            padding: 40px;
        }
        .header-bar {
            height: 8px;
            background-color: #0f172a;
            width: 100%;
        }
        .logo-section {
            margin-bottom: 40px;
            display: table;
            width: 100%;
        }
        .logo-cell {
            display: table-cell;
            vertical-align: top;
        }
        .company-info {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0;
            letter-spacing: -1px;
        }
        .invoice-number {
            font-size: 14px;
            color: #64748b;
            margin-top: 5px;
            font-weight: bold;
        }
        .address-box {
            margin-bottom: 40px;
            display: table;
            width: 100%;
        }
        .address-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .value {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
        }
        .sub-value {
            font-size: 12px;
            color: #475569;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table.items-table th {
            background-color: #f8fafc;
            padding: 12px 15px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        table.items-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .item-description {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .item-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        .totals-section {
            display: table;
            width: 100%;
        }
        .totals-spacer {
            display: table-cell;
            width: 60%;
        }
        .totals-content {
            display: table-cell;
            width: 40%;
        }
        .total-row {
            padding: 8px 0;
            display: table;
            width: 100%;
        }
        .total-label {
            display: table-cell;
            text-align: left;
            color: #64748b;
            font-weight: bold;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }
        .grand-total-box {
            background-color: #0f172a;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }
        .grand-total-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .grand-total-value {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            margin-top: 5px;
        }
        .footer-note {
            margin-top: 60px;
            padding: 25px;
            background-color: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .footer-note-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #0f172a;
        }
        .footer-note-text {
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
        .thanks {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="header-bar"></div>
    <div class="container">
        <!-- Header -->
        <div class="logo-section">
            <div class="logo-cell">
                <img src="{{ public_path('logo.png') }}" alt="DigiMart" style="height: 40px; margin-bottom: 10px;">
                <div style="font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 2px;">DIGIMART SOLUTIONS</div>
                <div style="color: #64748b; font-size: 10px; font-weight: bold; text-transform: uppercase;">(Pvt) Ltd - PV00336398</div>
                <div style="color: #64748b; font-size: 11px; margin-top: 5px;">
                    288, Boathura,<br>
                    Gampola, Sri Lanka.
                </div>
            </div>
            <div class="company-info">
                <h1 class="invoice-title">Invoice</h1>
                <div class="invoice-number">#{{ $payment->order_id }}</div>
                <div style="margin-top: 20px;">
                    <div class="label">Issue Date</div>
                    <div class="value">{{ $payment->created_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid #f1f5f9; margin-bottom: 40px;"></div>

        <!-- Addressing -->
        <div class="address-box">
            <div class="address-cell">
                <div class="label">Billed To</div>
                <div class="value">{{ $payment->customer_name }}</div>
                <div class="sub-value">{{ $payment->customer_email }}</div>
                <div class="value" style="margin-top: 5px;">{{ $payment->customer_phone ?? '' }}</div>
            </div>
            <div class="address-cell" style="text-align: right;">
                <div class="label">Contact Support</div>
                <div class="value">info@digimartsolutions.lk</div>
                <div class="sub-value">+94 77 250 3124</div>
                <div class="sub-value">www.digimartsolutions.lk</div>
            </div>
        </div>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Description</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 20%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-description">{{ $description }}</div>
                        <div class="item-sub" style="font-weight: bold; color: #64748b; margin-top: 5px;">{{ $subDescription }}</div>
                        <div class="item-sub" style="margin-top: 8px;">Ref ID: {{ $payment->order_id }} | PayHere Ref: {{ $payment->payhere_ref ?? 'N/A' }}</div>
                    </td>
                    <td style="text-align: center; font-weight: bold;">1</td>
                    <td style="text-align: right; font-weight: bold; font-size: 14px;">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-spacer"></div>
            <div class="totals-content">
                <div class="total-row">
                    <div class="total-label">Subtotal</div>
                    <div class="total-value">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Tax (0%)</div>
                    <div class="total-value">0.00</div>
                </div>
                
                <div class="grand-total-box">
                    <div class="grand-total-label">Grand Total</div>
                    <div class="grand-total-value">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div class="footer-note">
            <div class="footer-note-title">Payment Confirmation</div>
            <div class="footer-note-text">
                This document serves as an official confirmation of payment for services rendered by Digimart Solutions (Pvt) Ltd. 
                The transaction was completed securely and verified via PayHere. 
                For any inquiries regarding this document or the associated service, please contact our support team quoting the Invoice Number.
            </div>
        </div>

        <div class="thanks">
            Thank you for your business with DigiMart Solutions
        </div>
    </div>
</body>
</html>
