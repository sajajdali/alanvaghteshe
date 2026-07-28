<?php

namespace Modules\Api\Enum;

enum AuthRequestStatusEnum: int
{
    case ACTIVE = 1;
    case APPROVED = 2;
}
