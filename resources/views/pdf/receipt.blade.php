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

        /* Move logo to top center */
        .logo {
            text-align: center;
        }

        .logo img {
            max-width: 50px;
            height: auto;
        }

        .shop-name {
            font-weight: bold;
            font-size: 12px;
            margin-top: 3px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
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

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }


        th, td {
            padding: 3px;
            font-size: 10px;
            text-align: left;
            page-break-inside: avoid;
        }

        .items tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: auto;
        }

        .items thead {
            display: table-header-group;
        }

        .items tbody {
            display: table-row-group;
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
        .receipt, .no-break, .items tbody tr, .header, .footer, .total {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* QR Code */
        .qr-code {
            text-align: center;
            margin-top: 5px;
        }

        .qr-code img {
            width: 60px;
            height: 60px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="container">
            <!-- Logo & Shop Name -->
            <div class="logo">
                <img src="{{ public_path('assets/image.jpeg') }}" alt="logo">
            </div>
            <div class="shop-name">
                D'square Food Cart
            </div>

            <!-- Header Section -->
            <div class="header">
                <p><strong>Ph. No:</strong> 9514779494</p>
                <p>Address - B122 cheran managar, 4th bus stop, Vilankurchi post, Coimbatore - 641035</p>
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
                    <tr style="border-bottom:1px solid grey">
                        <th >Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order['items'] as $item)
                        <tr>
                            <td >{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['price'], 2) }}</td>
                            <td>{{ number_format($item['quantity'] * $item['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="divider"></div>

            <!-- Total Section -->
            <div class="total">
                <p>Total Amount: Rs.{{ number_format($order['total'], 2) }}</p>
            </div>

            <div class="divider"></div>

            <!-- Footer -->
            <div class="footer">
                <p class="thank-you">! Thank you! Visit Again! :)</p>
                <p class="thank-you">Double the Delight, only @ D<sup>2</sup></p>
                <p>For inquiries, call +91-9514779494</p>
                <p>Follow us on Insta: <span style="font-style:italic;">d2_delight_s</span></p>
            </div>

            <!-- QR Code -->
            <div class="qr-code">
                <img src="{{ public_path('assets/image.jpeg') }}" alt="Instagram QR">
            </div>
        </div>
    </div>
</body>

</html>

