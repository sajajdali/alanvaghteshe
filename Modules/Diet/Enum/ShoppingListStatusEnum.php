<?php

namespace Modules\Diet\Enum;

enum ShoppingListStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case READY = 'ready';
    case FAILED = 'failed';
}
