<div>
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">گفت و گو</h1>
        </div>
    </div>
    <!-- PAGE-HEADER END -->
    @if($chats->isNotEmpty())
        <!-- Row -->
        <div class="row row-deck">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                <div class="card  overflow-scroll">
                    <div class="main-content-app pt-0">
                        <div class="main-content-left main-content-left-chat">
                            <!-- main-chat-header -->
                            <div class="tab-content main-chat-list flex-2">
                                <div class="tab-pane active" id="ChatList">
                                    <div class="main-chat-list tab-pane">
                                        @foreach($chats as $chatItem)
                                            <a class="cursor-pointer media @if($chatItem->id == $chatId) selected @else new @endif @if($loop->first) border-top-0 @endif @if($loop->last) border-bottom-0 @endif @if($chatItem->status->value == \Modules\Chat\Enum\ChatStatusEnum::ANSWERED->value) answered-chat @endif"
                                               wire:click="selectChatRoom({{ $chatItem->id }})">
                                                <div class="main-img-user online">
                                                    <img alt="{{ $chatItem->user?->full_name }}"
                                                         src="{{ $chatItem->user?->avatar }}">
                                                    @if($chatItem->status->value == \Modules\Chat\Enum\ChatStatusEnum::USER_SEND_QUESTION->value)
                                                        <span>new</span>
                                                    @endif
                                                </div>
                                                <div class="media-body">
                                                    <div class="media-contact-name">
                                                        <span>{{ $chatItem->user?->full_name }}</span>
                                                        @if($chatItem)
                                                            <span>{{ verta($chatItem->chatDetails->last()->created_at)->format("Y/m/d ساعت H:i:s") }}</span>
                                                            - {{$chatItem->id}}
                                                        @endif
                                                    </div>
                                                    <p> {{ $chatItem->latest_message_excerpt }} </p>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <!-- main-chat-list -->
                                </div>
                            </div>
                            <!-- main-chat-list -->
                        </div>
                    </div>
                </div>
            </div>
            @if($chatId != 0)
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8">
                    <div class="card">
                        <div class="main-content-app pt-0">
                            <div class="main-content-body main-content-body-chat h-100">
                                <div class="main-chat-header pt-3 d-block d-sm-flex">
                                    <div class="main-img-user online"><img alt="{{ $this->chat?->user?->full_name }}"
                                                                           src="{{ $this->chat?->user?->avatar }}">
                                    </div>
                                    <div class="main-chat-msg-name mt-2">
                                        @if( $this->chat?->user)
                                        <p class="mb-0"><a target="_blank" href="{{route('admin.user.document' , $this->chat?->user)}}">{{ $this->chat?->user?->full_name }}</a></p>
                                            <a href="{{route('admin.user.document' , $this->chat?->user)}}"><span class="dot-label bg-info"></span><small class="me-3">{{$this->chat?->user->mobile}}</small></a>
                                        @endif
                                    </div>
                                    <nav class="nav">
                                        <div>
                                            <div class="input-group"></div>
                                        </div>
                                    </nav>
                                </div>
                                <!-- main-chat-header -->
                                <div class="main-chat-body flex-2" id="ChatBody">
                                    @if($this->chatList?->isNotEmpty())
                                        <div class="content-inner">
                                            @foreach($this->chatList as $date => $chatItems)
                                                <label
                                                    class="main-chat-time"><span>{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</span></label>
                                                @foreach($chatItems as $chatMessage)
                                                    @if($chatMessage->user_id != $this->chat?->user_id)
                                                        <div class="media chat-left">
                                                            <div class="main-img-user online">
                                                                <img alt="avatar"
                                                                     src="{{ $chatMessage->user?->avatar }}">
                                                            </div>
                                                            <div class="media-body">
                                                                @if($chatMessage->type->is(\Modules\Chat\Enum\ChatDetailTypeEnum::MESSAGE))
                                                                    <div class="main-msg-wrapper">
                                                                        {{ $chatMessage->content }}
                                                                    </div>
                                                                @else
                                                                    <div class="main-msg-wrapper">
                                                                        <a class="text-dark"
                                                                           href="{{ $chatMessage->content }}">
                                                                    <span
                                                                        class="fs-13 mt-1"> دانلود فایل </span> <i
                                                                                class="fe fe-download mt-3 ms-4 text-muted pe-2"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <span>{{ $chatMessage->created_at->format('H:i') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="media flex-row-reverse chat-right">

                                                            <div class="main-img-user online">
                                                                <img alt="avatar"
                                                                     src="{{ $chatMessage->user?->avatar }}">
                                                            </div>
                                                            <div class="media-body">
                                                                @if($chatMessage->type->is(\Modules\Chat\Enum\ChatDetailTypeEnum::MESSAGE))
                                                                    <div class="main-msg-wrapper">
                                                                        {{ $chatMessage->content }}
                                                                    </div>
                                                                @else
                                                                    <div class="main-msg-wrapper">
                                                                        <a class="text-dark"
                                                                           href="{{ $chatMessage->content }}">
                                                                    <span
                                                                        class="fs-13 mt-1"> دانلود فایل </span> <i
                                                                                class="fe fe-download mt-3 ms-4 text-muted pe-2"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <span>{{ $chatMessage->created_at->format('H:i') }}</span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            هنوز پیامی ارسال نشده است.
                                        </div>
                                    @endif
                                </div>
                                <div class="main-chat-footer pt-5 pb-5">
                                    <input class="form-control" placeholder="متن پیام شما..." type="text"
                                           wire:model="chatMessage">
{{--                                    <a class="nav-link" href="javascript:void(0)"><i class="fe fe-paperclip"></i></a>--}}
                                    <button type="button" wire:click="sendMessage"
                                            wire:loading.class="btn btn-light btn-loading"
                                            wire:loading.class.remove="btn-primary"
                                            class="btn btn-icon  btn-primary brround">
                                        <i class="fa fa-paper-plane-o"></i>
                                    </button>
                                    <nav class="nav">
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8">
                    <div class="card">
                        <div class="alert alert-info">
                            برای مشاهده گفت و گو از لیست یکی از گفت و گو ها را انتخاب کنید.
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- End Row -->
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    هنوز چتی آغاز نشده است.
                </div>
            </div>
        </div>
    @endif
</div>
@push('scripts')
    <style>
        @media (min-width: 992px) {
            .main-chat-list {
                max-height: calc(100vh - 200px);
                overflow-y: auto;
            }
        }
    </style>
    <!--- TABS JS -->
    <script src="{{admin_asset('js/pusher.js')}}"></script>
    <script src="{{admin_asset('plugins/tabs/jquery.multipurpose_tabcontent.js')}}"></script>
    <script src="{{admin_asset('plugins/tabs/tab-content.js')}}"></script>
    <script src="{{admin_asset('js/chat.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>
    <script>
        Livewire.on('error', param => {
            swal({
                title: "خطا!",
                text: "" + param.message,
                type: "error",
                showCancelButton: true,
                allowOutsideClick: true,
                showConfirmButton: false,
                cancelButtonText: "متوجه شدم",
                closeOnConfirm: false
            });
        });
        Livewire.on('chatRoomSelected', () => {
            //add delay to scroll to bottom
            setTimeout(function () {
                var ps5 = new PerfectScrollbar('#ChatBody', {
                    useBothWheelAxes: true,
                    suppressScrollX: true
                });
            }, 1000);
        });
    </script>
    <style>
        .answered-chat {
            opacity: 0.6;
            background-color: #e4e4e4 !important;
            color: #888 !important;
        }

        .answered-chat .media-body p {
            color: #888 !important;
        }
    </style>
@endpush
