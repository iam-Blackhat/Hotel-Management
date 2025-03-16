<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>

        @font-face {
        font-family: 'DejaVuSans';
        src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
    }

        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'DejaVuSans', monospace, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 58mm;
            margin: 0 auto;
            padding: 5px;
            text-align: center;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .header img {
            max-width: 60px;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: avoid;
        }

        th,
        td {
            padding: 3px;
            font-size: 10px;
            text-align: center;
            page-break-inside: avoid;
        }

        .items td.item-name {
            text-align: left;
            word-wrap: break-word;
            max-width: 70px;
        }

        .orderhead {
            font-weight: bold;
            text-decoration: underline;
            text-align: left;
            margin-top: 5px;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
            text-align: right;
        }

        .footer {
            font-size: 10px;
            margin-top: 10px;
        }

        .footer p {
            margin: 3px 0;
        }

        .thank-you {
            font-weight: bold;
            font-size: 12px;
        }

        /* Prevent breaking */
        .no-break,
        .items tbody tr,
        .header,
        .footer,
        .total {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="no-break">
        <div class="container">
            <!-- Header Section -->
            <div class="header">
                <img src="{{ public_path('assets/image.jpeg') }}" alt="logo">
                <div>
                    <p><strong>Ph. No:</strong> 0987654321</p>
                    <p>Lorem ipsum dolor sit amet consectetur.</p>
                </div>
            </div>

            <!-- Order Details -->
            <p class="orderhead">Order Details</p>
            <table>
                <thead>
                    <tr>
                        <th>Order No.</th>
                        <th>Issued Date</th>
                        <th>Payment Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $order['id'] }}</td>
                        <td>{{ $order['date'] }}, {{ $order['time'] }}</td>
                        <td>{{ $order['payment_type'] }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="divider"></div>

            <!-- Items Table -->
            <table class="items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order['items'] as $item)
                        <tr>
                            <td class="item-name">{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>&#8377;{{ number_format($item['price'], 2) }}</td>
                            <td>&#8377;{{ number_format($item['quantity'] * $item['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="divider"></div>

            <!-- Total Section -->
            <div class="total">
                <p>Total Amount: {{ number_format($order['total'], 2) }}</p>
            </div>

            <div class="divider"></div>

            <!-- Footer -->
            <div class="footer">
                <p class="thank-you">😎 Thank you! Visit Again! 👋</p>
                <p>For inquiries, call +91 0987654321</p>
                <p>Follow us: @insta_foods</p>
            </div>
        </div>
    </div>
</body>

</html>
