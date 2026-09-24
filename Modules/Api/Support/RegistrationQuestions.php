<?php

namespace Modules\Api\Support;

final class RegistrationQuestions
{
    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return [
            [
                'key' => 'weight_loss_medication',
                'type' => 'single_choice',
                'required' => true,
                'title' => 'آیا از داروهای کاهش وزن استفاده می‌کنی؟',
                'options' => [
                    ['value' => 0, 'title' => 'استفاده نمی‌کنم'],
                    ['value' => 1, 'title' => 'از داروهای کاهش وزن استفاده می‌کنم'],
                ],
            ],
            [
                'key' => 'food_budget',
                'type' => 'single_choice',
                'required' => true,
                'title' => 'دوست داری برنامه غذاییت از نظر هزینه چطور باشه؟',
                'options' => [
                    ['value' => 0, 'title' => 'اقتصادی و به‌صرفه'],
                    ['value' => 1, 'title' => 'متعادل و متنوع'],
                    ['value' => 2, 'title' => 'هزینه برام مهم نیست'],
                ],
            ],
            [
                'key' => 'weight_change_per_week',
                'type' => 'single_choice',
                'required' => true,
                'title' => 'دوست داری هر هفته چقدر وزنت تغییر کنه؟',
                'options' => [
                    [
                        'value' => 0,
                        'title' => 'استاندارد',
                        'subtitle' => 'حدود ۵۰۰ گرم در هفته',
                        'description' => 'آرام و قابل ادامه',
                    ],
                    [
                        'value' => 1,
                        'title' => 'سریع‌تر',
                        'subtitle' => '۵۰۰ گرم تا ۱ کیلو در هفته',
                        'description' => 'با سرعت بیشتری به هدفم برسم',
                    ],
                    [
                        'value' => 2,
                        'title' => 'خیلی سریع',
                        'subtitle' => 'بیشتر از ۱ کیلو در هفته',
                        'description' => 'می‌خوام زودتر نتیجه بگیرم',
                    ],
                ],
            ],
        ];
    }
}
