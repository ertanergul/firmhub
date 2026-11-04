<?php

use App\Models\Company;

test('contact emails are stored in lower case', function () {
    $company = Company::factory()->create(['contact_email' => 'Hello@Acme.TEST']);

    expect($company->refresh()->contact_email)->toBe('hello@acme.test');
});
