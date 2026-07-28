<?php

namespace Modules\Package\Livewire\Admin;

use Livewire\Component;
use Modules\Package\Entities\Package;
use Modules\Package\Enum\PackageTypeEnum;

class PackageUpdateOrCreate extends Component
{
    public ?Package $package = null;

    public string $name, $price, $specialPrice , $supportPrice , $apiUrl;

    public int $type, $isActive, $days;

    public $isEdited = false;

    public int $priceValue = 0;

    protected $rules = [
        'name' => 'required|string',
        'price' => 'required',
        'specialPrice' => 'nullable',
        'days' => 'required|numeric',
        'type' => 'required|numeric',
        'isActive' => 'required|boolean',
        'priceValue' => 'numeric|min:1000',
//        'supportPrice' => 'numeric|min:1000',
    ];

    protected $messages = [
        'name.required' => 'نام بسته را وارد کنید',
        'priceValue.required' => 'قیمت بسته را وارد کنید',
        'priceValue.min' => 'قیمت بسته نمیتواند کمتر از ۱۰۰۰ ریال باشد',
        'days.required' => 'تعداد روزهای بسته را وارد کنید',
        'type.required' => 'نوع بسته را وارد کنید',
        'supportPrice.numeric' => 'مبلغ پشتیبان باید به عدد باشد',
        'supportPrice.min' => 'مبلغ پشتیبان باید به بزگتر از ۱۰۰۰ ریال باشد',
        'isActive.required' => 'وضعیت بسته را وارد کنید',
    ];

    public function mount()
    {
        $package = request()->route('package');
        if ($package instanceof Package) {
            $this->authorize('update', $package);
            $this->package = $package;
            $this->name = $this->package->name ?? '';
            $this->price = number_format($this->package->price) ?? '';
            $this->specialPrice = number_format($this->package->special_price) ?? '';
            $this->supportPrice = number_format($this->package->support_price) ?? '';
            $this->days = $this->package->days ?? '';
            $this->type = $this->package->type->value ?? '';
            $this->isActive = $this->package->is_active ?? 0;
            $this->priceValue = (int)$this->package->price ?? 0;
            $this->apiUrl = isset($this->package->detail['api_url']) ? $this->package->detail['api_url'] : '';

            $this->isEdited = true;
        } else {
            $this->type = PackageTypeEnum::DIET->value;
            $this->days = 30;
            $this->price = 0;
            $this->specialPrice = 0;
            $this->supportPrice = 0;
            $this->priceValue = 0;
            $this->isActive = true;
        }
    }

    public function updatedPrice(): void
    {
        //replace persian numbers with english numbers
        $price = $this->price;
        $price = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], range(0, 9), $price);
        //replace arabic numbers with english numbers
        $price = str_replace(['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'], range(0, 9), $price);
        //remove not numeric characters
        $price = preg_replace('/[^0-9]/', '', $price);
        //make price to integer and number_format it
        $this->priceValue = (int)$price;
        $price = number_format((int)$price);

        $this->price = $price;
    }

    public function updatedSupportPrice(): void
    {
        //replace persian numbers with english numbers
        $supportPrice = $this->supportPrice;
        $supportPrice = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], range(0, 9), $supportPrice);
        //replace arabic numbers with english numbers
        $supportPrice = str_replace(['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'], range(0, 9), $supportPrice);
        //remove not numeric characters
        $supportPrice = preg_replace('/[^0-9]/', '', $supportPrice);
        //make price to integer and number_format it
        $this->priceValue = (int)$supportPrice;
        $supportPrice = number_format((int)$supportPrice);

        $this->supportPrice = $supportPrice;
    }

    public function updatedSpecialPrice(): void
    {
        //replace persian numbers with english numbers
        $specialPrice = $this->specialPrice;
        $specialPrice = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], range(0, 9), $specialPrice);
        //replace arabic numbers with english numbers
        $specialPrice = str_replace(['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'], range(0, 9), $specialPrice);
        //remove not numeric characters
        $specialPrice = preg_replace('/[^0-9]/', '', $specialPrice);
        //make price to integer and number_format it
        $specialPrice = number_format((int)$specialPrice);

        $this->specialPrice = $specialPrice;
    }

    public function submit()
    {
        $this->validate();
        if ($this->isEdited) {
            $detail = $this->package->detail;
            if (isset($this->apiUrl) && $this->apiUrl != ""){
                $detail['api_url'] = $this->apiUrl;
            } else {
                if (array_key_exists('api_url', $detail)) {
                    unset($detail['api_url']);
                }
            }
            $this->package->update([
                'name' => $this->name,
                'price' => str_replace(',', '', $this->price),
                'support_price' => str_replace(',', '', $this->supportPrice),
                'special_price' => str_replace(',', '', $this->specialPrice),
                'days' => $this->days,
                'type' => $this->type,
                'is_active' => $this->isActive,
                'detail' => $detail,
            ]);
            $message = 'بسته با موفقیت ویرایش شد';
        } else {
            $detail = [
                'api_url' => $this->apiUrl,
            ];
            //get max priority
            $maxPriority = Package::max('priority');
            Package::create([
                'name' => $this->name,
                'price' => str_replace(',', '', $this->price),
                'special_price' => str_replace(',', '', $this->specialPrice),
                'support_price' => str_replace(',', '', $this->supportPrice),
                'days' => $this->days,
                'type' => $this->type,
                'is_active' => $this->isActive,
                'detail' => $detail,
                'priority' => $maxPriority + 10,
            ]);
            $message = 'بسته با موفقیت ایجاد شد';
        }

        return redirect()->route('admin.package.index')->with('success', $message);
    }

    public function render()
    {
        $title = (!is_null($this->package) && $this->package->exists) ? 'ویرایش بسته' : 'ایجاد بسته جدید';

        return view('package::livewire.admin.package-update-or-create')->title($title);
    }
}
