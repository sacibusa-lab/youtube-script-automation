<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Verify your email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            text-align: center;
        }
        .logo {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -1px;
            color: #0d9488; /* teal-600 */
            margin-bottom: 30px;
            text-decoration: none;
            display: inline-block;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 16px;
            color: #1e293b;
        }
        p {
            font-size: 16px;
            margin-bottom: 24px;
            color: #475569;
        }
        .otp-box {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .otp-code {
            font-family: monospace;
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #0f172a;
            margin: 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="{{ config('app.url') }}" class="logo">
                {{ config('app.name', 'StoryBee') }}
            </a>
            
            <h1>Verify your email address</h1>
            <p>Hi {{ $userName }}, please use the following confirmation code to verify your account. It will expire in 15 minutes.</p>
            
            <div class="otp-box">
                <p class="otp-code">{{ $otpCode }}</p>
            </div>
            
            <p>If you didn't create an account, you can safely ignore this email.</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            If you're having trouble with the code above, contact support.
        </div>
    </div>
</body>
</html>
