<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':Attribute alanı kabul edilmelidir.',
    'accepted_if' => ':Other :value olduğunda :attribute alanı kabul edilmelidir.',
    'active_url' => ':Attribute alanı geçerli bir URL olmalıdır.',
    'after' => ':Attribute alanı :date tarihinden sonra bir tarih olmalıdır.',
    'after_or_equal' => ':Attribute alanı :date tarihi veya sonrası bir tarih olmalıdır.',
    'alpha' => ':Attribute alanı yalnızca harf içerebilir.',
    'alpha_dash' => ':Attribute alanı yalnızca harf, rakam, tire ve alt çizgi içerebilir.',
    'alpha_num' => ':Attribute alanı yalnızca harf ve rakam içerebilir.',
    'any_of' => ':Attribute alanı geçersiz.',
    'array' => ':Attribute alanı bir dizi olmalıdır.',
    'array_keys' => ':Attribute alanı yalnızca şu anahtarları içerebilir: :values.',
    'ascii' => ':Attribute alanı yalnızca tek baytlık harf, rakam ve sembol içerebilir.',
    'base64' => ':Attribute alanı geçerli bir Base64 metni olmalıdır.',
    'before' => ':Attribute alanı :date tarihinden önce bir tarih olmalıdır.',
    'before_or_equal' => ':Attribute alanı :date tarihi veya öncesi bir tarih olmalıdır.',
    'between' => [
        'array' => ':Attribute alanı :min ile :max arasında öğe içermelidir.',
        'file' => ':Attribute alanı :min ile :max kilobayt arasında olmalıdır.',
        'numeric' => ':Attribute alanı :min ile :max arasında olmalıdır.',
        'string' => ':Attribute alanı :min ile :max karakter arasında olmalıdır.',
    ],
    'boolean' => ':Attribute alanı doğru veya yanlış olmalıdır.',
    'can' => ':Attribute alanı yetkisiz bir değer içeriyor.',
    'confirmed' => ':Attribute alanı onayı eşleşmiyor.',
    'contains' => ':Attribute alanında gerekli bir değer eksik.',
    'current_password' => 'Şifre hatalı.',
    'date' => ':Attribute alanı geçerli bir tarih olmalıdır.',
    'date_equals' => ':Attribute alanı :date tarihine eşit olmalıdır.',
    'date_format' => ':Attribute alanı :format biçimiyle eşleşmelidir.',
    'decimal' => ':Attribute alanı :decimal ondalık basamak içermelidir.',
    'declined' => ':Attribute alanı reddedilmelidir.',
    'declined_if' => ':Other :value olduğunda :attribute alanı reddedilmelidir.',
    'different' => ':Attribute alanı ile :other farklı olmalıdır.',
    'digits' => ':Attribute alanı :digits basamaklı olmalıdır.',
    'digits_between' => ':Attribute alanı :min ile :max basamak arasında olmalıdır.',
    'dimensions' => ':Attribute alanının görsel boyutları geçersiz.',
    'distinct' => ':Attribute alanında tekrarlanan bir değer var.',
    'doesnt_contain' => ':Attribute alanı şunlardan hiçbirini içermemelidir: :values.',
    'doesnt_end_with' => ':Attribute alanı şunlardan biriyle bitmemelidir: :values.',
    'doesnt_start_with' => ':Attribute alanı şunlardan biriyle başlamamalıdır: :values.',
    'email' => ':Attribute alanı geçerli bir e-posta adresi olmalıdır.',
    'encoding' => ':Attribute alanı :encoding ile kodlanmış olmalıdır.',
    'ends_with' => ':Attribute alanı şunlardan biriyle bitmelidir: :values.',
    'enum' => 'Seçilen :attribute geçersiz.',
    'exists' => 'Seçilen :attribute geçersiz.',
    'extensions' => ':Attribute alanı şu uzantılardan birine sahip olmalıdır: :values.',
    'file' => ':Attribute alanı bir dosya olmalıdır.',
    'filled' => ':Attribute alanı bir değer içermelidir.',
    'gt' => [
        'array' => ':Attribute alanı :value öğeden fazla içermelidir.',
        'file' => ':Attribute alanı :value kilobayttan büyük olmalıdır.',
        'numeric' => ':Attribute alanı :value değerinden büyük olmalıdır.',
        'string' => ':Attribute alanı :value karakterden uzun olmalıdır.',
    ],
    'gte' => [
        'array' => ':Attribute alanı en az :value öğe içermelidir.',
        'file' => ':Attribute alanı :value kilobayt veya daha büyük olmalıdır.',
        'numeric' => ':Attribute alanı :value veya daha büyük olmalıdır.',
        'string' => ':Attribute alanı en az :value karakter olmalıdır.',
    ],
    'hex_color' => ':Attribute alanı geçerli bir onaltılık renk olmalıdır.',
    'image' => ':Attribute alanı bir görsel olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'in_array' => ':Attribute alanı :other içinde bulunmalıdır.',
    'in_array_keys' => ':Attribute alanı şu anahtarlardan en az birini içermelidir: :values.',
    'integer' => ':Attribute alanı bir tam sayı olmalıdır.',
    'ip' => ':Attribute alanı geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':Attribute alanı geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':Attribute alanı geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':Attribute alanı geçerli bir JSON metni olmalıdır.',
    'list' => ':Attribute alanı bir liste olmalıdır.',
    'lowercase' => ':Attribute alanı küçük harf olmalıdır.',
    'lt' => [
        'array' => ':Attribute alanı :value öğeden az içermelidir.',
        'file' => ':Attribute alanı :value kilobayttan küçük olmalıdır.',
        'numeric' => ':Attribute alanı :value değerinden küçük olmalıdır.',
        'string' => ':Attribute alanı :value karakterden kısa olmalıdır.',
    ],
    'lte' => [
        'array' => ':Attribute alanı en fazla :value öğe içerebilir.',
        'file' => ':Attribute alanı :value kilobayt veya daha küçük olmalıdır.',
        'numeric' => ':Attribute alanı :value veya daha küçük olmalıdır.',
        'string' => ':Attribute alanı en fazla :value karakter olmalıdır.',
    ],
    'mac_address' => ':Attribute alanı geçerli bir MAC adresi olmalıdır.',
    'max' => [
        'array' => ':Attribute alanı en fazla :max öğe içerebilir.',
        'file' => ':Attribute alanı en fazla :max kilobayt olabilir.',
        'numeric' => ':Attribute alanı en fazla :max olabilir.',
        'string' => ':Attribute alanı en fazla :max karakter olabilir.',
    ],
    'max_digits' => ':Attribute alanı en fazla :max basamak içerebilir.',
    'mimes' => ':Attribute alanı şu türde bir dosya olmalıdır: :values.',
    'mimetypes' => ':Attribute alanı şu türde bir dosya olmalıdır: :values.',
    'min' => [
        'array' => ':Attribute alanı en az :min öğe içermelidir.',
        'file' => ':Attribute alanı en az :min kilobayt olmalıdır.',
        'numeric' => ':Attribute alanı en az :min olmalıdır.',
        'string' => ':Attribute alanı en az :min karakter olmalıdır.',
    ],
    'min_digits' => ':Attribute alanı en az :min basamak içermelidir.',
    'missing' => ':Attribute alanı bulunmamalıdır.',
    'missing_if' => ':Other :value olduğunda :attribute alanı bulunmamalıdır.',
    'missing_unless' => ':Other :value olmadıkça :attribute alanı bulunmamalıdır.',
    'missing_with' => ':Values mevcutken :attribute alanı bulunmamalıdır.',
    'missing_with_all' => ':Values mevcutken :attribute alanı bulunmamalıdır.',
    'multiple_of' => ':Attribute alanı :value değerinin katı olmalıdır.',
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':Attribute alanının biçimi geçersiz.',
    'numeric' => ':Attribute alanı bir sayı olmalıdır.',
    'password' => [
        'letters' => ':Attribute alanı en az bir harf içermelidir.',
        'mixed' => ':Attribute alanı en az bir büyük ve bir küçük harf içermelidir.',
        'numbers' => ':Attribute alanı en az bir rakam içermelidir.',
        'symbols' => ':Attribute alanı en az bir sembol içermelidir.',
        'uncompromised' => 'Girilen :attribute bir veri sızıntısında yer almış. Lütfen farklı bir :attribute seçin.',
    ],
    'present' => ':Attribute alanı mevcut olmalıdır.',
    'present_if' => ':Other :value olduğunda :attribute alanı mevcut olmalıdır.',
    'present_unless' => ':Other :value olmadıkça :attribute alanı mevcut olmalıdır.',
    'present_with' => ':Values mevcutken :attribute alanı da mevcut olmalıdır.',
    'present_with_all' => ':Values mevcutken :attribute alanı da mevcut olmalıdır.',
    'prohibited' => ':Attribute alanı kullanılamaz.',
    'prohibited_if' => ':Other :value olduğunda :attribute alanı kullanılamaz.',
    'prohibited_if_accepted' => ':Other kabul edildiğinde :attribute alanı kullanılamaz.',
    'prohibited_if_declined' => ':Other reddedildiğinde :attribute alanı kullanılamaz.',
    'prohibited_unless' => ':Other :values içinde olmadıkça :attribute alanı kullanılamaz.',
    'prohibits' => ':Attribute alanı :other alanının bulunmasını engeller.',
    'regex' => ':Attribute alanının biçimi geçersiz.',
    'required' => ':Attribute alanı zorunludur.',
    'required_array_keys' => ':Attribute alanı şunlar için değer içermelidir: :values.',
    'required_if' => ':Other :value olduğunda :attribute alanı zorunludur.',
    'required_if_accepted' => ':Other kabul edildiğinde :attribute alanı zorunludur.',
    'required_if_declined' => ':Other reddedildiğinde :attribute alanı zorunludur.',
    'required_unless' => ':Other :values içinde olmadıkça :attribute alanı zorunludur.',
    'required_with' => ':Values mevcutken :attribute alanı zorunludur.',
    'required_with_all' => ':Values mevcutken :attribute alanı zorunludur.',
    'required_without' => ':Values mevcut değilken :attribute alanı zorunludur.',
    'required_without_all' => ':Values alanlarından hiçbiri mevcut değilken :attribute alanı zorunludur.',
    'same' => ':Attribute alanı :other ile eşleşmelidir.',
    'size' => [
        'array' => ':Attribute alanı :size öğe içermelidir.',
        'file' => ':Attribute alanı :size kilobayt olmalıdır.',
        'numeric' => ':Attribute alanı :size olmalıdır.',
        'string' => ':Attribute alanı :size karakter olmalıdır.',
    ],
    'starts_with' => ':Attribute alanı şunlardan biriyle başlamalıdır: :values.',
    'string' => ':Attribute alanı bir metin olmalıdır.',
    'timezone' => ':Attribute alanı geçerli bir saat dilimi olmalıdır.',
    'unique' => ':Attribute zaten kullanılıyor.',
    'uploaded' => ':Attribute yüklenemedi.',
    'uppercase' => ':Attribute alanı büyük harf olmalıdır.',
    'url' => ':Attribute alanı geçerli bir URL olmalıdır.',
    'ulid' => ':Attribute alanı geçerli bir ULID olmalıdır.',
    'uuid' => ':Attribute alanı geçerli bir UUID olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our messages more expressive.
    |
    */

    'attributes' => [
        'address_line1' => 'adres',
        'address_line2' => 'adres (devam)',
        'city' => 'İl',
        'code' => 'kod',
        'contact_email' => 'İletişim e-postası',
        'current_password' => 'mevcut şifre',
        'email' => 'e-posta',
        'established_year' => 'kuruluş yılı',
        'industry' => 'sektör',
        'legal_name' => 'ticari unvan',
        'locale' => 'dil',
        'name' => 'ad',
        'password' => 'şifre',
        'password_confirmation' => 'şifre onayı',
        'phone' => 'telefon',
        'postal_code' => 'posta kodu',
        'recovery_code' => 'kurtarma kodu',
        'region' => 'İlçe',
        'tax_number' => 'vergi numarası',
        'tax_office' => 'vergi dairesi',
        'trade_registry_number' => 'ticaret sicil numarası',
        'whatsapp_phone' => 'WhatsApp',
    ],

];
