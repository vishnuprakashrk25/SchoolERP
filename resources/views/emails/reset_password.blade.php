<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{ $isWelcome ?? false ? 'Welcome - Set Up Password' : 'Reset Password' }} - {{ $schoolName }}</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f3f4f8;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #1e293b;
    }
    .wrapper {
      width: 100%;
      background-color: #f3f4f8;
      padding: 32px 16px;
    }
    .container {
      max-width: 620px;
      margin: 0 auto;
    }
    .card {
      background-color: #ffffff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      padding: 36px 32px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .header-logo {
      text-align: center;
      margin-bottom: 24px;
    }
    .header-logo img {
      height: 48px;
      max-height: 48px;
      width: auto;
      object-fit: contain;
      display: inline-block;
    }
    .sub-header {
      font-size: 13px;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-top: 6px;
      text-align: center;
    }
    h2 {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 12px 0;
    }
    p {
      font-size: 15px;
      line-height: 1.6;
      color: #475569;
      margin: 0 0 16px 0;
    }
    .otp-box {
      display: inline-block;
      background-color: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 12px;
      padding: 12px 28px;
      margin: 8px 0 20px 0;
      letter-spacing: 0.25em;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      font-size: 30px;
      font-weight: 800;
      color: #0f172a;
    }
    .btn-purple {
      display: inline-block;
      background-color: #5e5ce6;
      color: #ffffff !important;
      font-weight: 600;
      font-size: 16px;
      padding: 14px 34px;
      border-radius: 11px;
      text-decoration: none;
      box-shadow: 0 2px 4px rgba(94, 92, 230, 0.2);
      margin: 10px 0 22px 0;
    }
    .notice-row {
      font-size: 14px;
      color: #475569;
      margin-bottom: 16px;
    }
    .security-box {
      background-color: #fffbeb;
      border: 1px solid #fde68a;
      border-radius: 12px;
      padding: 14px 16px;
      font-size: 13px;
      color: #78350f;
      line-height: 1.5;
      margin: 18px 0;
    }
    .security-box strong {
      color: #451a03;
    }
    .footer-links {
      border-top: 1px solid #e2e8f0;
      padding-top: 18px;
      margin-top: 24px;
      font-size: 12px;
      color: #64748b;
      word-break: break-all;
    }
    .footer-links a {
      color: #4f46e5;
      text-decoration: underline;
      display: block;
      margin-top: 6px;
      font-family: ui-monospace, SFMono-Regular, monospace;
      font-size: 11px;
    }
    .outer-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 12px;
      color: #94a3b8;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      
      <!-- Brand Header -->
      <div class="header-logo">
        <img src="{{ $logoUrl }}" alt="{{ $schoolName }}">
        <div class="sub-header">{{ $schoolName }} Portal</div>
      </div>

      <!-- Main Card -->
      <div class="card">
        <h2>Dear {{ $recipientName }},</h2>

        @if(!empty($isWelcome))
          <p>
            Welcome to the <strong>{{ $schoolName }} Portal</strong>! Your account has been successfully created.
          </p>
          <p>
            To activate your account and set up your personal login password, please use the following 6-digit One-Time Password (OTP) or click the button below:
          </p>
        @else
          <p>
            We received a request to reset your password for your <strong>{{ $schoolName }} Portal</strong> account.
          </p>
          <p>
            Please use the following 6-digit One-Time Password (OTP) to verify your request:
          </p>
        @endif

        <!-- OTP Box -->
        <div style="text-align: left;">
          <div class="otp-box">{{ $code }}</div>
        </div>

        <!-- Action CTA Button -->
        <div>
          <a class="btn-purple" href="{{ $resetUrl }}" target="_blank" rel="noopener noreferrer">
            {{ !empty($isWelcome) ? 'Set Up Password' : 'Reset Password' }}
          </a>
        </div>

        <!-- Expiration Notice -->
        <div class="notice-row">
          <span>⏱️ This verification code is valid for <strong>{{ $expireMinutes }} minutes</strong>.</span>
        </div>

        <!-- Security Advisory Card -->
        <div class="security-box">
          <strong>Security Notice:</strong>
          For your security, do not share this OTP with anyone. School administration will never ask for your code.
        </div>

        @if(empty($isWelcome))
          <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
            If you did not request a password reset, please ignore this email or notify the school IT helpdesk immediately.
          </p>
        @endif

        <p style="margin-bottom: 4px;">Warm regards,</p>
        <p style="font-weight: 600; color: #0f172a; margin-top: 0;">
          {{ $schoolName }} Portal Support
        </p>

        <!-- Direct Link Fallback -->
        <div class="footer-links">
          <p style="margin-bottom: 4px; color: #64748b;">
            If you're having trouble clicking the "{{ !empty($isWelcome) ? 'Set Up Password' : 'Reset Password' }}" button, copy and paste the URL below into your web browser:
          </p>
          <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
        </div>
      </div>

      <!-- Outer Footer -->
      <div class="outer-footer">
        <p>© {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
        <p>This is an automated administrative notification. Please do not reply directly to this email.</p>
      </div>

    </div>
  </div>
</body>
</html>
