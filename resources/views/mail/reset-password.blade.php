@component('mail::message')

# @lang('Hello!')

@lang('You are receiving this email because we received a password reset request for your account.')

@component('mail::button', ['url' => route('password.reset', ['token' => $token])])
    @lang('Reset Password')
@endcomponent

@lang('Click above to reset your password. This password reset link will expire in :count minutes.', [
    'count' => config('auth.passwords.users.expire')
])


@lang('If you did not request a password reset, no further action is required.')


@lang('Thank You'),<br>
@lang('YourInspection.report')
<!-- {{ setting('app_name') }} -->

@endcomponent
