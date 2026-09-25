<?php

namespace Modules\User\Entities;

use App\UserSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Admin\app\Models\ActivityLog;
use Modules\Admin\app\Models\WhatsappMessage;
use Modules\Diet\app\Models\FoodConsumption;
use Modules\Diet\app\Models\WaterConsumption;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Package\Entities\PackageUser;
use Modules\Course\app\Models\CourseUser;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\Reminder\app\Traits\HasReminderDispatch;
use Modules\Transaction\Entities\Transaction;
use Modules\User\app\Models\InviteFriend;
use Modules\User\app\Models\Message;
use Modules\User\app\Models\WalletTransaction;
use Modules\User\Database\factories\UserFactory;
use Modules\User\Enum\UserMetaEnum;
use Modules\User\Traits\MetaAttributeTrait;
use Modules\User\Traits\UserAttributeTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Verta;

/**
 * Modules\User\Entities\User
 *
 * @property int $id
 * @property string|null $mobile
 * @property string|null $email
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietRequest> $dietRequests
 * @property-read int|null $diet_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Core\Entities\Disease> $diseases
 * @property-read int|null $diseases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\ExercisePlanRequest> $exercisePlanRequests
 * @property-read int|null $exercise_plan_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\User\Entities\UserMeta> $metas
 * @property-read int|null $metas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $my
 * @property-read int|null $my_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $supporter
 * @property-read int|null $supporter_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Api\Entities\UserDevice> $userDevices
 * @property-read int|null $user_devices_count
 * @method static \Modules\User\Database\factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @property int $wallet
 * @property mixed $activity_per_week
 * @property-read mixed $activity_per_week_meta
 * @property string|null $avatar
 * @property mixed $birthday
 * @property mixed $body_fat
 * @property-read mixed $body_fat_meta
 * @property mixed $body_physical_style
 * @property-read mixed $body_physical_style_meta
 * @property mixed $body_style
 * @property-read Collection<int, \Modules\Chat\app\Models\Chat> $chats
 * @property-read int|null $chats_count
 * @property mixed $creator
 * @property mixed $current_weight
 * @property mixed $daily_water_consumption
 * @property-read mixed $daily_water_consumption_meta
 * @property mixed $diet_plan
 * @property-read mixed $diet_plan_meta
 * @property mixed $diet_type
 * @property-read mixed $diet_type_meata
 * @property mixed $favorite_basic_foods
 * @property mixed $favorite_recipe
 * @property mixed $first_name
 * @property-read Collection<int, FoodConsumption> $foodConsumptions
 * @property-read int|null $food_consumptions_count
 * @property mixed $food_restriction
 * @property-read mixed $food_restriction_meta
 * @property-read mixed $full_name
 * @property mixed $gender
 * @property-read mixed $gender_meta
 * @property mixed $habits
 * @property-read mixed $habits_meta
 * @property mixed $how_many_days_week_exercise
 * @property-read mixed $how_many_days_week_exercise_meta
 * @property mixed $how_much_experience_sports
 * @property-read mixed $how_much_experience_sports_meta
 * @property mixed $invitation_code
 * @property-read Collection<int, InviteFriend> $inviteFriends
 * @property-read int|null $invite_friends_count
 * @property string $last_name
 * @property-read Collection<int, Message> $messages
 * @property-read int|null $messages_count
 * @property-read mixed $newly_registered
 * @property-read Collection<int, PackageUser> $packages
 * @property-read int|null $packages_count
 * @property mixed $popup
 * @property string|null $request_payment_inviting_friends
 * @property mixed $route
 * @property mixed $tall
 * @property-read mixed $tall_meta
 * @property mixed $target_of_exercise
 * @property-read mixed $target_of_exercise_meta
 * @property mixed $target_plan
 * @property-read mixed $target_plan_meta
 * @property mixed $target_weight
 * @property-read mixed $target_weight_meta
 * @property mixed $type_daily_work
 * @property-read mixed $type_daily_work_meta
 * @property mixed $wake_up
 * @property-read mixed $wake_up_meta
 * @property-read Collection<int, WalletTransaction> $walletTransactions
 * @property-read int|null $wallet_transactions_count
 * @property-read Collection<int, WaterConsumption> $waterConsumptions
 * @property-read int|null $water_consumptions_count
 * @property mixed $weaknesses_body
 * @property-read mixed $weaknesses_body_meta
 * @property mixed $weight
 * @property-read mixed $weight_meta
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 * <<<<<<< HEAD
 * @property mixed $athlete_or_not
 * =======
 * >>>>>>> 3c6c4a4 (sercer changes)
 * @property mixed $referral_percentage
 * @property mixed $weight_change_per_week
 * @property mixed $telegram_chat_id
 * @property-read UserSession|null $userSession
 * @property-read InviteFriend|null $invitedBy
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read Collection<int, ActivityLog> $logBy
 * @property-read int|null $log_by_count
 * @property-read Collection<int, ActivityLog> $logFor
 * @property-read int|null $log_for_count
 * @property mixed $apply_to_all_payments
 * @property mixed $last_supporter_called
 * @property-read Collection<int, WhatsappMessage> $whatsappMessages
 * @property-read int|null $whatsapp_messages_count
 * @property-read Collection<int, CourseUser> $courses
 * @property-read int|null $courses_count
 * @property-read Collection<int, \Modules\Diet\Entities\DietShoppingList> $dietShoppingLists
 * @property-read int|null $diet_shopping_lists_count
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasRoles, Notifiable, HasFactory, HasApiTokens, UserAttributeTrait, MetaAttributeTrait, HasReminderDispatch;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $with = ['metas'];

    public function isAdmin()
    {
        return $this->roles()->where('id', 1)->count() > 0;
    }

    public function isDefaultUSer()
    {
        return $this->roles()->where('id', 2)->count() > 0;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function routeNotificationForFcm($notification = null)
    {
        // همه دستگاه‌ها را بگیر
        $tokens = $this->userDevices()
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        // اگر هیچ توکنی نبود → null بده
        if (empty($tokens)) {
            return null;
        }

        // اگر یک توکن بود → string بده (بهینه‌تر)
        if (count($tokens) === 1) {
            return $tokens[0];
        }

        // اگر چند توکن بود → array بده
        return $tokens;
    }
    public static function adminSupportRoles(): array
    {
        $roles = [];
        $adminRoles = Role::whereHas('permissions', function ($query) {
            $query->where('name', 'ADMIN_ACCESS');
        })->get();
        foreach ($adminRoles as $adminRole) {
            if ($adminRole->id === 1) {
                continue;
            }
            $roles[$adminRole->id] = $adminRole->name;
        }
        return $roles;
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
//    public function routeNotificationForFcm(): array|string|null
//    {
//        return $this->userDevices()->latest()->first()?->fcm_token;
//    }

    public function userDevices(): HasMany
    {
        return $this->hasMany(\Modules\Api\Entities\UserDevice::class);
    }


    public function getMeta(UserMetaEnum $metaKey): ?UserMeta
    {
        return $this->metas->where('meta_key', $metaKey)->last() ?? null;
    }

    public function metas(): HasMany
    {
        return $this->hasMany(UserMeta::class);
    }


    public function requestPaymentInvitingFriends(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getMeta(UserMetaEnum::REQUEST_PAYMENT_INVITING_FRIENDS)?->meta_value,
            set: function (?string $value) {
                if (is_null($value)) {
                    // Attempt to delete the meta entry if the value is null.
                    // If the entry doesn't exist, this operation will not cause any issues.
                    $this->metas()->where('meta_key', UserMetaEnum::REQUEST_PAYMENT_INVITING_FRIENDS)->delete();
                } else {
                    // Update or create the meta entry if the value is not null.
                    $this->metas()->updateOrCreate(
                        ['meta_key' => UserMetaEnum::REQUEST_PAYMENT_INVITING_FRIENDS],
                        ['meta_value' => $value]
                    );
                }
            }
        );
    }

    public function     getMetas(UserMetaEnum $metaKey): ?Collection
    {
        return $this->metas->where('meta_key', $metaKey) ?? null;
    }

    public function setMetas(UserMetaEnum $metaKey, $value): void
    {
        // Check if a meta with the given key already exists
        $meta = $this->metas()->where('meta_key', $metaKey)->first();

        if ($meta) {
            // Update the existing meta value
            $meta->meta_value = $value;
            $meta->save();
        } else {
            // Create a new meta if it doesn't exist
            $this->metas()->create([
                'meta_key' => $metaKey,
                'meta_value' => $value,
            ]);
        }
    }

    public function supporter(): belongsToMany
    {
        return $this->belongsToMany(User::class, 'user_supports', 'user_id', 'support_id');
    }

    public function my(): belongsToMany
    {
        return $this->belongsToMany(User::class, 'user_supports', 'support_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getReferrerUser()
    {
        return $this->inviteFriends()->where('user_invited_id', $this->id)->first();
    }

    public function dietRequests(): HasMany
    {
        return $this->hasMany(\Modules\Diet\Entities\DietRequest::class);
    }

    public function dietShoppingLists(): HasMany
    {
        return $this->hasMany(\Modules\Diet\Entities\DietShoppingList::class);
    }

    public function invitedBy()
    {
        return $this->hasOne(InviteFriend::class, 'user_invited_id')->with('user');
    }
    public function inviteFriends(): HasMany
    {
        return $this->hasMany(InviteFriend::class);
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Core\Entities\Disease::class, 'diseases_users', 'user_id', 'disease_id');
    }

    public function exercisePlanRequests(): HasMany
    {
        return $this->hasMany(\Modules\Exercise\Entities\ExercisePlanRequest::class);
    }
    public function age(): int
    {
        $birthDayData = $this->birthday;
        if ($birthDayData == null) {
            return 0;
        }
        $birthDay = json_decode($birthDayData);
        $day = $birthDay->day;
        if ($day == 0) {
            $day++;
        }
        $month = $birthDay->month;
        if ($month == 0) {
            $month++;
        }
        $shamsiDate = Verta::parse("{$birthDay->year}/{$month}/{$day}");
        return $shamsiDate->diff(now())->y;
    }

    public function activePackage()
    {
        return $this->packages()
            ->where('type', PackageUserTypeEnum::IN_USE)
            ->where('start_at', '<', Carbon::now()->addDay())
            ->where('end_at', '>', Carbon::now())
            ->first();
    }

    public function activeDiet()
    {
        return $this->dietRequests()
            ->where('status', DietRequestStatusEnum::ACTIVE)
            ->where('start_date', '<=', Carbon::now()->addDay())
            ->where('end_date', '>=', Carbon::now())
            ->first();
    }

    public function completedDiets()
    {
        return $this->dietRequests()
            ->where('status', DietRequestStatusEnum::END)
            ->where('end_date', '<=', Carbon::now())->latest()
            ->first();
    }
    public function completedDietsQuery()
    {
        return $this->dietRequests()
            ->where('status', DietRequestStatusEnum::END)
            ->where('end_date', '<=', now());
    }

    public function completedDiet()
    {
        return $this->completedDietsQuery()
            ->latest()
            ->first();
    }
    public function pendingDiet()
    {
        return $this->dietRequests()
            ->whereIn('status', [DietRequestStatusEnum::REJECT_BY_SYSTEM_HAVE_ERROR, DietRequestStatusEnum::REJECT_BY_SYSTEM, DietRequestStatusEnum::REJECTED])
            ->where('created_at', '>=', Carbon::now()->subWeeks(2))
            ->first();
    }

    public function packages(): HasMany
    {
        return $this->hasMany(PackageUser::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(CourseUser::class);
    }

    public function chats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Modules\Chat\app\Models\Chat::class);
    }
    public function metaOptionsNames($meta_name): string
    {
        $values =  $this->metaoptionsValues($meta_name);
        if (isset($values) && is_int($values)) {
            return  $this->metaOptionsName($meta_name, $values);
        } elseif (isset($values) && is_array($values)) {
            $options_name = [];
            foreach ($values as $key  =>  $meta_values_option) {
                $options_name[]  =   $this->metaOptionsName($meta_name, $meta_values_option);
            }
            $returned_values = '';
            foreach ($options_name as $key =>  $options_name) {
                $returned_values .= ($key == 0  ? '' : ',') . $options_name;
            };
            return $returned_values;
        }
        return ' ---';
    }

    public function metaoptionsValues($meta_value)
    {

        if (isset($meta_value)) {
            return json_decode($this->$meta_value?->last()?->meta_value, true);
        }
    }
    public function metaOptionsName($meta_type, $metaOptions)
    {

        return  $this->$meta_type?->last()?->meta_key->getOptionName($metaOptions);
    }
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
    public function walletDebitTransactions()
    {
        return $this->walletTransactions()->where('transaction_type','debit')->get();
    }
    public function inComeWalletTransactionsCount(): int
    {
        return $this->walletTransactions()->where('transaction_type','credit')->count();
    }

    public function increaseWallet(int $amount): bool
    {
        if ($amount <= 0) {
            return false;
        }

        return $this->update([
            'wallet' => $this->wallet + $amount,
        ]);
    }
    public function decreaseWallet(int $amount): bool
    {
        if ($amount <= 0 || $this->wallet < $amount) {
            return false;
        }

        return $this->update([
            'wallet' => $this->wallet - $amount,
        ]);
    }

    public function foodConsumptions(): HasMany
    {
        return $this->hasMany(FoodConsumption::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'user_id');
    }

    public function waterConsumptions(): HasMany
    {
        return $this->hasMany(WaterConsumption::class);
    }

    public function userSession(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserSession::class);
    }
    public function logFor()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
    public function logBy()
    {
        return $this->hasMany(ActivityLog::class, 'admin_id');
    }
    public function getApplicationSms()
    {
        return $this->logFor()->where('event', \Modules\Admin\app\Enums\ActivityEventEnum::SEND_APP_SMS_LINK)->exists();
    }
    public function smsLog()
    {
        return $this->logFor()->where('event', \Modules\Admin\app\Enums\ActivityEventEnum::SEND_APP_SMS_LINK)->get();
    }
    public static function supporters()
    {
        return Role::find(3)?->users();
    }
    public function lastSupporterCalledName(): string
    {
        if (! is_null($this->lastSupporterCalled)) {
            return User::find($this->lastSupporterCalled)?->fullName ?? ' ';
        }
        return '';
    }
}
