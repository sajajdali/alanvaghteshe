<?php

namespace Modules\Chat\Enum;

enum ChatStatusEnum : int
{
    case JUST_CREATED = 0;
    case USER_SEND_QUESTION = 10;
    case USER_SEND_QUESTION_ADMIN_SEEN = 20;
    case ANSWERED = 30;
}
