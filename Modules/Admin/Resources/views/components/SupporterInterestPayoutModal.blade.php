<div class="modal fade" id="supporterPayoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            @if (isset($supporter))
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">تسویه حساب با {{ $supporter->fullName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <h5>
                            میزان سود موجود قابل برداشت : <span> {{number_format($supporter->wallet)}}</span>
                        </h5>
                    </div>
                    <div class="row mb-4">
                        <label for="payoutInput" class="col-12 form-label mb-2">میزان تسویه حساب</label>
                        <div class="col-md-12">
                            <input class="form-control" id="payoutInput" wire:model="form.payout.amount" type="number"
                                autocomplete="off">
                        </div>
                        @error('form.payout.amount')
                            <span style="color: rgb(236, 64, 64)" class="ms-4 mt-2">
                                <i class="fa fa-bell-o" aria-hidden="true"></i>
                                {{$message}}
                            </span>
                        @enderror
                    </div>
                </div>
            @endif
            <div class="modal-footer">
                <button type="button" class="btn btn-primary me-3" wire:click='modalPayout'>ذخیره و کاهش موجودی</button>
                <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">بیخیال</button>
            </div>
        </div>
    </div>
</div>
