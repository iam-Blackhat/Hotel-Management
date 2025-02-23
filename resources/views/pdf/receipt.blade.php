<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .container { width: 180px; margin: auto; text-align: center; } /* 58mm width */
        .header { font-size: 12px; font-weight: bold; }
        .items { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .items td { padding: 2px; }
        .total { font-size: 12px; font-weight: bold; margin-top: 5px; }
        .footer { font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p>Street Food Stall</p>
            <p>Order Receipt</p>
            <hr>
        </div>

        <table class="items">
            <tbody>
                @foreach($order['items'] as $item)
                    <tr>
                        <td>{{$item['index']}}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>x{{ $item['quantity'] }}</td>
                        <td>${{ number_format($item['price'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">Total: ${{ number_format($order['total'], 2) }}</p>

        <div class="footer">
            <p>Thank you for your order!</p>
        </div>
    </div>
</body>
</html>
