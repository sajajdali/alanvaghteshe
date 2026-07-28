<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Modules\User\app\Notifications\UserMessageNotification;


function getRealIp()
{
    $ip = request()->header('X-Forwarded-For', request()->header('X-Real-Ip', request()->header('ar-real-ip')));
    $ipServer = request()->ip();
    return $ip == null ? $ipServer : $ip;
}
function convertToLatinNumbers($input)
{
    $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabicNumbers  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $latinNumbers   = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    return str_replace(array_merge($persianNumbers, $arabicNumbers), $latinNumbers, $input);
}
function replaceParams(string $template, array $params): string
{
    return preg_replace_callback('/%param(\d+)%/', function ($matches) use ($params) {
        $index = (int)$matches[1] - 1;
        return $params[$index] ?? $matches[0];
    }, $template);
}

function makeJsonFile($dietRequest)
{
    $result = app('dietService')->makeDietJsonFile($dietRequest);
    app('dietService')->createDietJsonFile($dietRequest , $result);

    //create pdf
    $user  = $dietRequest->user;
    $dietDetail = $result ;
    $mainFile = View::make('diet::exportDiet', compact('dietDetail', 'user', 'dietRequest'))->render();
    $fileName = 'diet-' . $dietRequest->id . '.html';
    $directory = '/tmp/pdf/';
    $filePath = $directory . $fileName;

    // Ensure the directory exists
    File::ensureDirectoryExists($directory, 0777, true);

    // Save the file and set permissions
    File::put($filePath, $mainFile);
//    File::chmod($filePath, 0777);
}
function sendNotification($user , $params = [] , $notification = null)
{
    if (empty($user->fcm_token)) {
        \Log::channel('fcm')->info('User has no FCM token', [
            'user_id' => $user->id,
            'email' => $user->email ?? null,
        ]);
        return;
    }
    $notification = replaceParams( $notification , $params);
    $user->notify(new UserMessageNotification(
        title: "پیام مهمی از الان وقتشه داریم",
        excerpt: $notification,
        message: $notification,
    ));
}

function getCurrentSeason()
{
    $month = \Carbon\Carbon::now()->format('n');

    switch ($month) {
        case 12:
        case 1:
        case 2:
            return 3;
        case 3:
        case 4:
        case 5:
            return 0;
        case 6:
        case 7:
        case 8:
            return 1;
        case 9:
        case 10:
        case 11:
            return 2;
        default:
            return 'Unknown';
    }
}
function getFileIconClass($mimeType) {
    return match(true) {
        str_contains($mimeType, 'png') => 'image',
        str_contains($mimeType, 'jpeg') => 'image',
        str_contains($mimeType, 'jpg') => 'image',
        str_contains($mimeType, 'webp') => 'image',
        str_contains($mimeType, 'heif') => 'image',
        str_contains($mimeType, 'pdf') => 'fa-file-pdf',
        str_contains($mimeType, 'word') => 'fa-file-word',
        str_contains($mimeType, 'excel') => 'fa-file-excel',
        str_contains($mimeType, 'text') => 'fa-file-alt',
        str_contains($mimeType, 'zip') => 'fa-file-archive',
        default => 'fa-file',
    };
}
if (!function_exists('formatDecimalArray')) {
    /**
     * Format all decimal values in an array to a specific precision.
     * Integer values remain unchanged.
     *
     * @param array $array The array to format.
     * @param int $precision The number of decimal places.
     * @return array The formatted array.
     */
    function formatDecimalArray(array $array, int $precision = 2): array
    {
        return array_map(function ($value) use ($precision) {
            if (is_array($value)) {
                // If the value is an array, process it recursively
                return formatDecimalArray($value, $precision);
            } elseif (is_numeric($value)) {
                // Check if the number is a decimal
                return floor($value) != $value
                    ? number_format((float)$value, $precision, '.', '') // Format decimal numbers
                    : $value; // Leave integers as they are
            }
            // Return the value as is for non-numeric values
            return $value;
        }, $array);
    }
}

function disableCache(): bool
{
    if (env('DISABLE_CACHE') && env('DISABLE_CACHE') === true){
        return true;
    }
    return false;
}
function validData($data){
    if (!isset($data)){
        return ' - ';
    }
    if ($data == 0 || $data == -1){
        return ' - ';
    }
    return $data;
}
function camelCaseToSpace($input)
{
    // Use a regular expression to insert a space before each uppercase letter
    $result = preg_replace('/(?<!^)([A-Z])/', ' $1', $input);

    // Convert the result to lowercase
    $result = strtolower($result);

    return $result;
}

function isValidJson($string)
{
    // تلاش برای تبدیل رشته به JSON
    json_decode($string);

    // بررسی خطا
    return (json_last_error() === JSON_ERROR_NONE);
}
function getPersianDate($date)
{
    return verta($date)->format("Y/m/d : h:i:s");
}
function getPersianDateSimple($date)
{
    return verta($date)->format("Y/m/d");
}
function getPersianDateFull($date)
{
    return verta($date)->format('l j F ساعت H:i:s');
}

function clcPerUnit($quantityPerUnit, $mainNutrition): int
{
    return (int) round(($mainNutrition * $quantityPerUnit) / 100);
}
