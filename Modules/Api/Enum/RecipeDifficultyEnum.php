<?php

declare(strict_types=1);

namespace Modules\Api\Enum;

enum RecipeDifficultyEnum: int
{
    case EASY = 1;
    case NORMAL = 2;
    case HARD = 3;

    public function apiResponse(): array
    {
        return [
            'name' => $this->getName(),
            'value' => $this->value,
        ];
    }

    public function getName(): string
    {
        return match ($this) {
            self::EASY => 'آسان',
            self::NORMAL => 'متوسط',
            self::HARD => 'سخت',
        };
    }
}
