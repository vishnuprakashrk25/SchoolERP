<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ !empty($isWelcome) ? 'Welcome - Set Up Password' : 'Reset Password' }} - {{ $schoolName ?? 'YESINDIA Portal' }}</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      width: 100% !important;
      background-color: #ffffff;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #0f172a;
      -webkit-font-smoothing: antialiased;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      text-align: left;
    }

    table {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
      text-align: left;
    }

    img {
      border: 0;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    .wrapper {
      width: 100%;
      background-color: #ffffff;
      padding: 24px 16px;
      margin: 0;
    }

    .email-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      text-align: left;
    }

    .header-img {
      width: 100% !important;
      max-width: 600px !important;
      height: auto !important;
      display: block;
      margin: 0;
      border: 0;
    }

    .body-content {
      padding: 28px 24px 24px 24px;
      text-align: left;
    }

    .greeting {
      font-size: 16px;
      line-height: 1.5;
      font-weight: 600;
      color: #0f172a;
      margin: 0 0 16px 0;
      text-align: left;
    }

    .paragraph {
      font-size: 14px;
      line-height: 1.7;
      color: #334155;
      margin: 0 0 16px 0;
      text-align: left;
    }

    .otp-label {
      font-size: 14px;
      line-height: 1.7;
      color: #334155;
      margin: 20px 0 10px 0;
    }

    .otp-code {
      display: inline-block;
      padding: 12px 24px;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      background-color: #f8fafc;
      color: #0f172a;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 0.25em;
    }

    .otp-wrap {
      margin: 0 0 24px 0;
    }

    .notice {
      padding: 14px 16px;
      margin: 0 0 20px 0;
      border-left: 3px solid #cbd5e1;
      background-color: #f8fafc;
      color: #475569;
      font-size: 13px;
      line-height: 1.6;
    }

    .notice strong {
      color: #0f172a;
    }

    .signoff {
      font-size: 14px;
      line-height: 1.6;
      color: #334155;
      margin: 24px 0 28px 0;
      text-align: left;
    }

    .signoff strong {
      color: #0f172a;
    }

    .cta-container {
      margin: 0 0 24px 0;
      text-align: left;
    }

    .btn-primary {
      display: inline-block;
      background-color: #0C1A49;
      color: #ffffff !important;
      text-decoration: none;
      padding: 13px 28px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-align: center;
      mso-padding-alt: 0;
    }

    .fallback-link {
      margin: 0 0 24px 0;
      color: #475569;
      font-size: 12px;
      line-height: 1.6;
      overflow-wrap: anywhere;
      word-break: break-word;
    }

    .fallback-link a {
      color: #0C1A49;
      text-decoration: underline;
      overflow-wrap: anywhere;
      word-break: break-all;
    }

    .footer {
      padding: 20px 24px 32px 24px;
      border-top: 1px solid #f1f5f9;
      text-align: left;
      font-size: 11px;
      color: #94a3b8;
      line-height: 1.6;
    }

    @media only screen and (max-width: 600px) {
      .wrapper {
        padding: 0 !important;
      }

      .email-container {
        width: 100% !important;
        max-width: 100% !important;
      }

      .header-img {
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
      }

      .body-content {
        padding: 20px 20px 20px 20px !important;
      }

      .footer {
        padding: 18px 20px 16px 20px !important;
      }

      .btn-primary {
        display: block !important;
        width: auto !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        text-align: center !important;
        padding: 14px 20px !important;
        font-size: 14px !important;
      }
    }
  </style>
</head>

<body>
  <table role="presentation" class="wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding: 0;">
        <table role="presentation" class="email-container" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="padding: 0;">
              <!-- Header Banner Image -->
              <img src="{{ $headerImageUrl ?? asset('images/email-header.jpg') }}" alt="{{ $schoolName ?? 'YES INDIA FOUNDATION - Yaseen English School Maloora Portal' }}" class="header-img">

              <div class="body-content">
                <p class="greeting">Dear {{ $recipientName ?? 'User' }},</p>

                @if(!empty($isWelcome))
                  <p class="paragraph">
                    Welcome to the <strong>{{ $schoolName ?? 'YASEEN ENGLISH SCHOOL - MALOORA' }} ERP</strong>! Your account has been successfully created.
                  </p>
                  <p class="paragraph">
                    To activate your account and set up your personal login password, please click the button below:
                  </p>
                @else
                  <p class="paragraph">
                    We received a request to reset your password for your
                    <strong>{{ $schoolName ?? 'YASEEN ENGLISH SCHOOL - MALOORA' }} ERP</strong> account.
                  </p>
                @endif

                @if(!empty($code))
                  <div class="otp-wrap">
                    <div class="otp-label">Your One-Time Password (OTP) verification code:</div>
                    <div class="otp-code">{{ $code }}</div>
                  </div>
                @endif

                <div class="cta-container">
                  <a href="{{ $resetUrl }}" class="btn-primary" target="_blank" rel="noopener noreferrer">
                    {{ !empty($isWelcome) ? 'Set Up Password' : 'Reset Password' }}
                  </a>
                </div>

                <p class="paragraph">
                  This verification link is valid for <strong>{{ $expireMinutes ?? 60 }} minutes</strong>.
                </p>

                <div class="notice">
                  <strong>Security Notice:</strong>
                  For your security, do not share this OTP or link with anyone. School administration will never ask for your code.
                </div>

                @if(empty($isWelcome))
                  <p class="paragraph">
                    If you did not request a password reset, please ignore this email or notify the school IT helpdesk immediately.
                  </p>
                @endif

                <div class="signoff">
                  Warm regards,<br>
                  <strong>{{ $schoolName ?? 'YASEEN ENGLISH SCHOOL - MALOORA' }}</strong>
                </div>

                <div class="fallback-link">
                  If you're having trouble clicking the "{{ !empty($isWelcome) ? 'Set Up Password' : 'Reset Password' }}" button, copy and paste the URL below into your web browser:
                  <br>
                  <a href="{{ $resetUrl }}">
                    {{ $resetUrl }}
                  </a>
                </div>
              </div>

              <div class="footer">
                <p style="margin: 0 0 6px 0;">
                  © {{ date('Y') }} YES INDIA FOUNDATION. All rights reserved.
                </p>
                <p style="margin: 0;">
                  This is an automated administrative notification. Please do not reply directly to this email.
                </p>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>
