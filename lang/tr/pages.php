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
        'tagline' => 'Şirketinizin web sitesini tek bir panelden oluşturun, düzenleyin ve yayınlayın.',
    ],

    'authentication' => [
        'login' => [
            'title' => 'Hesabınıza giriş yapın',
            'description' => 'Giriş yapmak için e-posta adresinizi ve şifrenizi girin',
            'forgot_password' => 'Şifrenizi mi unuttunuz?',
            'remember_me' => 'Beni hatırla',
            'no_account' => 'Hesabınız yok mu?',
            'sign_up' => 'Kayıt ol',
        ],

        'register' => [
            'title' => 'Hesap oluşturun',
            'description' => 'Hesabınızı oluşturmak için bilgilerinizi girin',
            'full_name' => 'Ad soyad',
            'submit' => 'Hesap oluştur',
            'has_account' => 'Zaten hesabınız var mı?',
        ],

        'forgot_password' => [
            'title' => 'Şifremi unuttum',
            'description' => 'Şifre sıfırlama bağlantısı almak için e-posta adresinizi girin',
            'submit' => 'Şifre sıfırlama bağlantısı gönder',
            'return_to' => 'Ya da şuraya dönün:',
            'log_in' => 'giriş yap',
            'link_sent' => 'Bu e-posta adresine ait bir hesap varsa şifre sıfırlama bağlantısı gönderildi.',
        ],

        'reset_password' => [
            'title' => 'Şifreyi sıfırla',
            'description' => 'Lütfen yeni şifrenizi girin',
        ],

        'confirm_password' => [
            'description' => 'Burası uygulamanın güvenli bir alanı. Devam etmeden önce lütfen şifrenizi onaylayın.',
        ],

        'verify_email' => [
            'title' => 'E-posta doğrulama',
            'description' => 'Lütfen size e-posta ile gönderdiğimiz bağlantıya tıklayarak e-posta adresinizi doğrulayın.',
            'link_sent' => 'Kayıt sırasında verdiğiniz e-posta adresine yeni bir doğrulama bağlantısı gönderildi.',
            'resend' => 'Doğrulama e-postasını yeniden gönder',
        ],

        'two_factor_challenge' => [
            'authentication_code' => 'Doğrulama kodu',
            'authentication_code_description' => 'Doğrulama uygulamanızın verdiği kodu girin.',
            'recovery_code' => 'Kurtarma kodu',
            'recovery_code_description' => 'Kurtarma kodlarınızdan birini girerek hesabınıza erişiminizi onaylayın.',
            'or_you_can' => 'veya',
            'use_recovery_code' => 'kurtarma koduyla giriş yapın',
            'use_authentication_code' => 'doğrulama koduyla giriş yapın',
        ],
    ],

    'company' => [
        'errors' => [
            'industry_unavailable' => 'Bu sektör artık kullanılmıyor. Lütfen başka bir sektör seçin.',
            'already_in_company' => 'Zaten bir şirkete üyesiniz.',
        ],
    ],

    'dashboard' => [
        'welcome' => 'Hoş geldiniz, :name.',
        'description' => 'Şirketleriniz ve web siteleri burada yönetilecek.',
    ],

    'settings' => [
        'description' => 'Profilinizi ve hesap ayarlarınızı yönetin',

        'profile' => [
            'page_title' => 'Profil ayarları',
            'title' => 'Profil',
            'description' => 'Adınızı ve e-posta adresinizi güncelleyin',
            'updated' => 'Profil güncellendi.',
            'email_unverified' => 'E-posta adresiniz doğrulanmadı.',
            'resend_verification' => 'Doğrulama e-postasını yeniden göndermek için buraya tıklayın.',
            'verification_sent' => 'E-posta adresinize yeni bir doğrulama bağlantısı gönderildi.',
        ],

        'delete_account' => [
            'title' => 'Hesabı sil',
            'description' => 'Hesabınızı ve tüm verilerini silin',
            'confirm_title' => 'Hesabınızı silmek istediğinizden emin misiniz?',
            'confirm_description' => 'Hesabınız silindiğinde tüm verileri kalıcı olarak silinir. Hesabınızı kalıcı olarak silmek istediğinizi onaylamak için şifrenizi girin.',
        ],

        'security' => [
            'page_title' => 'Güvenlik ayarları',
            'title' => 'Güvenlik',
            'update_password' => 'Şifreyi güncelle',
            'update_password_description' => 'Hesabınızın güvenliği için uzun ve rastgele bir şifre kullandığınızdan emin olun',
            'current_password' => 'Mevcut şifre',
            'new_password' => 'Yeni şifre',
            'password_updated' => 'Şifre güncellendi.',
        ],

        'two_factor' => [
            'description' => 'İki adımlı doğrulama ayarlarınızı yönetin',
            'enabled_description' => 'Girişte sizden güvenli ve rastgele bir kod istenecek; bu kodu telefonunuzdaki TOTP destekli uygulamadan alabilirsiniz.',
            'disabled_description' => 'İki adımlı doğrulamayı açtığınızda girişte sizden güvenli bir kod istenir. Bu kodu telefonunuzdaki TOTP destekli bir uygulamadan alabilirsiniz.',
            'enable' => '2FA\'yı aç',
            'disable' => '2FA\'yı kapat',
            'enabled_title' => 'İki adımlı doğrulama açıldı',
            'enabled_message' => 'İki adımlı doğrulama açıldı. QR kodu tarayın veya kurulum anahtarını doğrulama uygulamanıza girin.',
            'verify_title' => 'Doğrulama kodunu onayla',
            'verify_description' => 'Doğrulama uygulamanızdaki 6 haneli kodu girin.',
            'setup_title' => 'İki adımlı doğrulamayı açın',
            'setup_description' => 'İki adımlı doğrulamayı açmayı tamamlamak için QR kodu tarayın veya kurulum anahtarını doğrulama uygulamanıza girin.',
            'manual_entry' => 'ya da kodu elle girin',
        ],

        'recovery_codes' => [
            'title' => '2FA kurtarma kodları',
            'description' => 'Kurtarma kodları, 2FA cihazınızı kaybederseniz hesabınıza yeniden erişmenizi sağlar. Bunları güvenli bir şifre yöneticisinde saklayın.',
            'view' => 'Kurtarma kodlarını göster',
            'hide' => 'Kurtarma kodlarını gizle',
            'regenerate' => 'Kodları yenile',
            'list_label' => 'Kurtarma kodları',
            'usage' => 'Her kurtarma kodu hesabınıza erişmek için bir kez kullanılabilir ve kullanıldıktan sonra silinir. Daha fazlasına ihtiyacınız olursa yukarıdaki Kodları yenile düğmesine tıklayın.',
        ],

        'appearance' => [
            'page_title' => 'Görünüm ayarları',
            'title' => 'Görünüm',
            'description' => 'Hesabınızın görünüm ayarlarını güncelleyin',
            'light' => 'Açık',
            'dark' => 'Koyu',
            'system' => 'Sistem',
        ],
    ],

];
