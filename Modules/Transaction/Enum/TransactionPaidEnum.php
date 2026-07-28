<?php

namespace Modules\Transaction\Enum;
use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum TransactionPaidEnum : int implements EnumHasNameInterface
{
    use EnumFunctionTrait ;

    case ONLINE = 1;
    case CARD_TO_CARD = 2;
    case BY_ADMIN = 3;
    case BY_BAZAR = 4;


    public function getName(): string
    {
        return match($this)
        {
           self::ONLINE => 'آنلاین' ,
           self::CARD_TO_CARD => 'کارت به کارت' ,
           self::BY_ADMIN => 'ثبت شده توسط ادمین' ,
           self::BY_BAZAR => 'توسط بازار' ,
            default => "",
        } ;
    }
}
