<?php

namespace Modules\Diet\Enum;
use App\trait\EnumFunctionTrait;

enum DietRequestStatusEnum: int
{
    use EnumFunctionTrait ;
    case REQUESTED = 0;

    case PENDING = 10;

    case REJECTED = 20;

    case ACTIVE = 30;

    case END = 40;
    case REJECT_BY_SYSTEM = 41;
    case REJECT_BY_SYSTEM_HAVE_ERROR = 42;


    public function getName() :string
    {
        return match($this) {
            self::REQUESTED     => '<span class="badge bg-info">درخواست شده</span>',
            self::PENDING       => '<span class="badge bg-warning">در انتظار</span>',
            self::REJECTED      => '<span class="badge bg-danger">رد شده</span>',
            self::ACTIVE        => '<span class="badge bg-success">فعال</span>',
            self::END           => '<span class="badge bg-secondary">تمام شده</span>',
            self::REJECT_BY_SYSTEM           => '<span class="badge bg-secondary">غیر فعال شده توسط سیستم بابت رژیم جدید</span>',
            self::REJECT_BY_SYSTEM_HAVE_ERROR           => '<span class="badge bg-secondary">- عدم تجویز کامل بابت رژیم جدید</span>',
            default             => "",
        };
    }
    public function getJustName() :string
    {
        return match($this) {
            self::REQUESTED     => 'درخواست شده',
            self::PENDING       => 'در انتظار',
            self::REJECTED      => 'رد شده',
            self::ACTIVE        => 'فعال',
            self::END           => 'تمام شده',
            self::REJECT_BY_SYSTEM           => 'غیر فعال شده توسط سیستم',
            self::REJECT_BY_SYSTEM_HAVE_ERROR           => 'عدم تجویز - اخطار در هنگام تجویز',
            default             => "",
        };
    }
    public function boolianStatus() :string
    {
        return match($this) {
            self::REQUESTED     =>  false,
            self::PENDING       => 'false',
            self::REJECTED      => 'false',
            self::ACTIVE        => 'true',
            self::END           => 'false',
            self::REJECT_BY_SYSTEM           => 'false',
            default             => 'false',
        };
    }
}
