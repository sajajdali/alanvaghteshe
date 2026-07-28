<!-- Modal -->
<div class="modal  fade" id=userLogModal tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            @if (!is_null($user))
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        سابقه تماس های: <strong>{{ $user->fullName }}</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        x
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                            <tr>
                                <th>زمان</th>
                                <th>بابت</th>
                                <th>توسط</th>
                                <th>توضیحات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($user->logFor->isNotEmpty())
                                @foreach ($user->logFor as $log)
                                    <tr>
                                        <td>{{ verta($log->created_at)->format('Y/m/d ساعت H:i') }}</td>
                                        <td>{{ $log->event->getName() }}</td>
                                        <td>{{ $log->loggedBy->fullName }}</td>
                                        <td class="break-anywhere">{!! nl2br($log->description)!!}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <span class="alert-inner--text">سابقه تماسی!</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            @endif
        </div>
    </div>
</div>
