<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your New Account</title>
    <style>
        /* NOTE: Most email clients ignore styles in <head>. 
          For best results, you need to inline all CSS styles directly 
          onto the HTML elements (e.g., <p style="color: #333;">). 
          There are tools that can do this for you automatically.
        */
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            max-width: 600px;
            margin: 20px auto;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our RMPOIMS</h1>
        
        <p>Hello {{ $user->name ?? $user->username ?? $user->staff_username }},</p>

        <p>An account has been created for you. Here are your login details:</p>

        <ul>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Password:</strong> {{ $password }}</li>
        </ul>

        <p>Please log in using the link below and change your password in your profile settings.</p>

        <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>

        <p>Thanks,<br>
        RMPOIMS Admin</p>
    </div>
</body>
</html>