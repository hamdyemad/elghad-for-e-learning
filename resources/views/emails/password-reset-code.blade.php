<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            background-color: #f46a6a;
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .code-box {
            background-color: #f8f9fa;
            border: 2px dashed #f46a6a;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #f46a6a;
            letter-spacing: 8px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello {{ $name ?? 'User' }},</p>
            <p>We received a request to reset your password. Use the following code to set a new password:</p>

            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>

            <div class="warning">
                <strong>Important:</strong> This code will expire in 15 minutes. Do not share this code with anyone.
            </div>

            <p>If you didn't request a password reset, please ignore this email.</p>

            <p>Best regards,<br>{{ config('app.name', 'Laravel') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
