<div class="container py-3" dir="rtl">
    @once
        <style>
            .chat-item:hover {
                background: #f8fafc;
            }

            .avatar {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #e2e8f0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                color: #334155;
            }

            .text-truncate-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .wh-time {
                font-size: .78rem;
                color: #64748b;
                white-space: nowrap;
            }

            .wh-name {
                font-weight: 600;
                color: #0f172a;
            }

            .type-pill {
                font-size: .7rem;
                background: #eef2ff;
                color: #3730a3;
            }

            .clip {
                color: #64748b;
            }

            .badge-unread {
                background-color: #25D366;
            }
            .search-toolbar { gap:.5rem; }
            .btn-icon-sm { padding:.3rem .55rem; line-height:1; display:inline-flex; align-items:center; justify-content:center; }
            .search-group .form-control { min-width:260px; }

            /* گردی صحیح در RTL برای Input Group */
            [dir="rtl"] .search-group .form-control {
                border-top-right-radius:.375rem!important;
                border-bottom-right-radius:.375rem!important;
                border-top-left-radius:0!important;
                border-bottom-left-radius:0!important;
            }
            [dir="rtl"] .search-group .input-group-text,
            [dir="rtl"] .search-group .btn {
                border-top-left-radius:.375rem!important;
                border-bottom-left-radius:.375rem!important;
                border-top-right-radius:0!important;
                border-bottom-right-radius:0!important;
            }

            /* اسپینر ریز داخل باکس سرچ */
            .search-spinner {
                position:absolute; inset-inline-end:.5rem; top:50%; transform:translateY(-50%);
            }
            .loading-inline {
                font-size: .85rem; color:#64748b;
            }
            .list-wrapper { position: relative; }
            .loading-overlay {
                position: absolute; inset: 0;
                background: rgba(255,255,255,.7);
                display: none; align-items: center; justify-content: center;
                z-index: 2;
            }
            .loading-overlay .inside {
                display:flex; gap:.5rem; align-items:center; color:#475569;
                background:#fff; border:1px solid #e2e8f0; border-radius:.5rem; padding:.5rem .75rem;
                box-shadow: 0 6px 18px rgba(15,23,42,.08);
            }
            /* Livewire: وقتی لودینگ فعاله این کلاس ها نمایش میشن */
            [wire\\:loading].flex { display:flex!important; }
        </style>
    @endonce

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">لیست گفتگوها</h5>

            <div class="search-toolbar d-flex flex-wrap align-items-center">
                {{-- گروه ورودی جستجو --}}
                <div class="position-relative">
                    <div class="input-group input-group-sm" style="min-width:260px">
                        <input type="search"
                               class="form-control"
                               placeholder="جستجو در شماره یا متن..."
                               wire:model.debounce.600ms="search"
                               wire:keydown.enter="$refresh">
                        <button class="btn btn-primary" type="button" wire:click="$refresh">جستجو</button>
                        @if(filled($search))
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('search','')">پاک</button>
                        @endif
                    </div>
                    {{-- اسپینر هنگام تایپ --}}

                </div>

                {{-- پاک کردن جستجو --}}
                @if(filled($search))
                    <button class="btn btn-outline-secondary btn-sm" type="button" title="پاک کردن"
                            wire:click="$set('search','')">
                        <i class="bi bi-x-lg"></i>
                    </button>
                @endif

                {{-- صفحه/تعداد در هر صفحه --}}
                <select class="form-select form-select-sm ms-2" style="width:110px" wire:model.live="perPage">
                    @foreach([10,15,20,30,50] as $n)
                        <option value="{{ $n }}">{{ $n }} / صفحه</option>
                    @endforeach
                </select>
            </div>
        </div>
    <div class="list-group">
        @forelse($rows as $item)
            @php
                $ts = $item->created_at;
                $timeText = optional($ts)->diffForHumans(null, true);

                $jid = $item->chat_id;
                if (!$jid){
                    continue;
                }

                $initials = preg_replace('/[^0-9]/', '', $jid);
                $initials = $initials ? substr($initials, -4) : 'NA';

                $type = $item->type;
                $typeIcon = match($type) {
                    'text'=>'bi-chat-left-text','image'=>'bi-image','video'=>'bi-camera-video',
                    'audio'=>'bi-mic','document'=>'bi-file-earmark','sticker'=>'bi-emoji-smile',
                    default=>'bi-chat',
                };
                $hasMedia = $item->has_media;
            @endphp

                <a href="{{ route('admin.whatsapp.thread', ['chat' => $jid]) }}"
               class="list-group-item list-group-item-action chat-item"
               wire:navigate>
                <div class="d-flex gap-3">
                    <div class="avatar flex-shrink-0">{{ $initials }}</div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="wh-name">
                                {{ $jid }}
                                <span class="badge rounded-pill type-pill ms-2">
                                    <i class="bi {{ $typeIcon }}"></i>
                                    {{ ucfirst($type) }}
                                </span>
                                @if($hasMedia)
                                    <i class="bi bi-paperclip clip ms-2" title="دارای فایل"></i>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="wh-time">{{ $timeText }}</div>
                                @php $unread = (int)($item->unread_count ?? 0); @endphp
                                @if($unread > 0)
                                    <span class="badge rounded-pill badge-unread mt-1">{{ $unread }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-muted small mt-1 text-truncate-2">
                            @if($item->type === 'text' && $item->formatted_body)
                                {{ $item->formatted_body }}
                            @elseif($item->type === 'image')
                                عکس {{ $item->body ? '— '.$item->body : '' }}
                            @elseif($item->type === 'video')
                                ویدئو {{ $item->body ? '— '.$item->body : '' }}
                            @elseif($item->type === 'audio')
                                پیام صوتی
                            @elseif($item->type === 'document')
                                فایل {{ $item->body ? '— '.$item->body : '' }}
                            @else
                                {{ $item->body ?: 'پیام جدید' }}
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-5 text-muted">هیچ گفتگویی پیدا نشد.</div>
        @endforelse
    </div>

    <div class="mt-3">
        {{-- {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }} --}}
    </div>
</div>
