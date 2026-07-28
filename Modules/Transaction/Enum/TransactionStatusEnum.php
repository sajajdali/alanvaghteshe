<?php

namespace Modules\Transaction\Enum;
use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum TransactionStatusEnum : int implements EnumHasNameInterface
{
    use EnumFunctionTrait ;

    case SUCCESSFUL = 1;
    case REJECTED = 0;
    case PENDING = 2;


    public function getName(): string
    {
        return match($this)
        {
           self::SUCCESSFUL => 'موفق' ,
           self::REJECTED => 'ناموفق' ,
           self::PENDING => 'در حال انجام' ,
            default => "",
        } ;
    }


}
