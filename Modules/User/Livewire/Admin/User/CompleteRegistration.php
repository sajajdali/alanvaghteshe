<?php

namespace Modules\User\Livewire\Admin\User;

use Livewire\Component;
use Modules\Diet\Service\DietService;
use Modules\User\Entities\User;
use Modules\User\Entities\UserMeta;
use Modules\User\Enum\UserMetaEnum;

class CompleteRegistration extends Component
{
    public User $user;

    public array $form = [
        'first_name' => '',
        'last_name' => '',
        'diet_type' => null,
        'gender' => null,
        'athlete_or_not' => null,
        'activity_per_week' => 1,
        'food_restriction' => [],
        'birthday' => ['year' => null, 'month' => null, 'day' => null],
        'food_allergy' => [],
        'diseases' => [],
        'tall' => null,
        'weight' => null,
        'target_weight' => null,
        'weight_change_per_week' => 1,
        'invitation_code' => '',
        'target_plan' => 10, // پیشفرض
        'action' => 1,
    ];

    // گزینه‌ها (برای UI)
    public array $dietTypeOptions = [
        0 => 'کاهش وزن',
        1 => 'افزایش وزن',
        2 => 'تثبیت وزن',
    ];

    public array $genderOptions = [
        0 => 'مرد',
        1 => 'زن',
    ];

    public array $athleteOptions = [
        0 => 'ورزشکار نیستم',
        1 => 'ورزشکار هستم',
    ];

    public array $activityOptions = [
        0 => 'فعالیت فیزیکی بسیار کم',
        1 => 'فعالیت فیزیکی کم',
        2 => 'فعالیت فیزیکی متوسط',
        3 => 'فعالیت فیزیکی زیاد',
        4 => 'فعالیت فیزیکی بسیار زیاد',
    ];

    public array $weightChangeOptions = [
        0 => 'کم',
        1 => 'متوسط',
        2 => 'زیاد',
    ];

    public array $targetPlanOptions = [
        10 => 'رژیم',
        20 => 'ورزش',
        30 => 'ورزش و رژیم',
    ];

    // توجه: لیست شما تکراری داشت؛ اینجا Unique و مرتبش کردم
    public array $foodRestrictionOptions = [
        0 => 'وگن',
        1 => 'گیاه‌خوار',
        2 => 'گوشت‌خوار',
        3 => 'همه‌چیزخوار',
        4 => 'بدون ماهی',
        5 => 'بدون لاکتوز',
        6 => 'کتوژنیک',
    ];

    public array $foodAllergyOptions = [
        0 => 'ماهی و میگو',
        1 => 'سویا',
        2 => 'باقالا',
        3 => 'بادمجان',
        4 => 'گلوتن',
        5 => 'شیر',
        6 => 'تخم‌مرغ',
        7 => 'بادام زمینی',
        8 => 'ذرت',
    ];

    // این باید از سرور بیاد (فعلاً نمونه/placeholder)
    public array $diseaseOptions = [
        1 => 'دیابت',
        2 => 'فشار خون',
        3 => 'تیروئید',
        4 => 'کبد چرب',
        5 => 'کلسترول',
    ];

    private function handleBirthdayParts(): array
    {
        $raw = $this->user->birthday ?? null;

        if (!$raw) {
            return ['year' => null, 'month' => null, 'day' => null];
        }

        $data = is_array($raw) ? $raw : json_decode($raw, true);

        if (!is_array($data)) {
            return ['year' => null, 'month' => null, 'day' => null];
        }

        return [
            'year'  => isset($data['year']) ? (int)$data['year'] : null,
            'month' => isset($data['month']) ? (int)$data['month'] : null,
            'day'   => isset($data['day']) ? (int)$data['day'] : null,
        ];
    }

    private function normalizeMetaValue(string $fieldKey, ?string $value)
    {
        if ($value === null) return null;

        $v = trim($value);

        // بعضی فیلدها عددی هستن
        $castIntFields = [
            'diet_type', 'gender', 'athlete_or_not', 'activity_per_week',
            'weight_change_per_week', 'target_plan', 'action',
        ];

        // اگه json باشه (birthday / arrays)
        if ($v !== '' && ($v[0] === '{' || $v[0] === '[')) {
            $decoded = json_decode($v, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // birthday در DB به شکل {"year":...,"month":...,"day":...} ذخیره شده
                if ($fieldKey === 'birthday') {
                    return [
                        'year'  => (int)($decoded['year'] ?? null),
                        'month' => (int)($decoded['month'] ?? null),
                        'day'   => (int)($decoded['day'] ?? null),
                    ];
                }
                // سایر آرایه‌ها مثل food_restriction/food_allergy
                return $decoded;
            }
        }

        if (in_array($fieldKey, $castIntFields, true)) {
            return $v === '' ? null : (int)$v;
        }

        // float ها
        $castFloatFields = ['weight', 'target_weight', 'tall'];
        if (in_array($fieldKey, $castFloatFields, true)) {
            return $v === '' ? null : (float)$v;
        }

        return $v;
    }

