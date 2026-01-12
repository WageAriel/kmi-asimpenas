<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // List domain email yang umum dan valid
        $validDomains = [
            'gmail.com',
            'yahoo.com',
            'yahoo.co.id',
            'hotmail.com',
            'outlook.com',
            'live.com',
            'icloud.com',
            'mail.com',
            'aol.com',
            'protonmail.com',
            'zoho.com',
            'yandex.com',
            'gmx.com',
            'tutanota.com',
        ];

        // Tambahkan domain perusahaan Indonesia yang umum
        $indonesianDomains = [
            'co.id',
            'id',
            'or.id',
            'ac.id',
            'go.id',
            'net.id',
            'web.id',
        ];

        // Extract domain dari email
        $emailParts = explode('@', $value);
        if (count($emailParts) !== 2) {
            $fail('Format email tidak valid.');
            return;
        }

        $domain = strtolower($emailParts[1]);
        
        // List typo umum untuk Gmail
        $typoGmail = [
            'gamail.com',
            'gmai.com',
            'gmial.com',
            'gmaill.com',
            'gmil.com',
            'gmal.com',
            'gmaik.com',
            'gnail.com',
            'gamil.com',
        ];

        // List typo umum untuk Yahoo
        $typoYahoo = [
            'yaho.com',
            'yahooo.com',
            'yhoo.com',
            'yahho.com',
            'yaoo.com',
        ];

        // Cek jika ada typo Gmail
        if (in_array($domain, $typoGmail)) {
            $fail('Sepertinya Anda salah ketik. Maksud Anda "gmail.com"?');
            return;
        }

        // Cek jika ada typo Yahoo
        if (in_array($domain, $typoYahoo)) {
            $fail('Sepertinya Anda salah ketik. Maksud Anda "yahoo.com"?');
            return;
        }

        // Cek apakah domain termasuk dalam list valid
        $isValidDomain = false;
        
        // Cek exact match dengan domain populer
        if (in_array($domain, $validDomains)) {
            $isValidDomain = true;
        }
        
        // Cek domain Indonesia (ends with)
        foreach ($indonesianDomains as $indoDomain) {
            if (str_ends_with($domain, '.' . $indoDomain) || $domain === $indoDomain) {
                $isValidDomain = true;
                break;
            }
        }

        // Validasi DNS record (opsional, untuk domain korporat)
        if (!$isValidDomain) {
            // Cek apakah domain memiliki MX record (mail server)
            if (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A')) {
                $isValidDomain = true;
            }
        }

        if (!$isValidDomain) {
            $fail('Domain email "' . $domain . '" tidak valid atau tidak dikenali. Silakan gunakan email yang valid seperti Gmail, Yahoo, atau email perusahaan yang terdaftar.');
        }
    }
}
