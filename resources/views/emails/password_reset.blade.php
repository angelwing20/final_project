{{-- prettier-ignore --}}
@component('mail::message')

# Password Reset Request

Hello {{ $data['name'] ?? 'User' }},

We received a request to reset your password for your **{{ config('app.name') }}** account.
If you initiated this request, click the button below to create a new password:

{{-- 按钮居中处理 --}}
<div style="text-align:center; margin: 20px 0;">
    <a href="{{ route('reset_password.index', ['token' => $data['token'], 'email' => $data['email']]) }}"
        style="display:inline-block;
              background-color:#28a745 !important;
              background-image:none !important;
              color:#ffffff !important;
              text-decoration:none;
              padding:12px 24px;
              border-radius:6px;
              font-weight:bold;
              font-size:16px;
              font-family:Arial, sans-serif;
              text-align:center;">
        Reset Password
    </a>
</div>

---

### Security Notice
- This link will expire in **{{ $data['expired_minutes'] }} minutes**.
- If you did not request a password reset, please ignore this email or contact our support team immediately.

Thank you for using **{{ config('app.name') }}**!
Stay secure and have a great day.

Regards,
**The {{ config('app.name') }} Team**

@endcomponent
