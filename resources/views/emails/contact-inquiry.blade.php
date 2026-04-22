<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Inquiry</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 32px; }
        .label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 16px; margin-bottom: 20px; }
        .message-box { background: #f9fafb; border-left: 4px solid #2563eb; padding: 16px; border-radius: 4px; }
        .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-top:0;">New Contact Inquiry</h2>

        <div class="label">From</div>
        <div class="value">{{ $senderName }} &lt;{{ $senderEmail }}&gt;</div>

        <div class="label">Subject</div>
        <div class="value">{{ $subject }}</div>

        <div class="label">Message</div>
        <div class="message-box">
            {!! nl2br(e($messageBody)) !!}
        </div>

        <div class="footer">
            This email was sent from the contact form on {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
