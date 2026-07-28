<?php

namespace Modules\Api\Transformers\Chat;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */

    public function toArray($request): array
    {
        $isSingleMessage = $request->route()->getActionMethod() === 'store';

        if ($isSingleMessage) {
            $message = $this->chatDetails->sortByDesc('created_at')->first();

            $flatItems = collect([
                [
                    'type' => $message->is_question ? 'question' : 'answer',
                    'date' => null,
                    'content' => $message->content,
                    'seen' => true,
                    'time' => $message->created_at->format('H:i'),
                ]
            ]);

            return [
                'id' => $this->id,
                'is_banned' => $this->ban,
                'items' => $flatItems->values(),
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 1,
                    'last_page' => 1,
                ],
            ];
        }

        $perPage = 10;

        $allMessages = $this->chatDetails->sortByDesc('created_at');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $pagedMessages = $allMessages->forPage($currentPage, $perPage)->values();

        $paginatedMessages = new LengthAwarePaginator(
            $pagedMessages,
            $allMessages->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]
        );

        $groupedMessages = $pagedMessages->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        $flatItems = collect();
        foreach ($groupedMessages as $date => $messages) {
            foreach ($messages as $message) {
                $flatItems->push([
                    'type' => $message->is_question ? 'question' : 'answer',
                    'date' => null,
                    'content' => $message->content,
                    'seen' => true,
                    'time' => $message->created_at->format('H:i'),
                ]);
            }

            // add date
            $flatItems->push([
                'type' => 'date',
                'date' => getPersianDateSimple($date),
                'content' => null,
                'seen' => true,
                'time' => null,
            ]);
        }

        return [
            'id' => $this->id,
            'is_banned' => $this->ban,
            'items' => $flatItems->values(),
            'pagination' => [
                'current_page' => $paginatedMessages->currentPage(),
                'per_page' => $paginatedMessages->perPage(),
                'total' => $paginatedMessages->total(),
                'last_page' => $paginatedMessages->lastPage(),
            ],
        ];
    }
}

