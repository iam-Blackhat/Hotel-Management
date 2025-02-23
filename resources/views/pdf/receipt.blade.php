<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .container { width: 180px; margin: auto; text-align: center; padding: 5px; }
        .header { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .header img { max-width: 50px; margin-bottom: 5px; } /* Add a logo if needed */
        .header p { margin: 2px 0; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .items { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .items td { padding: 2px; }
        .items .item-name { text-align: left; }
        .items .item-qty { text-align: center; }
        .items .item-price { text-align: right; }
        .total { font-size: 12px; font-weight: bold; margin-top: 5px; text-align: right; }
        .footer { font-size: 8px; margin-top: 10px; line-height: 1.2; }
        .footer .highlight { font-weight: bold; }
        .barcode { margin-top: 10px; } /* Optional: Add a barcode for order tracking */
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <!-- Add a logo if available -->
            <!-- <img src="logo.png" alt="Street Food Stall Logo"> -->
            <p>Street Food Stall</p>
            <p>Order Receipt</p>
            <p><strong>Date:</strong> {{ $order['date'] }} | <strong>Time:</strong> {{ $order['time'] }}</p>
            <p><strong>Order ID:</strong> #{{ $order['id'] }}</p>
            <div class="divider"></div>
        </div>

        <!-- Items Table -->
        <table class="items">
            <tbody>
                @foreach($order['items'] as $item)
                    <tr>
                        <td class="item-name">{{$item['index']}}. {{ $item['name'] }}</td>
                        <td class="item-qty">x{{ $item['quantity'] }}</td>
                        <td class="item-price">${{ number_format($item['price'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="divider"></div>

        <!-- Total Section -->
        <p class="total">Total: ${{ number_format($order['total'], 2) }}</p>
        <div class="divider"></div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Thank you for dining with us!</p>
            <p>Visit us again for more delicious treats.</p>
            <p class="highlight">Follow us on social media:</p>
            <p>Instagram: @streetfoodstall</p>
            <p>Facebook: /streetfoodstall</p>
            <p>Contact: +123 456 7890</p>
            <p>Address: 123 Food Street, City, Country</p>
            <!-- Optional: Add a barcode for order tracking -->
            <div class="barcode">
                <img src="barcode.png" alt="Order Barcode" style="max-width: 100%;">
            </div>
        </div>
    </div>
</body>
</html>
