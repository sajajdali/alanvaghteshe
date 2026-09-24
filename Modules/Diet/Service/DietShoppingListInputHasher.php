<?php

namespace Modules\Diet\Service;

class DietShoppingListInputHasher
{
    public const ALGORITHM_VERSION = 'v1';

    /**
     * @param array<string, mixed> $snapshot
     */
    public function hash(array $snapshot): string
    {
        $payload = [
            'algorithm_version' => self::ALGORITHM_VERSION,
            'diet_request_id' => $snapshot['diet_request_id'],
            'period' => $snapshot['period'],
            'start_date' => $snapshot['start_date'],
            'end_date' => $snapshot['end_date'],
            'meals' => $snapshot['meals'],
        ];

        return hash('sha256', json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR
        ));
    }
}
