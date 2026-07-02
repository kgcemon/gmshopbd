<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your PIN Codes</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">
<div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 20px;">
    <h2 style="text-align: center; color: #4CAF50;">🎉 Your PIN Codes</h2>
    <p style="font-size: 16px; color: #333;">Hello <b>{{ $customerName }}</b>,</p>
    <p style="font-size: 15px; color: #555;">Thank you for your purchase. Below you will find your PIN codes:</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <thead>
        <tr style="background: #4CAF50; color: #fff; text-align: left;">
            <th style="padding: 10px;">#</th>
            <th style="padding: 10px;">PIN Code</th>
            <th style="padding: 10px;">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pins as $index => $item)
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">{{ $index + 1 }}</td>
                <td style="padding: 10px; color: #e63946;"><b>{{ $item['pin'] }}</b></td>
                <td style="padding: 10px;">{{ $item['name'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p style="font-size: 14px; color: #666;">Please keep these PINs safe. If you face any issue, contact our support team.</p>
    <hr>
    <p style="font-size: 13px; color: #999; text-align: center;">© {{ $year }} Gaming Shop Lite. All rights reserved.</p>
</div>
</body>
</html>
