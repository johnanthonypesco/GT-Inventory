<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message Notification</title>
    <style>
        /* Basic responsive styles */
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="border-collapse: collapse; background-color: #ffffff; border: 1px solid #dddddd;">
                    <tr>
                        <td style="padding: 40px 30px 30px 30px;">
                            <h1 style="font-size: 24px; color: #333333; margin: 0;">
                                Hello!
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <p style="font-size: 16px; color: #555555; margin: 0;">
                                You have received a new message from <strong>{{ $senderName}}</strong>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="background-color: #f9f9f9; border-left: 5px solid #007bff; padding: 20px;">
                                        <p style="font-size: 16px; color: #555555; margin: 0; font-style: italic;">
                                            {{ $messageContent }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <p style="font-size: 16px; color: #555555; margin: 0 0 20px 0;">
                                You can view the full conversation by logging into the portal.
                            </p>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="border-radius: 5px; background-color: #007bff;">
                                        <a href="{{ url('/') }}" target="_blank" style="font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; display: inline-block; padding: 15px 25px; border-radius: 5px;">
                                            Go to Chat
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px;">
                            <p style="font-size: 16px; color: #555555; margin: 0;">
                                Thanks,<br>
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>