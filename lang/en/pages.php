<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Page Language Lines
    |--------------------------------------------------------------------------
    |
    | The home page, the authentication pages (login, registration, password
    | reset, email verification, two-factor challenge), the dashboard and the
    | settings pages. Laravel's own login messages stay in auth.php, which
    | "php artisan lang:publish --force" overwrites; Fortify's messages and
    | the error page titles are translated in lang/{locale}.json.
    |
    */

    'welcome' => [
        'tagline' => 'Create, edit and publish your company website from one dashboard.',
    ],

    'authentication' => [
        'login' => [
            'title' => 'Log in to your account',
            'description' => 'Enter your email and password below to log in',
            'forgot_password' => 'Forgot your password?',
            'remember_me' => 'Remember me',
            'no_account' => 'Don\'t have an account?',
            'sign_up' => 'Sign up',
        ],

        'register' => [
            'title' => 'Create an account',
            'description' => 'Enter your details below to create your account',
            'full_name' => 'Full name',
            'submit' => 'Create account',
            'has_account' => 'Already have an account?',
        ],

        'forgot_password' => [
            'title' => 'Forgot password',
            'description' => 'Enter your email to receive a password reset link',
            'submit' => 'Email password reset link',
            'return_to' => 'Or, return to',
            'log_in' => 'log in',
            'link_sent' => 'If an account exists for that email address, a password reset link has been sent.',
        ],

        'reset_password' => [
            'title' => 'Reset password',
            'description' => 'Please enter your new password below',
        ],

        'confirm_password' => [
            'description' => 'This is a secure area of the application. Please confirm your password before continuing.',
        ],

        'verify_email' => [
            'title' => 'Email verification',
            'description' => 'Please verify your email address by clicking on the link we just emailed to you.',
            'link_sent' => 'A new verification link has been sent to the email address you provided during registration.',
            'resend' => 'Resend verification email',
        ],

        'two_factor_challenge' => [
            'authentication_code' => 'Authentication code',
            'authentication_code_description' => 'Enter the authentication code provided by your authenticator application.',
            'recovery_code' => 'Recovery code',
            'recovery_code_description' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
            'or_you_can' => 'or you can',
            'use_recovery_code' => 'login using a recovery code',
            'use_authentication_code' => 'login using an authentication code',
        ],
    ],

    'company' => [
        'errors' => [
            'industry_unavailable' => 'This industry is no longer available. Please choose another one.',
            'already_in_company' => 'You already belong to a company.',
        ],
    ],

    'dashboard' => [
        'welcome' => 'Welcome, :name.',
        'description' => 'Your companies and their websites will be managed here.',
    ],

    'settings' => [
        'description' => 'Manage your profile and account settings',

        'profile' => [
            'page_title' => 'Profile settings',
            'title' => 'Profile',
            'description' => 'Update your name and email address',
            'updated' => 'Profile updated.',
            'email_unverified' => 'Your email address is unverified.',
            'resend_verification' => 'Click here to re-send the verification email.',
            'verification_sent' => 'A new verification link has been sent to your email address.',
        ],

        'delete_account' => [
            'title' => 'Delete account',
            'description' => 'Delete your account and all of its resources',
            'confirm_title' => 'Are you sure you want to delete your account?',
            'confirm_description' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
        ],

        'security' => [
            'page_title' => 'Security settings',
            'title' => 'Security',
            'update_password' => 'Update password',
            'update_password_description' => 'Ensure your account is using a long, random password to stay secure',
            'current_password' => 'Current password',
            'new_password' => 'New password',
            'password_updated' => 'Password updated.',
        ],

        'two_factor' => [
            'description' => 'Manage your two-factor authentication settings',
            'enabled_description' => 'You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
            'disabled_description' => 'When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.',
            'enable' => 'Enable 2FA',
            'disable' => 'Disable 2FA',
            'enabled_title' => 'Two-factor authentication enabled',
            'enabled_message' => 'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
            'verify_title' => 'Verify authentication code',
            'verify_description' => 'Enter the 6-digit code from your authenticator app.',
            'setup_title' => 'Enable two-factor authentication',
            'setup_description' => 'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.',
            'manual_entry' => 'or, enter the code manually',
        ],

        'recovery_codes' => [
            'title' => '2FA recovery codes',
            'description' => 'Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.',
            'view' => 'View recovery codes',
            'hide' => 'Hide recovery codes',
            'regenerate' => 'Regenerate codes',
            'list_label' => 'Recovery codes',
            'usage' => 'Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate codes above.',
        ],

        'appearance' => [
            'page_title' => 'Appearance settings',
            'title' => 'Appearance',
            'description' => 'Update the appearance settings for your account',
            'light' => 'Light',
            'dark' => 'Dark',
            'system' => 'System',
        ],
    ],

];
