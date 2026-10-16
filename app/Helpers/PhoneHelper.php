<?php

namespace App\Helpers;

class PhoneHelper
{
    private static array $timezoneMap = [
        'Asia/Dhaka' => '880',
        'Asia/Kolkata' => '91',
        'Asia/Karachi' => '92',
        'Asia/Colombo' => '94',
        'Asia/Kathmandu' => '977',
        'Asia/Rangoon' => '95',
        'Asia/Bangkok' => '66',
        'Asia/Jakarta' => '62',
        'Asia/Manila' => '63',
        'Asia/Singapore' => '65',
        'Asia/Kuala_Lumpur' => '60',
        'Asia/Ho_Chi_Minh' => '84',
        'Asia/Shanghai' => '86',
        'Asia/Tokyo' => '81',
        'Asia/Seoul' => '82',
        'Asia/Dubai' => '971',
        'Asia/Riyadh' => '966',
        'Asia/Tehran' => '98',
        'Asia/Baghdad' => '964',
        'Europe/London' => '44',
        'Europe/Paris' => '33',
        'Europe/Berlin' => '49',
        'Europe/Moscow' => '7',
        'America/New_York' => '1',
        'America/Chicago' => '1',
        'America/Denver' => '1',
        'America/Los_Angeles' => '1',
        'Australia/Sydney' => '61',
        'Africa/Cairo' => '20',
        'Africa/Lagos' => '234',
        'Africa/Nairobi' => '254',
    ];

    public static function countryCodeFromTimezone(?string $timezone): string
    {
        if ($timezone && isset(self::$timezoneMap[$timezone])) {
            return self::$timezoneMap[$timezone];
        }

        return '880';
    }

    public static function whatsappLink(string $phone, ?string $timezone = null): string
    {
        $code = self::countryCodeFromTimezone($timezone);
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            $cleaned = substr($cleaned, 1);
        }

        return 'https://wa.me/'.$code.$cleaned;
    }
}
