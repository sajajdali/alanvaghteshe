<?php

namespace Modules\User\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\Admin\app\Models\WhatsappMessage;
use Modules\User\Enum\UserMetaEnum;

//this Trait is return value as a UerMetaEnum not string
trait MetaAttributeTrait
{

    public function genderMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::GENDER),
        );
    }

    public function newlyRegistered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::NEWLY_REGISTERED),
        );
    }

    public function popup(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMeta(UserMetaEnum::POPUP)?->meta_value,
            set: function ($value) {
                // Update the meta value
                $this->metas()->updateOrCreate(
                    ['user_id' => $this->id, 'meta_key' => UserMetaEnum::POPUP],
                    ['meta_value' => $value]
                );
            }
        );
    }


    public function weightMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::WEIGHT),
        );
    }

    public function targetPlanMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::TARGET_PLAN),
        );
    }
    public function dailyWaterConsumptionMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::DAILY_WATER_CONSUMPTION)
        );
    }
    public function dietTypeMeata(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMeta(UserMetaEnum::DIET_TYPE),
        );
    }
    public function weaknessesBodyMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::WEAKNESSES_BODY),
        );
    }

    public function habitsMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::HABITS),
        );
    }

    public function howMuchExperienceSportsMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::HOW_MUCH_EXPERIENCE_SPORTS)
        );
    }
    public function activityPerWeekMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::ACTIVITY_PER_WEEK),

        );
    }
    public function howManyDaysWeekExerciseMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::HOW_MANY_DAYS_WEEK_EXERCISE),
        );
    }
    public function tallMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::TALL),
        );
    }
    public function targetWeightMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::TARGET_WEIGHT),
        );
    }
    public function bodyFatMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::BODY_FAT),
        );
    }
    public function bodyPhysicalStyleMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::BODY_PHYSICAL_STYLE),
        );
    }
    public function typeDailyWorkMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::TYPE_DAILY_WORK),
        );
    }

    public function foodRestrictionMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::FOOD_RESTRICTION),

        );
    }

    public function wakeUpMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::WAKE_UP),
        );
    }

    public function targetOfExerciseMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::TARGET_OF_EXERCISE),
        );
    }
    public function dietPlanMeta(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMetas(UserMetaEnum::DIET_PLAN),
        );
    }



}
