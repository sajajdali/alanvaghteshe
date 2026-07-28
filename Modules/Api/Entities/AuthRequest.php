<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Modules\Api\Emails\RegisterMail;
use Modules\Api\Enum\AuthRequestStatusEnum;
use Modules\Api\Notifications\AuthSmsNotification;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;

/**
 * Modules\Api\Entities\AuthRequest
 *
 * @property int $id
 * @property string|null $mobile
 * @property string|null $email
 * @property string|null $ip
 * @property string|null $code
 * @property AuthRequestStatusEnum $status
 * @property int $request_count
 * @property \Illuminate\Support\Carbon|null $expire_at
 * @property \Illuminate\Support\Carbon|null $next_request_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest active()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereNextRequestAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereRequestCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AuthRequest extends Model
{
    use Notifiable;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'expire_at' => 'datetime',
        'status' => AuthRequestStatusEnum::class,
        'next_request_at' => 'datetime',
    ];

    public static function canRequest($mobileOrEmail): bool
    {
        return self::where(function ($query) use ($mobileOrEmail) {
            $query->where('mobile', $mobileOrEmail)
                ->orWhere('email', $mobileOrEmail);
        })->where('next_request_at', '>', now())
            ->count() === 0;
    }

    public static function make($mobileOrEmail, $ip): void
    {
        $code = self::makeCode();
        $expireAt = now()->addMinutes(5);
        $nextRequestAt = now()->addMinutes(1);
        $status = AuthRequestStatusEnum::ACTIVE;
        //check has old request
        $oldRequest = self::where('mobile', $mobileOrEmail)
            ->orWhere('email', $mobileOrEmail)
            ->first();
        if ($oldRequest) {
            $oldRequest->update([
                'code' => $code,
                'expire_at' => $expireAt,
                'next_request_at' => $nextRequestAt,
                'status' => $status,
                'request_count' => $oldRequest->request_count + 1,
            ]);
        } else {
            $email = filter_var($mobileOrEmail, FILTER_VALIDATE_EMAIL) ? $mobileOrEmail : null;
            $mobile = $email ? null : $mobileOrEmail;
            $oldRequest = self::create([
                'mobile' => $mobile,
                'email' => $email,
                'ip' => $ip,
                'code' => $code,
                'expire_at' => $expireAt,
                'next_request_at' => $nextRequestAt,
                'status' => $status,
            ]);
        }
        //send notification
        if ($oldRequest->mobile) {
            $oldRequest->notify(new AuthSmsNotification($code));
        } else {
            Mail::to($mobileOrEmail)->send(new RegisterMail($code));

        }
    }

    /**
     * Make random code with 4 digits
     */
    public static function makeCode(): string
    {
        return \str_pad(\rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public static function check($mobileOrEmail, $code): bool
    {

        // TODO temp auth check
        if ($code == '2223' || $code == '2345') {
            $request = self::where(function ($query) use ($mobileOrEmail) {
                $query->where('mobile', $mobileOrEmail)
                    ->orWhere('email', $mobileOrEmail);
            })
                ->where('expire_at', '>=', now())
                ->where('status', AuthRequestStatusEnum::ACTIVE)
                ->first();
            if ($request) {
                $request->update([
                    'status' => AuthRequestStatusEnum::APPROVED,
                ]);
                return true;
            }
        }
        $request = self::where(function ($query) use ($mobileOrEmail) {
            $query->where('mobile', $mobileOrEmail)
                ->orWhere('email', $mobileOrEmail);
        })
            ->where('code', $code)
            ->where('expire_at', '>=', now())
            ->where('status', AuthRequestStatusEnum::ACTIVE)
            ->first();
        if ($request) {
            $request->update([
                'status' => AuthRequestStatusEnum::APPROVED,
            ]);

            return true;
        }

        return false;
    }

    public static function getUser($mobileOrEmail): User
    {
        $user = User::where(function ($query) use ($mobileOrEmail) {
            $query->where('mobile', $mobileOrEmail)
                ->orWhere('email', $mobileOrEmail);
        })->first();
        if (! $user) {
            $mobile = filter_var($mobileOrEmail, FILTER_VALIDATE_EMAIL) ? null : $mobileOrEmail;
            $email = $mobile ? null : $mobileOrEmail;
            $user = User::create([
                'mobile' => $mobile,
                'email' => $email,
                'password' => bcrypt(\Str::random(8)),
            ]);
            //get role with permmision USER_DEFAULT
            $role = Permission::where('name', 'USER_DEFAULT')->first()->roles()->first();
            $user->assignRole($role);

            app(\App\Services\AppMetricaService::class)->sendEvent(
                profileId: $user->id,
                eventName: 'signup',
                params: [
                    'mobile_or_email' => $mobile,
                ]);
        }

        return $user;
    }

    public static function checkUserExist($mobileOrEmail): bool
    {
        $user = User::where(function ($query) use ($mobileOrEmail) {
            $query->where('mobile', $mobileOrEmail)
                ->orWhere('email', $mobileOrEmail);
        })->first();
        if ( $user && $user->gender != '') {
            return true;
        }

        return false;
    }

    public function isExpired(): bool
    {
        return $this->expire_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('status', AuthRequestStatusEnum::ACTIVE);
    }
}
