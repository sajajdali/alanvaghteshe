<?php

namespace Modules\Api\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Api\app\Resources\MessageResource;
use Modules\Api\Trait\ApiHandlerTrait;

class MessageController extends Controller
{
   use ApiHandlerTrait;
    public function index()
    {
        $messages = auth()->user()->messages()->latest()->paginate(20);

        return $this->ok([
            'data' => MessageResource::collection($messages->items()), // پیام‌های صفحه فعلی
            'pagination' => [
                'total' => $messages->total(),
                'per_page' => $messages->perPage(),
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
            ],
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $message = auth()->user()->messages()->findOrFail($id);

        if (auth()->user()->id !== $message->user_id) {
            return $this->badRequest([
                'message' => 'You can not mark this message as read.'
            ]);
        }

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return $this->ok([
            'message' => 'پیغام به حالت مشاهده شده تغییر یافت',
            'data' => new MessageResource($message),
        ]);
    }

}
