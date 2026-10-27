<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #0d6efd; font-size: 32px; }
        .header-info { text-align: right; }
        .details { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #f8f9fa; }
        .table .text-end { text-align: right; }
        .totals { width: 50%; float: right; border-collapse: collapse; }
        .totals td { padding: 8px 12px; }
        .totals .fw-bold { font-weight: bold; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>HEXASHOP</h1>
                <p>123 Business Road, Tech Park<br>Mumbai, Maharashtra, India<br>support@hexashop.com</p>
            </div>
            <div class="header-info">
                <h2 style="margin:0;">INVOICE</h2>
                <p>Order #: <strong>{{ $order->order_number }}</strong><br>
                Date: {{ date('d M, Y', strtotime($order->created_at)) }}<br>
                Status: <span style="text-transform:uppercase">{{ $order->payment_status }}</span></p>
            </div>
        </div>

        <div class="details">
            <div>
                <strong>Billed To:</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->customer_phone }}<br>
                {{ $order->email }}
            </div>
            @php $addr = json_decode($order->shipping_address); @endphp
            @if($addr)
            <div style="text-align: right;">
                <strong>Shipped To:</strong><br>
                {{ $addr->full_name }}<br>
                {{ $addr->address_line1 }}, {{ $addr->address_line2 }}<br>
                {{ $addr->city }}, {{ $addr->state }} {{ $addr->pincode }}
            </div>
            @endif
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->product_name }}<br><small style="color:#777">Code: {{ $item->product_code }}</small></td>
                    <td>₹{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-end">₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td class="text-end">Subtotal:</td>
                <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->coupon_discount > 0)
            <tr>
                <td class="text-end" style="color: green;">Discount:</td>
                <td class="text-end" style="color: green;">-₹{{ number_format($order->coupon_discount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="text-end">Shipping:</td>
                <td class="text-end">₹{{ number_format($order->shipping_charge, 2) }}</td>
            </tr>
            <tr style="background:#f8f9fa;">
                <td class="text-end fw-bold" style="font-size: 18px;">Grand Total:</td>
                <td class="text-end fw-bold" style="font-size: 18px; color: #0d6efd;">₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>
        <div class="clearfix"></div>

        <div class="footer">
            <p>Thank you for shopping with DPShop!</p>
            <p>This is a computer-generated invoice and does not require a physical signature.</p>
        </div>
    </div>
    
    <script>
        // Auto print when opened
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
