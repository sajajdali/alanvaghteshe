<?php

namespace Modules\Diet\app\Models;

use App\Events\RefreshDashboardEvent;
use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\Meal;
use Modules\User\Entities\User;

class FoodConsumption extends Model
{
    use HasFactory;

    protected $casts = [
        'consumed_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    protected static function clearUserCacheAndRefreshDashboard($foodConsumption)
    {
        $user = $foodConsumption->user;
        $endDate = Carbon::today();
        $cacheKey = 'user_food_consumption_week_' . $user->id . '_' . $endDate->toDateString();
        $cacheTag = 'user_' . $user->id;

        Cache::tags($cacheTag)->forget($cacheKey);
        event(new RefreshDashboardEvent($user));
    }

    public static function boot()
    {
        parent::boot();

        static::created(fn($foodConsumption) => self::clearUserCacheAndRefreshDashboard($foodConsumption));
        static::deleted(fn($foodConsumption) => self::clearUserCacheAndRefreshDashboard($foodConsumption));
        static::updated(fn($foodConsumption) => self::clearUserCacheAndRefreshDashboard($foodConsumption));
    }

    public function consumable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function meal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(FoodUnit::class , 'food_unit_id');
    }

}
