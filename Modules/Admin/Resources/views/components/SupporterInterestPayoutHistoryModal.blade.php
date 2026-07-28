<div class="modal fade" id="supporterPayoutHistory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            @if (isset($supporter))
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">سابقه تسویه ها با{{ $supporter->fullName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive table-striped mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">زمان انجام تسویه</th>
                                    <th scope="col">مبلغ تسویه</th>
                                    <th scope="col">موجودی بعد از تسویه</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($supporter->walletDebitTransactions()->isNotEmpty())
                                    @foreach ($supporter->walletDebitTransactions() as $walletT)
                                        <tr>
                                            <td>{{ $walletT->id }}</td>
                                            <td>{{ verta($walletT->created_at)->format('Y/m/d ساعت H:i') }}</td>
                                            <td>{{ number_format($walletT->amount) }}</td>
                                            <td>{{ number_format($walletT->balance_after_transaction) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-info">
                                                سابقه ای یافت نشد!
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">بیخیال</button>
            </div>
        </div>
    </div>
</div>
