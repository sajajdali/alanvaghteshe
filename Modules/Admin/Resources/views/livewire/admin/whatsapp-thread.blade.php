<div class="container py-3" dir="rtl">
    @once
        <style>
            .chat-box { background:#f8fafc; border-radius:1rem; padding:1rem; height:70vh; overflow:auto; }
            .bubble { max-width: 70%; padding:.6rem .8rem; border-radius:1rem; }
            .bubble.me { background:#d1fae5; border-bottom-right-radius:.3rem; margin-left:auto; }
            .bubble.other { background:#ffffff; border-bottom-left-radius:.3rem; margin-right:auto; border:1px solid #e5e7eb; }
            .meta { font-size:.75rem; color:#64748b; margin-top:.25rem; }
            .media-thumb img { max-width: 220px; border-radius:.6rem; }
            .media-thumb video, .media-thumb audio { max-width: 260px; border-radius:.6rem; }
            .doc-link { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .6rem; background:#eef2ff; border-radius:.5rem; }
            .sticky-footer { position: sticky; bottom: 0; background: #fff; padding-top: .75rem; }
            .bubble.me   { border:1px solid #a7f3d0; }
            .bubble.other{ border:1px solid #e5e7eb; } /* همین هست */
            .meta.me     { text-align:right; }
        </style>
    @endonce

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.whatsapp.thread', ['chat' => $chatKey]) }}" class="text-decoration-none h5 mb-0">
                گفت‌وگو با: <span class="text-primary">{{ $chatKey }}</span>
            </a>
        </div>
        <div>
            <span class="badge bg-secondary">آخرین‌ها بالا</span>
        </div>
    </div>

    <div class="chat-box mb-3">
        @forelse($messages as $msg)
            @php
                $isMe = (bool)$msg->from_me;
                $sideClass = $isMe ? 'me' : 'other';
                $ts = $msg->created_at; // فقط created_at
            @endphp

            <div class="d-flex mb-3 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="bubble {{ $sideClass }}">
                    {{-- بدنهٔ پیام/کپشن --}}
                    @if($msg->type === 'text' && $msg->body)
                        <div class="mb-1">{{ $msg->formatted_body ?? $msg->body }}</div>
                    @elseif($msg->body)
                        <div class="mb-1">{{ $msg->body }}</div>
                    @endif

                    {{-- رسانه‌ها --}}
                    @if($msg->has_media && $msg->media && count($msg->media) > 0)
                        <div class="media-thumb d-flex flex-wrap gap-2 mt-1">
                            @foreach($msg->media as $m)
                                @php $url = $m->url; $mime = $m->mime; @endphp

                                @if($mime && str_starts_with($mime, 'image/'))
                                    <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="image"></a>
                                @elseif($mime && str_starts_with($mime, 'video/'))
                                    <video src="{{ $url }}" controls></video>
                                @elseif($mime && str_starts_with($mime, 'audio/'))
                                    <audio src="{{ $url }}" controls></audio>
                                @else
                                    <a class="doc-link" href="{{ $url }}" target="_blank">
                                        <i class="bi bi-file-earmark"></i>
                                        <span>{{ $m->original_filename ?? 'دانلود فایل' }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- متادیتا --}}
                    <div class="meta d-flex align-items-center gap-2">
                        <span title="{{ $ts }}">{{ optional($ts)->diffForHumans() }}</span>
                        {{-- اگر خواستی وضعیت خوانده/نخوانده را نشان بدهی:
                        @if($isMe)
                            @if(!is_null($msg->read_at))
                                <i class="bi bi-check2-all text-primary" title="خوانده توسط مخاطب"></i>
                            @else
                                <i class="bi bi-check2" title="ارسال شد"></i>
                            @endif
                        @endif
                        --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">هیچ پیامی در این گفتگو وجود ندارد.</div>
        @endforelse
    </div>

    {{-- صفحه‌بندی --}}
    <div class="mb-3">
        {{ $messages->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>

    {{-- فرم ارسال --}}
    <div class="sticky-footer">
        <form wire:submit.prevent="send" class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <textarea class="form-control" rows="2" placeholder="نوشتن پیام..."
                              wire:model.defer="messageText"></textarea>
                    <button class="btn btn-success align-self-end" type="submit">
                        <i class="bi bi-send"></i> ارسال
                    </button>
                </div>
                @error('messageText') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
        </form>
    </div>
</div>
