<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش برنامه' : 'افزودن برنامه جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">اطلاعات بسته</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <label for="name">نام برنامه (الزامیءءء)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       wire:model="name" placeholder="نام">
                                @error('name')
                                <div id="validationuserName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <label for="validationuserApiUrl">آدرس خرید  (نسخه دلاری)</label>
                                <input type="text" class="form-control @error('apiUrl') is-invalid @enderror"
                                       id="name"
                                       dir="ltr"
                                       wire:model="apiUrl" placeholder="api url">
                                @error('api_url')
                                <div id="validationuserApiUrl"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <p>نوع برنامه:</p>
                                @foreach(\Modules\Package\Enum\PackageTypeEnum::cases() as $typeEnum)
                                    <label class="rdiobox" for="type-{{ $typeEnum->value }}">
                                        <input
                                            name="gender"
                                            value="{{ $typeEnum->value }}"
                                            type="radio"
                                            wire:model="type"
                                            class="radio-primary"
                                            id="type-{{ $typeEnum->value }}">
                                        <span>{{ $typeEnum->getName() }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <label for="days">مدت برنامه</label>
                                <div>
                                    <select class="form-control form-select"
                                            wire:model="days"
                                            id="days">
                                        <option value="7">یک هفته</option>
                                        <option value="14">دو هفته</option>
                                        <option value="21">21 روزه</option>
                                        <option value="30">یک ماهه</option>
                                        <option value="90">سه ماهه</option>
                                        <option value="180">شش ماهه</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <label for="price">قیمت به ریالء (الزامیءء)</label>
                                <div class="input-group mb-3"><span
                                        class="input-group-text bg-primary-transparent text-primary"
                                        id="basic-price-postfix">ریالءء</span>
                                    <input aria-describedby="قیمت"
                                           wire:model.live="price"
                                           id="basic-price" aria-label="قیمت"
                                           class="form-control @error('priceValue') is-invalid @enderror"
                                           placeholder="قیمت"
                                           type="text">
                                </div>
                                @error('priceValue')
                                <div id="validationuserPrice"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="alert alert-info col-12">
                                با صفر قرار دادن قیمت با تخفیف، برنامه بدون تخفیف محاسبه می شود.
                            </div>
                            <div class="col-12 mb-3">
                                <label for="specialPrice-price">قیمت با تخفیف به ریالء</label>
                                <div class="input-group mb-3"><span
                                        class="input-group-text bg-danger-transparent text-danger"
                                        id="basic-price-postfix">ریالءء</span>
                                    <input aria-describedby="قیمت ویژه"
                                           wire:model.live="specialPrice"
                                           id="specialPrice-price" aria-label="قیمت ویژه"
                                           class="form-control @error('specialPrice') is-invalid @enderror"
                                           placeholder="قیمت ویژه"
                                           type="text">
                                </div>
                                @error('specialPrice')
                                <div id="validationuserSpecialPrice"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="col-12 mb-3">
                                <label for="supportPrice-price">قیمت پشتیبانی اختصاصی</label>
                                <div class="input-group mb-3"><span
                                        class="input-group-text bg-danger-transparent text-danger"
                                        id="basic-price-postfix">ریالءء</span>
                                    <input aria-describedby="قیمت ویژه"
                                           wire:model.live="supportPrice"
                                           id="supportPrice-price" aria-label="قیمت ویژه"
                                           class="form-control @error('supportPrice') is-invalid @enderror"
                                           placeholder="قیمت ویژه"
                                           type="text">
                                </div>
                                @error('supportPrice')
                                <div id="validationForSupportPrice"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <p>وضعیت برنامه:</p>
                                <label class="rdiobox" for="status-1">
                                    <input
                                        name="status"
                                        value="1"
                                        type="radio"
                                        wire:model="isActive"
                                        class="radio-warning"
                                        id="status-1">
                                    <span>فعال</span>
                                </label>
                                <label class="rdiobox" for="status-0">
                                    <input
                                        name="status"
                                        value="0"
                                        type="radio"
                                        wire:model="isActive"
                                        class="radio-warning"
                                        id="status-0">
                                    <span>غیرفعال</span>
                                </label>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a
                        wire:loading.class="btn btn-light btn-loading"
                        wire:loading.class.remove="btn-success"
                        wire:click="submit()"
                        class="btn btn-success">
                        {{ $isEdited ? 'ویرایش برنامه' : 'افزودن برنامه' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
