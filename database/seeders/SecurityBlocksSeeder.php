<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockedCountry;
use App\Models\BlockedEmailDomain;

class SecurityBlocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة دول محظورة
        $blockedCountries = [
            ['country_code' => 'IR'], // إيران
            ['country_code' => 'KP'], // كوريا الشمالية
            ['country_code' => 'SY'], // سوريا
            ['country_code' => 'CU'], // كوبا
            ['country_code' => 'VE'], // فنزويلا
            ['country_code' => 'RU'], // روسيا (مثال)
        ];

        foreach ($blockedCountries as $country) {
            BlockedCountry::firstOrCreate($country);
        }

        // إضافة دومينات إيميل محظورة (إيميلات مؤقتة)
        $blockedDomains = [
            ['domain' => '10minutemail.com'],
            ['domain' => 'temp-mail.org'],
            ['domain' => 'guerrillamail.com'],
            ['domain' => 'mailinator.com'],
            ['domain' => 'throwaway.email'],
            ['domain' => 'yopmail.com'],
            ['domain' => 'maildrop.cc'],
            ['domain' => 'tempail.com'],
            ['domain' => 'dispostable.com'],
            ['domain' => 'getnada.com'],
        ];

        foreach ($blockedDomains as $domain) {
            BlockedEmailDomain::firstOrCreate($domain);
        }
    }
}