    private function loadFormFromMetas(): void
    {
        // تمام متاها: meta_key(int enum) => meta_value(string)
        $metas = $this->user->metas()
            ->select(['meta_key', 'meta_value'])
            ->get();

        $filled = [];

        foreach ($metas as $meta) {
            // meta_key عدد است => enum
            $enum = UserMetaEnum::tryFrom((int)$meta->meta_key->value);
            if (!$enum) continue;

            // تبدیل به key فرم: diet_type, food_allergy, ...
            $fieldKey = strtolower($enum->name);

            // فقط اگر داخل فرم داریم پر کن
            if (!array_key_exists($fieldKey, $this->form)) {
                continue;
            }

            $filled[$fieldKey] = $this->normalizeMetaValue($fieldKey, $meta->meta_value);
        }

        // diseases از pivot
        $filled['diseases'] = $this->user->diseases()->pluck('id')->map(fn($id)=> (int)$id)->values()->all();

        // merge امن
        $this->form = array_replace_recursive($this->form, $filled);

        // اطمینان از نوع آرایه‌ها برای checkbox ها
        $this->form['food_restriction'] = array_values(array_map('intval', (array)($this->form['food_restriction'] ?? [])));
        $this->form['food_allergy']     = array_values(array_map('intval', (array)($this->form['food_allergy'] ?? [])));
        $this->form['diseases']         = array_values(array_map('intval', (array)($this->form['diseases'] ?? [])));

        // birthday همیشه آرایه بمونه
        if (!is_array($this->form['birthday'] ?? null)) {
            $this->form['birthday'] = ['year' => null, 'month' => null, 'day' => null];
        }
    }

    public function mount()
    {
        $userParam = request()->route('user');
        $this->user = $userParam instanceof User ? $userParam : User::findOrFail($userParam);

        // 1) اول: همه چیز از metas + diseases بیاد
        $this->loadFormFromMetas();

        // 2) اگر ستون‌های مستقیم user هم داری و می‌خوای اولویت داشته باشن:
        $this->form['first_name'] = $this->user->first_name ?? $this->form['first_name'] ?? '';
        $this->form['last_name']  = $this->user->last_name  ?? $this->form['last_name'] ?? '';
        $this->form['gender']     = $this->user->gender     ?? $this->form['gender'] ?? null;
    }

    public function submit()
    {
        $user = $this->user;

        /** -------------------------
         * 1) payload
         * --------------------------*/
        $payload = $this->form;

        $payload['birthday'] = [
            (int)($this->form['birthday']['year'] ?? 0),
            (int)($this->form['birthday']['month'] ?? 0),
            (int)($this->form['birthday']['day'] ?? 0),
        ];


        /** -------------------------
         * 3) diseases (pivot)
         * --------------------------*/
        if (!empty($payload['diseases']) && is_array($payload['diseases'])) {
            $user->diseases()->sync($payload['diseases']);
        }
        unset($payload['diseases']);

        /** -------------------------
         * 4) target_plan
         * --------------------------*/
        $payload['target_plan'] = 10;

        /** -------------------------
         * 5) metas
         * --------------------------*/
        $listMetas = [];

        foreach (UserMetaEnum::keys() as $key) {

            $fieldKey = strtolower($key->name);

            if (!array_key_exists($fieldKey, $payload)) {
                continue;
            }

            $value = $payload[$fieldKey];

            // birthday / arrays → json
            if (is_array($value) || is_object($value)) {
                if ($fieldKey === 'birthday') {
                    $value = [
                        'year'  => (int)($value[0] ?? 0),
                        'month' => (int)($value[1] ?? 0),
                        'day'   => (int)($value[2] ?? 0),
                    ];
                }
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            if ($fieldKey === 'athlete_or_not') {
                $value = (int)$value;
            }


            $listMetas[] = new UserMeta([
                'meta_key'   => $key->value,
                'meta_value' => $value,
            ]);
        }



        if (count($listMetas)) {
            $user->metas()->saveMany($listMetas);
        }

        /** -------------------------
         * 6) پیام موفقیت
         * --------------------------*/
        session()->flash('success', 'اطلاعات با موفقیت ذخیره شد');

         return redirect()->route('admin.user.index');
    }

    public function render()
    {
        return view('user::livewire.admin.user.complete-registration');
    }
}
