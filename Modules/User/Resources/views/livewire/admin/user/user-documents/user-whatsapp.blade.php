<div class="row mt-4" wire:init="initSessionGate">


    @if (! $hasSession)
        <div class="alert alert-warning my-3">
            دستگاهی تعریف نشده است.
        </div>
    @elseif (! $canSend)
        <div class="alert alert-warning my-3">
            دستگاه شما لاگین نیست. ابتدا دستگاه را لاگین کنید.
        </div>
    @else
        <div class="row px-5 mt-3 mb-5 col-12">
            <div class="col-12 d-flex justify-content-between">
                <h4 class="my-3">ارسال پیغام در واتس اپ</h4>
            </div>
            <div class="col-12 mt-3">
            <textarea rows="8" wire:model='whatsappMessage'
                      class="form-control" id="validationTextarea"
                      placeholder="متن پیغام را وارد کنید"></textarea>
                @error('whatsappMessage')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end mt-2" wire:click='sendWhatsappMessage'>
                <button class="btn btn-success my-1 text-white">
                    ارسال پیغام در واتس اپ کاربر
                </button>
            </div>
        </div>
    @endif
    <div class="table-responsive mb-3">
        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
            <thead>
            <tr>
                <th>متن</th>
                <th>زمان</th>
                <th>گیرنده</th>
                <th>وضعیت</th>
            </tr>
            </thead>
            <tbody>
            @if ($messages->isNotEmpty())
                @foreach ($messages as $m)
                    @php
                        // 0: queued, 1: sent-to-server, 2: delivered, 3: read
                        if ($m->viewed || $m->read_at || $m->ack >= 3) { $status='دیده‌شده'; $badge='bg-success'; }
                        elseif ($m->ack >= 2) { $status='تحویل‌شده'; $badge='bg-primary'; }
                        elseif ($m->ack >= 1) { $status='ارسال به سرور'; $badge='bg-info'; }
                        else { $status='در صف/در حال ارسال'; $badge='bg-warning text-dark'; }
                    @endphp
                    <tr>
                        <td class="break-anywhere text-start">{!! nl2br(e($m->body)) !!}</td>
                        <td>{{ isset($m->created_at) ? verta($m->created_at)->format('Y/m/d ساعت H:i') : '-' }}</td>
                        <td dir="ltr">{{ $m->chat_id }}</td>
                        <td><span class="badge {{ $badge }}">{{ $status }}</span></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <span class="alert-inner--text">پیامی یافت نشد!</span>
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    {{-- صفحه‌بندی مستقل این جدول --}}
    <div class="d-flex justify-content-center">
        {{ $messages->onEachSide(1)->links() }}
    </div>
</div>

@push('scripts')
    <script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script>
        window.addEventListener('swal', function (e) {
            const d = e.detail || {};
            swal({
                title: d.title || '',
                text: d.text || '',
                icon: d.icon || 'info',
                button: 'باشه'
            });
        });
    </script>
@endpush
