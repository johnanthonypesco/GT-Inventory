<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            background: #fff;
            padding: 30px;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 22px;
            color: #005382;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn {
            background: #15ABFF;
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: inline-block;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0c87d8;
        }

        .footer {
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        {{-- <!-- ✅ Logo (Replace with actual logo image) -->
        <img src="{{ asset('image/Group 41.png') }}" alt="Logo" class="logo"> --}}

        <h1>Password Reset Request</h1>

        <p>
            You are receiving this email because we received a password reset request for your account.
        </p>

        <!-- ✅ Call-to-Action Button -->
        <a href="{{ $resetUrl }}" class="btn">Reset Password</a>

        <p>
            If you did not request a password reset, no further action is required.
        </p>

        {{-- <p class="footer">
            Need help? <a href="mailto:support@yourcompany.com">Contact Support</a>
        </p> --}}
    </div>

</body>
</html>
