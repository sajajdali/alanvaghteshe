<?php

namespace Modules\Diet\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RuntimeException;

class OpenAiShoppingListService
{
    public const PROMPT_VERSION = 'shopping-list-v1';

    public const CATEGORIES = [
        'protein' => 'پروتئین‌ها',
        'dairy' => 'لبنیات',
        'vegetables' => 'سبزیجات و صیفی‌جات',
        'fruits' => 'میوه‌ها',
        'bread_grains' => 'نان و غلات',
        'legumes' => 'حبوبات',
        'nuts_seeds' => 'مغزها و دانه‌ها',
        'condiments' => 'چاشنی‌ها و مواد مصرفی',
        'beverages' => 'نوشیدنی‌ها',
        'other' => 'سایر',
    ];

    /**
     * @param array<string, mixed> $snapshot
     * @return array{result: array<string, mixed>, model: string, input_tokens: int|null, output_tokens: int|null}
     */
    public function generate(array $snapshot): array
    {
        $apiKey = (string) config('services.openai.api_key');
        $model = (string) config('services.openai.model', 'gpt-5-nano');

        if ($apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout((int) config('services.openai.timeout', 90))
            ->retry(2, 500, throw: false)
            ->post('https://api.openai.com/v1/responses', $this->buildPayload($snapshot, $model));

        if (!$response->successful()) {
            $message = data_get($response->json(), 'error.message');

            throw new RuntimeException(
                'OpenAI request failed with HTTP status '.$response->status().
                (is_string($message) && $message !== '' ? ': '.$message : '.')
            );
        }

        $body = $response->json();

        if (!is_array($body) || ($body['status'] ?? null) !== 'completed') {
            throw new RuntimeException('OpenAI response was not completed.');
        }

        $result = json_decode($this->extractOutputText($body), true);

        if (!is_array($result)) {
            throw new RuntimeException('OpenAI returned invalid shopping list JSON.');
        }

        try {
            $normalized = $this->validateAndNormalize($result, $snapshot);
        } catch (RuntimeException $exception) {
            if (!str_starts_with($exception->getMessage(), 'OpenAI returned an incomplete shopping list')) {
                throw $exception;
            }

            $normalized = $this->buildCompleteList($snapshot);
        }

        return [
            'result' => $normalized,
            'model' => (string) ($body['model'] ?? $model),
            'input_tokens' => isset($body['usage']['input_tokens']) ? (int) $body['usage']['input_tokens'] : null,
            'output_tokens' => isset($body['usage']['output_tokens']) ? (int) $body['usage']['output_tokens'] : null,
        ];
    }

    /**
     * Builds a guaranteed-complete list from the prescribed basic foods when AI omits items.
     *
     * @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    public function buildCompleteList(array $snapshot): array
    {
        $items = [];

        foreach ($snapshot['meals'] as $meal) {
            $source = [
                'date' => $meal['date'],
                'meal' => $this->normalizeSourceText($meal['meal_name']),
                'food_name' => $this->normalizeSourceText($meal['food']['food_name']),
            ];

            foreach ($meal['food']['basic_foods'] as $food) {
                $name = $this->normalizeSourceText($food['name']);
                $unit = $this->normalizeSourceText($food['unit']);
                $key = ($food['basic_food_id'] ?? $name).'|'.($food['unit_id'] ?? $unit);

                if (!isset($items[$key])) {
                    $items[$key] = [
                        'key' => 'food-'.($food['basic_food_id'] ?? md5($name.'|'.$unit)),
                        'name' => $name,
                        'quantity' => 0.0,
                        'unit' => $unit !== '' ? $unit : null,
                        'is_estimated' => false,
                        'is_checked' => false,
                        'sources' => [],
                    ];
                }

                $items[$key]['quantity'] += (float) ($food['quantity'] ?? 0);
                $items[$key]['sources'][] = $source;
            }
        }

        $groups = [];
        foreach ($items as $item) {
            $item['quantity'] = round($item['quantity'], 2);
            $item['display_quantity'] = $this->formatQuantity($item['quantity'], $item['unit']);
            $item['sources'] = collect($item['sources'])->unique(fn (array $source) => json_encode($source))->values()->all();
            $category = $this->categoryFor($item['name']);
            $groups[$category][] = $item;
        }

        return [
            'schema_version' => 1,
            'engine' => 'prescribed-foods-fallback',
            'prompt_version' => self::PROMPT_VERSION,
            'title' => $snapshot['period'] === 'daily' ? 'سبد خرید فردا' : 'سبد خرید هفت روز آینده',
            'total_items' => count($items),
            'checked_items' => 0,
            'progress_percent' => 0,
            'groups' => collect(self::CATEGORIES)->map(fn (string $title, string $key) => isset($groups[$key]) ? [
                'key' => $key,
                'title' => $title,
                'items' => $groups[$key],
            ] : null)->filter()->values()->all(),
        ];
    }

    private function formatQuantity(float $quantity, ?string $unit): string
    {
        $number = fmod($quantity, 1.0) === 0.0
            ? (string) (int) $quantity
            : rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');

        return trim($number.' '.($unit ?? ''));
    }

    private function categoryFor(string $name): string
    {
        $categories = [
            'fruits' => ['موز', 'پرتقال', 'نارنگی', 'میوه', 'لیمو', 'خرما', 'آووکادو'],
            'vegetables' => ['سبزی', 'سالاد', 'خیار', 'گوجه', 'کاهو', 'قارچ', 'اسفناج', 'سیب زمینی'],
            'dairy' => ['شیر', 'ماست', 'پنیر'],
            'bread_grains' => ['نان', 'برنج', 'رایس کیک', 'پاپ کورن', 'کیک', 'پنکیک'],
            'legumes' => ['عدس', 'لوبیا', 'نخود'],
            'nuts_seeds' => ['بادام', 'گردو', 'آجیل', 'چیا'],
            'beverages' => ['چای', 'قهوه'],
            'condiments' => ['سس', 'زیتون', 'شکلات'],
            'protein' => ['مرغ', 'ماهی', 'کباب', 'تخم مرغ', 'تخم‌مرغ', 'املت'],
        ];

        foreach ($categories as $category => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($name, $needle)) {
                    return $category;
                }
            }
        }

        return 'other';
    }

    /**
     * @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    public function buildPayload(array $snapshot, string $model): array
    {
        $safeMeals = collect($snapshot['meals'])->map(fn (array $meal, int $index): array => [
            'source_id' => $index + 1,
            'date' => $meal['date'],
            'meal' => $this->normalizeSourceText($meal['meal_name']),
            'food_name' => $this->normalizeSourceText($meal['food']['food_name']),
            'foods' => collect($meal['food']['basic_foods'])->map(fn (array $food): array => [
                'name' => $this->normalizeSourceText($food['name']),
                'quantity' => $food['quantity'],
                'unit' => $food['unit'],
                'grams' => $food['grams'],
            ])->values()->all(),
        ])->values()->all();

        $safeInput = [
            'period' => $snapshot['period'],
            'start_date' => $snapshot['start_date'],
            'end_date' => $snapshot['end_date'],
            'expected_source_count' => count($safeMeals),
            'meals' => $safeMeals,
        ];

        return [
            'model' => $model,
            'store' => false,
            'reasoning' => [
                'effort' => 'minimal',
            ],
            'instructions' => $this->instructions(),
            'input' => [[
                'role' => 'user',
                'content' => json_encode(
                    $safeInput,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                ),
            ]],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'diet_shopping_list',
                    'strict' => true,
                    'schema' => $this->schema($safeMeals),
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    public function validateAndNormalize(array $result, array $snapshot): array
    {
        $validator = Validator::make($result, [
            'groups' => ['required', 'array'],
            'groups.*.key' => ['required', 'string', Rule::in(array_keys(self::CATEGORIES))],
            'groups.*.title' => ['required', 'string'],
            'groups.*.items' => ['required', 'array'],
            'groups.*.items.*.key' => ['required', 'string', 'max:120'],
            'groups.*.items.*.name' => ['required', 'string', 'max:255'],
            'groups.*.items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'groups.*.items.*.unit' => ['nullable', 'string', 'max:80'],
            'groups.*.items.*.display_quantity' => ['required', 'string', 'max:255'],
            'groups.*.items.*.is_estimated' => ['required', 'boolean'],
            'groups.*.items.*.source_ids' => ['required', 'array', 'min:1'],
            'groups.*.items.*.source_ids.*' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw new RuntimeException('OpenAI shopping list validation failed: '.$validator->errors()->first());
        }

        $seenItemKeys = [];
        $seenGroupKeys = [];
        $coveredSourceIds = [];
        $allowedSources = [];
        $groups = [];

        foreach ($snapshot['meals'] as $index => $meal) {
            $allowedSources[$index + 1] = [
                'date' => $meal['date'],
                'meal' => $this->normalizeSourceText($meal['meal_name']),
                'food_name' => $this->normalizeSourceText($meal['food']['food_name']),
            ];
        }

        foreach ($result['groups'] as $group) {
            if (isset($seenGroupKeys[$group['key']])) {
                throw new RuntimeException('OpenAI returned a duplicate shopping group key.');
            }

            $seenGroupKeys[$group['key']] = true;
            $items = [];

            foreach ($group['items'] as $item) {
                if (isset($seenItemKeys[$item['key']])) {
                    throw new RuntimeException('OpenAI returned a duplicate shopping item key.');
                }

                $seenItemKeys[$item['key']] = true;

                $sources = [];
                foreach (array_unique($item['source_ids']) as $sourceId) {
                    $canonicalSource = $allowedSources[$sourceId] ?? null;

                    if ($canonicalSource === null) {
                        throw new RuntimeException('OpenAI returned a source that is not present in the prescribed diet.');
                    }

                    $sources[] = $canonicalSource;
                    $coveredSourceIds[(int) $sourceId] = true;
                }

                $items[] = [
                    'key' => $item['key'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'display_quantity' => $item['display_quantity'],
                    'is_estimated' => (bool) $item['is_estimated'],
                    'is_checked' => false,
                    'sources' => $sources,
                ];
            }

            if ($items !== []) {
                $groups[] = [
                    'key' => $group['key'],
                    'title' => self::CATEGORIES[$group['key']],
                    'items' => $items,
                ];
            }
        }

        $missingSourceIds = array_diff(array_keys($allowedSources), array_keys($coveredSourceIds));
        if ($missingSourceIds !== []) {
            throw new RuntimeException(
                'OpenAI returned an incomplete shopping list; missing source IDs: '.implode(', ', $missingSourceIds)
            );
        }

        return [
            'schema_version' => 1,
            'engine' => 'openai',
            'prompt_version' => self::PROMPT_VERSION,
            'title' => $snapshot['period'] === 'daily' ? 'سبد خرید فردا' : 'سبد خرید هفت روز آینده',
            'total_items' => count($seenItemKeys),
            'checked_items' => 0,
            'progress_percent' => 0,
            'groups' => $groups,
        ];
    }

    private function normalizeSourceText(mixed $value): string
    {
        $value = str_replace(["\u{200C}", "\u{00A0}"], [' ', ' '], (string) $value);

        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * @param array<string, mixed> $body
     */
    private function extractOutputText(array $body): string
    {
        if (is_string($body['output_text'] ?? null) && $body['output_text'] !== '') {
            return $body['output_text'];
        }

        foreach ($body['output'] ?? [] as $output) {
            foreach ($output['content'] ?? [] as $content) {
                if (($content['type'] ?? null) === 'output_text' && is_string($content['text'] ?? null)) {
                    return $content['text'];
                }
            }
        }

        throw new RuntimeException('OpenAI response did not contain output text.');
    }

    private function instructions(): string
    {
        return <<<'PROMPT'
از تمام وعده‌های رژیم فارسی یک سبد خرید کامل بسازید، نه فقط مواد پروتئینی. هیچ وعده‌ای را حذف نکن: همه source_idها از ۱ تا expected_source_count باید دست‌کم یک بار در source_ids اقلام خروجی وجود داشته باشند. میوه، سبزی، لبنیات، نان و غلات، نوشیدنی و سایر مواد را نیز پوشش بده. غذاهای ترکیبی را به مواد قابل خرید تبدیل و اقلام همسان را تجمیع کن. مقدار را کوتاه و کاربردی بنویس و مقدار تخمینی را مشخص کن. برای هر قلم فقط source_idهای مرتبط موجود در ورودی را برگردان. فقط از دسته‌های schema استفاده کن و چیزی خارج از رژیم نیفزا.
PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    private function schema(array $safeMeals): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['groups'],
            'properties' => [
                'groups' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['key', 'title', 'items'],
                        'properties' => [
                            'key' => ['type' => 'string', 'enum' => array_keys(self::CATEGORIES)],
                            'title' => ['type' => 'string', 'minLength' => 1],
                            'items' => [
                                'type' => 'array',
                                'minItems' => 1,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'required' => ['key', 'name', 'quantity', 'unit', 'display_quantity', 'is_estimated', 'source_ids'],
                                    'properties' => [
                                        'key' => ['type' => 'string', 'minLength' => 1],
                                        'name' => ['type' => 'string', 'minLength' => 1],
                                        'quantity' => ['type' => ['number', 'null']],
                                        'unit' => ['type' => ['string', 'null']],
                                        'display_quantity' => ['type' => 'string', 'minLength' => 1],
                                        'is_estimated' => ['type' => 'boolean'],
                                        'source_ids' => [
                                            'type' => 'array',
                                            'minItems' => 1,
                                            'maxItems' => count($safeMeals),
                                            'items' => [
                                                'type' => 'integer',
                                                'enum' => range(1, count($safeMeals)),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
