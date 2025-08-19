<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            color: #005382;
            font-size: 22px;
            font-weight: bold;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #15ABFF;
            margin: 15px 0;
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            background: #f4f4f4;
        }
        .footer {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <p class="header">Two-Factor Authentication (2FA) Code</p>
        <p>Use the following code to complete your login:</p>
        <span class="code">{{ implode(' ', str_split($code)) }}</span>
        <p class="header" style="font-size: 17px"> (╯°□°）╯︵ ┻━┻ </p>
        <p>This code will expire in <strong>10 minutes</strong>.</p>
        <p class="footer">If you didn't request this code, please ignore this email.</p>
        <p class="footer">Thank you,<br> RMPOIMS</p>
    </div>
</body>
</html>
