{{-- prettier-ignore --}}
@component('mail::message')

# Password Reset Request

Hello {{ $data['name'] ?? 'User' }},

We received a request to reset your password for your **{{ config('app.name') }}** account.
If you initiated this request, click the button below to create a new password:

@component('mail::button', [
    'url' => route('reset_password.index', ['token' => $data['token'], 'email' => $data['email']]),
    'color' => 'success',
])
    Reset Password
@endcomponent

---

### Security Notice
- This link will expire in **{{ $data['expired_minutes'] }} minutes**.
- If you did not request a password reset, please ignore this email or contact our support team immediately.

Thank you for using **{{ config('app.name') }}**!
Stay secure and have a great day.

Regards,
**The {{ config('app.name') }} Team**
@endcomponent
