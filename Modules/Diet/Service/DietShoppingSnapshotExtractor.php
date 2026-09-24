<?php

namespace Modules\Diet\Service;

use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Diet\Enum\ShoppingListPeriodEnum;
use RuntimeException;

class DietShoppingSnapshotExtractor
{
    public function __construct(
        private readonly DietShoppingListInputHasher $hasher
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function extract(
        DietRequest $dietRequest,
        ShoppingListPeriodEnum $period,
        int $userId,
        ?CarbonInterface $now = null
    ): array {
        $this->ensureAccessible($dietRequest, $userId);

        $range = $period->dateRange($now);

        $details = $dietRequest->dietRequestDetails()
            ->with('meal:id,name')
            ->whereNull('replaced_parent_id')
            ->whereBetween('date_of_day', [
                $range['start']->toDateString(),
                $range['end']->toDateString(),
            ])
            ->orderBy('date_of_day')
            ->orderBy('meal_id')
            ->orderBy('id')
            ->get();

        $snapshot = [
            'diet_request_id' => $dietRequest->getKey(),
            'period' => $period->value,
            'start_date' => $range['start']->toDateString(),
            'end_date' => $range['end']->toDateString(),
            'meals' => $this->mapDetails($details),
        ];

        $snapshot['input_hash'] = $this->hasher->hash($snapshot);

        return $snapshot;
    }

    private function ensureAccessible(DietRequest $dietRequest, int $userId): void
    {
        if ((int) $dietRequest->user_id !== $userId) {
            throw new AuthorizationException('Diet request does not belong to the authenticated user.');
        }

        if (!$dietRequest->active || $dietRequest->status !== DietRequestStatusEnum::ACTIVE) {
            throw new RuntimeException('Shopping lists can only be generated for an active diet.');
        }
    }

    /**
     * @param Collection<int, DietRequestDetail> $details
     * @return array<int, array<string, mixed>>
     */
    private function mapDetails(Collection $details): array
    {
        return $details->map(function (DietRequestDetail $detail): array {
            $storedDetail = $this->decodeDetail($detail->detail);
            $foodSnapshot = $storedDetail['food_snapshot'] ?? null;

            if (!is_array($foodSnapshot)) {
                throw new RuntimeException("Diet detail {$detail->id} does not contain a valid food snapshot.");
            }

            return [
                'diet_request_detail_id' => $detail->getKey(),
                'date' => $detail->date_of_day?->toDateString(),
                'meal_id' => $detail->meal_id,
                'meal_name' => $detail->meal?->name,
                'food' => [
                    'food_id' => $foodSnapshot['food_id'] ?? $detail->foodable_id,
                    'food_name' => $foodSnapshot['food_name'] ?? null,
                    'basic_foods' => $this->normalizeBasicFoods($foodSnapshot['basic_foods'] ?? []),
                ],
            ];
        })->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeDetail(mixed $detail): array
    {
        if (is_array($detail)) {
            return $detail;
        }

        if (!is_string($detail) || trim($detail) === '') {
            return [];
        }

        $decoded = json_decode($detail, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeBasicFoods(mixed $basicFoods): array
    {
        if (!is_array($basicFoods)) {
            return [];
        }

        return collect($basicFoods)
            ->filter(fn (mixed $food): bool => is_array($food))
            ->map(fn (array $food): array => [
                'basic_food_id' => $food['basic_food_id'] ?? null,
                'name' => $food['name'] ?? null,
                'quantity' => isset($food['quantity']) ? (float) $food['quantity'] : null,
                'unit' => $food['unit'] ?? null,
                'unit_id' => $food['unit_id'] ?? null,
                'grams' => isset($food['weight_per_gram']) ? (float) $food['weight_per_gram'] : null,
            ])
            ->values()
            ->all();
    }
}
