<?php

namespace App\Services;

final class PhoneNormalizer
{
    /** @return array{msisdn98:string, local:string, chatId:string} */
    public static function toIran98(string $input): array
    {
        $digits = preg_replace('/\D+/', '', (string) $input);

        // پوشش +98 و 0098 هم
        if (str_starts_with($digits, '0098'))      $digits = substr($digits, 4);
        if (str_starts_with($digits, '098'))       $digits = substr($digits, 3);
        if (str_starts_with($digits, '98'))        $msisdn98 = $digits;
        elseif (str_starts_with($digits, '0'))     $msisdn98 = '98'.ltrim($digits, '0');
        elseif (str_starts_with($digits, '9'))     $msisdn98 = '98'.$digits;
        else                                       $msisdn98 = $digits; // fallback

        $local  = '0'.ltrim(substr($msisdn98, 2), '0');
        $chatId = $msisdn98.'@c.us';

        return [$msisdn98, $local, $chatId];
    }
}
